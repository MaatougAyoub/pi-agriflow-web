<?php

namespace App\Controller;

use App\Entity\Culture;
use App\Entity\Parcelle;
use App\Form\CultureType;
use App\Repository\CultureRepository;
use App\Repository\ParcelleRepository;
use App\Service\CurrentAgriculteurProvider;
use App\Service\ParcelleCultureSurfaceService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/mes-cultures', name: 'app_culture_')]
final class CultureController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(
        Request $request,
        CurrentAgriculteurProvider $currentAgriculteurProvider,
        CultureRepository $cultureRepository,
        ParcelleRepository $parcelleRepository,
        ParcelleCultureSurfaceService $surfaceService,
       ): Response {
        $agriculteurId = $currentAgriculteurProvider->getCurrentTestUserId();
        $filters = [
            'search' => (string) $request->query->get('search', ''),
            'type_culture' => (string) $request->query->get('type_culture', ''),
            'parcelle_id' => (string) $request->query->get('parcelle_id', ''),
            'etat' => (string) $request->query->get('etat', ''),
               'sort' => (string) $request->query->get('sort', 'date_creation'),
            'direction' => (string) $request->query->get('direction', 'desc'),
        ];
        $cultures = $cultureRepository->findFilteredForProprietaire($agriculteurId, $filters);
        $parcelles = $parcelleRepository->findByAgriculteurId($agriculteurId);
        $parcellesById = $this->indexParcellesById($parcelles);

        return $this->render('culture/index.html.twig', [
            'cultures' => $cultures,
            'parcelles_by_id' => $parcellesById,
            'available_surface_map' => $surfaceService->buildAvailableSurfaceMap($parcelles),
            'filters' => $filters,
            'type_culture_choices' => $cultureRepository->findTypeCultureChoicesForProprietaire($agriculteurId),
            'etat_choices' => $cultureRepository->findEtatChoicesForProprietaire($agriculteurId),
            'parcelles' => $parcelles,
            'sort_choices' => [
                'nom' => 'Nom',
                'superficie' => 'Superficie',
                'recolte_estime' => 'Recolte estimee',
                'date_creation' => 'Date de creation',
            ],
        ]);
    }

    #[Route('/ajouter', name: 'new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        CurrentAgriculteurProvider $currentAgriculteurProvider,
        ParcelleRepository $parcelleRepository,
        ParcelleCultureSurfaceService $surfaceService,
        EntityManagerInterface $entityManager,
    ): Response {
        $agriculteurId = $currentAgriculteurProvider->getCurrentTestUserId();
        $parcelles = $parcelleRepository->findByAgriculteurId($agriculteurId);

        if ([] === $parcelles) {
            $this->addFlash('warning', 'Ajoutez d abord une parcelle avant de creer une culture.');

            return $this->redirectToRoute('app_parcelle_new');
        }

        $culture = new Culture();
        $preselectedParcelleId = $this->extractPreselectedParcelleId($request, $parcelles);

        if (null !== $preselectedParcelleId) {
            $culture->setParcelleId($preselectedParcelleId);
        }

        return $this->handleCultureForm(
            $request,
            $culture,
            $parcelles,
            $agriculteurId,
            $surfaceService,
            $entityManager,
            'culture/new.html.twig',
            'La culture a ete creee avec succes.'
        );
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(
        int $id,
        CurrentAgriculteurProvider $currentAgriculteurProvider,
        CultureRepository $cultureRepository,
        ParcelleRepository $parcelleRepository,
    ): Response {
        $agriculteurId = $currentAgriculteurProvider->getCurrentTestUserId();
        $culture = $this->findOwnedCulture($id, $agriculteurId, $cultureRepository);
        $parcelle = $culture->getParcelleId()
            ? $parcelleRepository->findOneForAgriculteur($culture->getParcelleId(), $agriculteurId)
            : null;

        return $this->render('culture/show.html.twig', [
            'culture' => $culture,
            'parcelle' => $parcelle,
        ]);
    }

    #[Route('/{id}/modifier', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(
        int $id,
        Request $request,
        CurrentAgriculteurProvider $currentAgriculteurProvider,
        CultureRepository $cultureRepository,
        ParcelleRepository $parcelleRepository,
        ParcelleCultureSurfaceService $surfaceService,
        EntityManagerInterface $entityManager,
    ): Response {
        $agriculteurId = $currentAgriculteurProvider->getCurrentTestUserId();
        $culture = $this->findOwnedCulture($id, $agriculteurId, $cultureRepository);
        $parcelles = $parcelleRepository->findByAgriculteurId($agriculteurId);

        return $this->handleCultureForm(
            $request,
            $culture,
            $parcelles,
            $agriculteurId,
            $surfaceService,
            $entityManager,
            'culture/edit.html.twig',
            'La culture a ete modifiee avec succes.'
        );
    }

    #[Route('/{id}/supprimer', name: 'delete', methods: ['POST'])]
    public function delete(
        int $id,
        Request $request,
        CurrentAgriculteurProvider $currentAgriculteurProvider,
        CultureRepository $cultureRepository,
        EntityManagerInterface $entityManager,
    ): Response {
        $culture = $this->findOwnedCulture($id, $currentAgriculteurProvider->getCurrentTestUserId(), $cultureRepository);

        if ($this->isCsrfTokenValid('delete_culture_'.$culture->getId(), (string) $request->request->get('_token'))) {
            $entityManager->remove($culture);
            $entityManager->flush();
            $this->addFlash('success', 'La culture a ete supprimee.');
        }

        return $this->redirectToRoute('app_culture_index');
    }

    /**
     * @param Parcelle[] $parcelles
     */
    private function handleCultureForm(
        Request $request,
        Culture $culture,
        array $parcelles,
        int $agriculteurId,
        ParcelleCultureSurfaceService $surfaceService,
        EntityManagerInterface $entityManager,
        string $template,
        string $successMessage,
    ): Response {
        $parcellesById = $this->indexParcellesById($parcelles);
        $availableSurfaceMap = $surfaceService->buildAvailableSurfaceMap($parcelles, $culture);
        $selectedParcelleId = $culture->getParcelleId();

        $form = $this->createForm(CultureType::class, $culture, [
            'parcelle_choices' => $this->buildParcelleChoices($parcelles, $availableSurfaceMap),
            'surface_by_parcelle' => $availableSurfaceMap,
            'selected_parcelle_id' => $selectedParcelleId,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $selectedParcelleId = $culture->getParcelleId();
            $selectedParcelle = $selectedParcelleId ? ($parcellesById[$selectedParcelleId] ?? null) : null;

            if (null === $selectedParcelle) {
                $form->get('parcelleId')->addError(new FormError('La parcelle selectionnee est invalide.'));
            } elseif (!$surfaceService->canAssignSurface($selectedParcelle, $culture->getSuperficie(), $culture->getId())) {
                $form->get('superficie')->addError(
                    new FormError($surfaceService->createSurfaceExceededMessage($selectedParcelle, $culture->getId()))
                );
            }

            if ($form->isValid()) {
                $culture->setProprietaireId($agriculteurId);
                $culture->setDateCreation($culture->getDateCreation() ?? new \DateTime());

                $entityManager->persist($culture);
                $entityManager->flush();

                $this->addFlash('success', $successMessage);

                return $this->redirectToRoute('app_culture_show', ['id' => $culture->getId()]);
            }
        }

        $selectedParcelle = $selectedParcelleId ? ($parcellesById[$selectedParcelleId] ?? null) : null;

        return $this->render($template, [
            'form' => $form->createView(),
            'culture' => $culture,
            'parcelles' => $parcelles,
            'available_surface_map' => $availableSurfaceMap,
            'selected_parcelle' => $selectedParcelle,
            'selected_available_surface' => $selectedParcelle instanceof Parcelle
                ? $surfaceService->getAvailableSurfaceForParcelle($selectedParcelle, $culture->getId())
                : null,
        ]);
    }

    private function findOwnedCulture(int $id, int $agriculteurId, CultureRepository $cultureRepository): Culture
    {
        $culture = $cultureRepository->findOneForProprietaire($id, $agriculteurId);

        if (!$culture instanceof Culture) {
            throw $this->createNotFoundException('Culture introuvable.');
        }

        return $culture;
    }

    /**
     * @param Parcelle[] $parcelles
     * @return array<int, Parcelle>
     */
    private function indexParcellesById(array $parcelles): array
    {
        $indexedParcelles = [];

        foreach ($parcelles as $parcelle) {
            $parcelleId = $parcelle->getId();

            if (null === $parcelleId) {
                continue;
            }

            $indexedParcelles[$parcelleId] = $parcelle;
        }

        return $indexedParcelles;
    }

    /**
     * @param Parcelle[] $parcelles
     * @param array<int, float> $availableSurfaceMap
     * @return array<string, int>
     */
    private function buildParcelleChoices(array $parcelles, array $availableSurfaceMap): array
    {
        $choices = [];

        foreach ($parcelles as $parcelle) {
            $parcelleId = $parcelle->getId();

            if (null === $parcelleId) {
                continue;
            }

            $label = sprintf(
                '%s - %s (disponible: %.0f m²)',
                $parcelle->getNom() ?: sprintf('Parcelle #%d', $parcelleId),
                $parcelle->getLocalisation() ?: 'Sans localisation',
                $availableSurfaceMap[$parcelleId] ?? 0
            );

            $choices[$label] = $parcelleId;
        }

        return $choices;
    }

    /**
     * @param Parcelle[] $parcelles
     */
 private function extractPreselectedParcelleId(Request $request, array $parcelles): ?int
    {
        $preselectedParcelleId = $request->query->getInt('parcelle');

        if (0 === $preselectedParcelleId) {
            return null;
        }

        foreach ($parcelles as $parcelle) {
            if ($parcelle->getId() === $preselectedParcelleId) {
                return $preselectedParcelleId;
            }
        }

        return null;
    }
}