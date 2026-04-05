<?php

namespace App\Controller;

use App\Entity\Parcelle;
use App\Form\ParcelleType;
use App\Repository\CultureRepository;
use App\Repository\ParcelleRepository;
use App\Service\CurrentAgriculteurProvider;
use App\Service\ParcelleCultureSurfaceService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/mes-parcelles', name: 'app_parcelle_')]
final class ParcelleController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(
        Request $request,
        CurrentAgriculteurProvider $currentAgriculteurProvider,
        ParcelleRepository $parcelleRepository,
        CultureRepository $cultureRepository,
        ParcelleCultureSurfaceService $surfaceService,
    ): Response {
        $agriculteurId = $currentAgriculteurProvider->getCurrentTestUserId();
        $filters = [
            'search' => (string) $request->query->get('search', ''),
            'type_terre' => (string) $request->query->get('type_terre', ''),
            'sort' => (string) $request->query->get('sort', 'date_creation'),
            'direction' => (string) $request->query->get('direction', 'desc'),
        ];
        $parcelles = $parcelleRepository->findFilteredForAgriculteur($agriculteurId, $filters);
        $availableSurfaceMap = $surfaceService->buildAvailableSurfaceMap($parcelles);
        $cultureCountByParcelle = [];

        foreach ($parcelles as $parcelle) {
            $parcelleId = $parcelle->getId();

            if (null === $parcelleId) {
                continue;
            }

            $cultureCountByParcelle[$parcelleId] = count(
                $cultureRepository->findByParcelleIdAndProprietaireId($parcelleId, $agriculteurId)
            );
        }

        return $this->render('parcelle/index.html.twig', [
            'parcelles' => $parcelles,
            'available_surface_map' => $availableSurfaceMap,
            'culture_count_by_parcelle' => $cultureCountByParcelle,
            'current_test_user_id' => $agriculteurId,
            'filters' => $filters,
            'type_terre_choices' => $parcelleRepository->findTypeTerreChoicesForAgriculteur($agriculteurId),
            'sort_choices' => [
                'nom' => 'Nom',
                'superficie' => 'Superficie',
                'date_creation' => 'Date de creation',
            ],
        ]);
    }

    #[Route('/ajouter', name: 'new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        CurrentAgriculteurProvider $currentAgriculteurProvider,
        EntityManagerInterface $entityManager,
    ): Response {
        $parcelle = new Parcelle();
        $form = $this->createForm(ParcelleType::class, $parcelle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $parcelle->setAgriculteurId($currentAgriculteurProvider->getCurrentTestUserId());
            $parcelle->setDateCreation($parcelle->getDateCreation() ?? new \DateTime());

            $entityManager->persist($parcelle);
            $entityManager->flush();

            $this->addFlash('success', 'La parcelle a ete creee avec succes.');

            return $this->redirectToRoute('app_parcelle_show', ['id' => $parcelle->getId()]);
        }

        return $this->render('parcelle/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(
        int $id,
        CurrentAgriculteurProvider $currentAgriculteurProvider,
        ParcelleRepository $parcelleRepository,
        CultureRepository $cultureRepository,
        ParcelleCultureSurfaceService $surfaceService,
    ): Response {
        $agriculteurId = $currentAgriculteurProvider->getCurrentTestUserId();
        $parcelle = $this->findOwnedParcelle($id, $agriculteurId, $parcelleRepository);
        $cultures = $cultureRepository->findByParcelleIdAndProprietaireId($id, $agriculteurId);

        return $this->render('parcelle/show.html.twig', [
            'parcelle' => $parcelle,
            'cultures' => $cultures,
            'available_surface' => $surfaceService->getAvailableSurfaceForParcelle($parcelle),
        ]);
    }

    #[Route('/{id}/modifier', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(
        int $id,
        Request $request,
        CurrentAgriculteurProvider $currentAgriculteurProvider,
        ParcelleRepository $parcelleRepository,
        EntityManagerInterface $entityManager,
    ): Response {
        $parcelle = $this->findOwnedParcelle($id, $currentAgriculteurProvider->getCurrentTestUserId(), $parcelleRepository);
        $form = $this->createForm(ParcelleType::class, $parcelle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'La parcelle a ete modifiee avec succes.');

            return $this->redirectToRoute('app_parcelle_show', ['id' => $parcelle->getId()]);
        }

        return $this->render('parcelle/edit.html.twig', [
            'parcelle' => $parcelle,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/supprimer', name: 'delete', methods: ['POST'])]
    public function delete(
        int $id,
        Request $request,
        CurrentAgriculteurProvider $currentAgriculteurProvider,
        ParcelleRepository $parcelleRepository,
        CultureRepository $cultureRepository,
        EntityManagerInterface $entityManager,
    ): Response {
        $agriculteurId = $currentAgriculteurProvider->getCurrentTestUserId();
        $parcelle = $this->findOwnedParcelle($id, $agriculteurId, $parcelleRepository);
        $cultures = $cultureRepository->findByParcelleIdAndProprietaireId($id, $agriculteurId);

        if ($this->isCsrfTokenValid('delete_parcelle_'.$parcelle->getId(), (string) $request->request->get('_token'))) {
            $confirmCascadeDelete = '1' === $request->request->get('confirm_cascade_delete');

            if ([] !== $cultures && !$confirmCascadeDelete) {
                return $this->render('parcelle/delete_confirm.html.twig', [
                    'parcelle' => $parcelle,
                    'cultures' => $cultures,
                ]);
            }

            foreach ($cultures as $culture) {
                $entityManager->remove($culture);
            }

            $entityManager->remove($parcelle);
            $entityManager->flush();
            $this->addFlash(
                'success',
                [] !== $cultures
                    ? 'La parcelle et toutes ses cultures liees ont ete supprimees.'
                    : 'La parcelle a ete supprimee.'
            );
        }

        return $this->redirectToRoute('app_parcelle_index');
    }

    private function findOwnedParcelle(int $id, int $agriculteurId, ParcelleRepository $parcelleRepository): Parcelle
    {
        $parcelle = $parcelleRepository->findOneForAgriculteur($id, $agriculteurId);

        if (!$parcelle instanceof Parcelle) {
            throw $this->createNotFoundException('Parcelle introuvable.');
        }

        return $parcelle;
    }
}
