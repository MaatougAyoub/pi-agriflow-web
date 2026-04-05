<?php

namespace App\Controller;

use App\Entity\Parcelle;
use App\Repository\CultureRepository;
use App\Repository\ParcelleRepository;
use App\Service\ParcelleCultureSurfaceService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/parcelles', name: 'app_admin_parcelle_')]
#[IsGranted('ROLE_ADMIN')]
final class AdminParcelleController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(Request $request, ParcelleRepository $parcelleRepository): Response
    {
        $filters = [
            'search' => (string) $request->query->get('search', ''),
            'type_terre' => (string) $request->query->get('type_terre', ''),
            'sort' => (string) $request->query->get('sort', 'date_creation'),
            'direction' => (string) $request->query->get('direction', 'desc'),
        ];

        return $this->render('admin/parcelle/index.html.twig', [
            'parcelles' => $parcelleRepository->findFilteredForAdmin($filters),
            'filters' => $filters,
            'type_terre_choices' => $parcelleRepository->findTypeTerreChoicesForAdmin(),
            'sort_choices' => [
                'nom' => 'Nom',
                'superficie' => 'Superficie',
                'date_creation' => 'Date de creation',
            ],
        ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(
        int $id,
        ParcelleRepository $parcelleRepository,
        CultureRepository $cultureRepository,
        ParcelleCultureSurfaceService $surfaceService,
    ): Response {
        $parcelle = $this->findParcelle($id, $parcelleRepository);
        $cultures = $cultureRepository->findByParcelleIdForAdmin($id);

        return $this->render('admin/parcelle/show.html.twig', [
            'parcelle' => $parcelle,
            'cultures' => $cultures,
            'available_surface' => $surfaceService->getAvailableSurfaceForParcelle($parcelle),
        ]);
    }

    #[Route('/{id}/supprimer', name: 'delete', methods: ['POST'])]
    public function delete(
        int $id,
        Request $request,
        ParcelleRepository $parcelleRepository,
        CultureRepository $cultureRepository,
        EntityManagerInterface $entityManager,
    ): Response {
        $parcelle = $this->findParcelle($id, $parcelleRepository);
        $cultures = $cultureRepository->findByParcelleIdForAdmin($id);

        if ($this->isCsrfTokenValid('admin_delete_parcelle_'.$parcelle->getId(), (string) $request->request->get('_token'))) {
            $confirmCascadeDelete = '1' === $request->request->get('confirm_cascade_delete');

            if ([] !== $cultures && !$confirmCascadeDelete) {
                return $this->render('admin/parcelle/delete_confirm.html.twig', [
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

        return $this->redirectToRoute('app_admin_parcelle_index');
    }

    private function findParcelle(int $id, ParcelleRepository $parcelleRepository): Parcelle
    {
        $parcelle = $parcelleRepository->find($id);

        if (!$parcelle instanceof Parcelle) {
            throw $this->createNotFoundException('Parcelle introuvable.');
        }

        return $parcelle;
    }
}
