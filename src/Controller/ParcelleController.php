<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Enum\Role;
use App\Entity\Parcelle;
use App\Form\ParcelleType;
use App\Repository\CultureRepository;
use App\Repository\ParcelleRepository;
use App\Service\ParcelRecommendationService;
use App\Service\ParcelleCultureSurfaceService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/mes-parcelles', name: 'app_parcelle_')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
final class ParcelleController extends AbstractController
{
    private const AGRICULTEUR_ONLY_MESSAGE = "Cette partie n'est accessible qu'aux utilisateurs de type AGRICULTEUR.";

    #[Route('', name: 'index', methods: ['GET'])]
    public function index(
        Request $request,
        ParcelleRepository $parcelleRepository,
        CultureRepository $cultureRepository,
        ParcelleCultureSurfaceService $surfaceService,
    ): Response {
        if ($accessResponse = $this->redirectUnlessAgriculteur()) {
            return $accessResponse;
        }

        $agriculteurId = $this->getAuthenticatedUserId();
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
        EntityManagerInterface $entityManager,
    ): Response {
        if ($accessResponse = $this->redirectUnlessAgriculteur()) {
            return $accessResponse;
        }

        $parcelle = new Parcelle();
        $form = $this->createForm(ParcelleType::class, $parcelle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $parcelle->setAgriculteurId($this->getAuthenticatedUserId());

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
        ParcelleRepository $parcelleRepository,
        CultureRepository $cultureRepository,
        ParcelleCultureSurfaceService $surfaceService,
    ): Response {
        if ($accessResponse = $this->redirectUnlessAgriculteur()) {
            return $accessResponse;
        }

        $agriculteurId = $this->getAuthenticatedUserId();
        $parcelle = $this->findOwnedParcelle($id, $agriculteurId, $parcelleRepository);
        $cultures = $cultureRepository->findByParcelleIdAndProprietaireId($id, $agriculteurId);

        return $this->render('parcelle/show.html.twig', [
            'parcelle' => $parcelle,
            'cultures' => $cultures,
            'available_surface' => $surfaceService->getAvailableSurfaceForParcelle($parcelle),
        ]);
    }

    #[Route('/{id}/recommendations', name: 'recommendations', methods: ['GET', 'POST'])]
    public function recommendations(
        int $id,
        Request $request,
        ParcelleRepository $parcelleRepository,
        ParcelRecommendationService $parcelRecommendationService,
        LoggerInterface $logger,
    ): JsonResponse {
        if ($accessResponse = $this->redirectUnlessAgriculteur()) {
            return new JsonResponse([
                'success' => false,
                'message' => self::AGRICULTEUR_ONLY_MESSAGE,
            ], Response::HTTP_FORBIDDEN);
        }

        $parcelle = $this->findOwnedParcelle($id, $this->getAuthenticatedUserId(), $parcelleRepository);

        if (!$this->isCsrfTokenValid('parcel_recommendations_'.$parcelle->getId(), (string) $request->request->get('_token'))) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Jeton CSRF invalide.',
            ], Response::HTTP_FORBIDDEN);
        }

        try {
            $recommendations = $parcelRecommendationService->getRecommendations(
                (string) ($parcelle->getTypeTerre() ?? ''),
                (float) ($parcelle->getSuperficie() ?? 0),
                (string) ($parcelle->getLocalisation() ?? '')
            );

            return new JsonResponse([
                'success' => true,
                'recommendations' => $recommendations,
            ]);
        } catch (\Throwable $exception) {
            $logger->error('Parcel recommendations request failed.', [
                'exception' => $exception,
                'message' => $exception->getMessage(),
            ]);

            $payload = [
                'success' => false,
                'error' => 'Generation des recommandations impossible.',
            ];

            if ((bool) $this->getParameter('kernel.debug')) {
                $payload['details'] = $exception->getMessage();
            }

            return new JsonResponse($payload, Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}/modifier', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(
        int $id,
        Request $request,
        ParcelleRepository $parcelleRepository,
        EntityManagerInterface $entityManager,
    ): Response {
        if ($accessResponse = $this->redirectUnlessAgriculteur()) {
            return $accessResponse;
        }

        $parcelle = $this->findOwnedParcelle($id, $this->getAuthenticatedUserId(), $parcelleRepository);
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
        ParcelleRepository $parcelleRepository,
        CultureRepository $cultureRepository,
        EntityManagerInterface $entityManager,
    ): Response {
        if ($accessResponse = $this->redirectUnlessAgriculteur()) {
            return $accessResponse;
        }

        $agriculteurId = $this->getAuthenticatedUserId();
        $parcelle = $this->findOwnedParcelle($id, $agriculteurId, $parcelleRepository);
        $cultures = $cultureRepository->findByParcelleIdAndProprietaireId($id, $agriculteurId);
        $blockingCultures = array_filter($cultures, static fn ($culture) => !$culture->isRecoltee());

        if ($this->isCsrfTokenValid('delete_parcelle_'.$parcelle->getId(), (string) $request->request->get('_token'))) {
            if ([] !== $blockingCultures) {
                $this->addFlash(
                    'danger',
                    'La suppression de cette parcelle est impossible tant qu elle contient des cultures en cours, en vente ou vendues.'
                );

                return $this->redirectToRoute('app_parcelle_show', ['id' => $parcelle->getId()]);
            }

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

    private function redirectUnlessAgriculteur(): ?Response
    {
        if ($this->isGranted(Role::AGRICULTEUR->value)) {
            return null;
        }

        $this->addFlash('danger', self::AGRICULTEUR_ONLY_MESSAGE);

        return $this->redirectToRoute('site_home');
    }

    private function getAuthenticatedUserId(): int
    {
        $user = $this->getUser();

        if (!$user instanceof Utilisateur || null === $user->getId()) {
            throw $this->createAccessDeniedException('Utilisateur non connecte.');
        }

        return $user->getId();
    }
}
