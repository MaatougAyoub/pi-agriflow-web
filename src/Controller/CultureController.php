<?php

namespace App\Controller;

use App\Entity\Culture;
use App\Entity\Parcelle;
use App\Entity\Utilisateur;
use App\Enum\Role;
use App\Form\CultureType;
use App\Repository\CultureRepository;
use App\Repository\ParcelleRepository;
use App\Service\ParcelleCultureSurfaceService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/mes-cultures', name: 'app_culture_')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
final class CultureController extends AbstractController
{
    private const AGRICULTEUR_ONLY_MESSAGE = "Cette partie n'est accessible qu'aux utilisateurs de type AGRICULTEUR.";

    #[Route('', name: 'index', methods: ['GET'])]
    public function index(
        Request $request,
        CultureRepository $cultureRepository,
        ParcelleRepository $parcelleRepository,
        ParcelleCultureSurfaceService $surfaceService,
    ): Response {
        if ($accessResponse = $this->redirectUnlessAgriculteur()) {
            return $accessResponse;
        }

        $agriculteurId = $this->getAuthenticatedUserId();
        $filters = [
            'search' => (string) $request->query->get('search', ''),
            'type_culture' => (string) $request->query->get('type_culture', ''),
            'parcelle_id' => (string) $request->query->get('parcelle_id', ''),
            'etat' => (string) $request->query->get('etat', ''),
            'sort' => (string) $request->query->get('sort', 'date_creation'),
            'direction' => (string) $request->query->get('direction', 'desc'),
        ];
        $cultures = $this->sortCulturesByEtatPriority(
            $cultureRepository->findFilteredForProprietaire($agriculteurId, $filters)
        );
        $parcelles = $parcelleRepository->findByAgriculteurId($agriculteurId);
        $parcellesById = $this->indexParcellesById($parcelles);

        return $this->render('culture/index.html.twig', [
            'cultures' => $cultures,
            'purchased_cultures' => $cultureRepository->findPurchasedByAcheteurId($agriculteurId),
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

    #[Route('/marketplace', name: 'marketplace', methods: ['GET'])]
    public function marketplace(
        CultureRepository $cultureRepository,
    ): Response {
        if ($accessResponse = $this->redirectUnlessAgriculteur()) {
            return $accessResponse;
        }

        $utilisateurId = $this->getAuthenticatedUserId();
        $cultures = $cultureRepository->findBy(
            ['etat' => Culture::ETAT_EN_VENTE],
            ['date_publication' => 'DESC', 'date_creation' => 'DESC', 'id' => 'DESC']
        );

        return $this->render('culture/marketplace.html.twig', [
            'marketplace_cultures' => $cultures,
            'current_user_id' => $utilisateurId,
        ]);
    }

    #[Route('/ajouter', name: 'new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        ParcelleRepository $parcelleRepository,
        ParcelleCultureSurfaceService $surfaceService,
        EntityManagerInterface $entityManager,
    ): Response {
        if ($accessResponse = $this->redirectUnlessAgriculteur()) {
            return $accessResponse;
        }

        $agriculteurId = $this->getAuthenticatedUserId();
        $parcelles = $parcelleRepository->findByAgriculteurId($agriculteurId);

        if ([] === $parcelles) {
            $this->addFlash('warning', 'Ajoutez d abord une parcelle avant de creer une culture.');

            return $this->redirectToRoute('app_parcelle_new');
        }

        $culture = (new Culture())
            ->setEtat(Culture::ETAT_EN_COURS)
            ->setAcheteur(null)
            ->setDatePublication(null)
            ->setDateVente(null);

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
        CultureRepository $cultureRepository,
        ParcelleRepository $parcelleRepository,
    ): Response {
        if ($accessResponse = $this->redirectUnlessAgriculteur()) {
            return $accessResponse;
        }

        $utilisateurId = $this->getAuthenticatedUserId();
        $culture = $this->findAccessibleCulture($id, $utilisateurId, $cultureRepository);
        $parcelle = $culture->getParcelleId() ? $parcelleRepository->find($culture->getParcelleId()) : null;
        $isOwnerViewer = $culture->isOwnedBy($utilisateurId);
        $isBuyerViewer = $culture->isBoughtBy($utilisateurId);

        return $this->render('culture/show.html.twig', [
            'culture' => $culture,
            'parcelle' => $parcelle,
            'is_owner_viewer' => $isOwnerViewer,
            'is_buyer_viewer' => $isBuyerViewer,
            'can_edit' => $isOwnerViewer && $culture->isModifiableOrSuppressible(),
            'can_delete' => $isOwnerViewer && $culture->isModifiableOrSuppressible(),
            'can_publish' => $culture->canBePublishedBy($utilisateurId),
            'can_cancel_publication' => $culture->canCancelPublicationBy($utilisateurId),
            'can_buy' => $culture->canBeBoughtBy($utilisateurId),
            'can_harvest' => $culture->canBeHarvestedBy($utilisateurId),
            'can_view_parcelle' => $isOwnerViewer && $parcelle instanceof Parcelle,
        ]);
    }

    #[Route('/{id}/modifier', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(
        int $id,
        Request $request,
        CultureRepository $cultureRepository,
        ParcelleRepository $parcelleRepository,
        ParcelleCultureSurfaceService $surfaceService,
        EntityManagerInterface $entityManager,
    ): Response {
        if ($accessResponse = $this->redirectUnlessAgriculteur()) {
            return $accessResponse;
        }

        $agriculteurId = $this->getAuthenticatedUserId();
        $culture = $this->findOwnedCulture($id, $agriculteurId, $cultureRepository);

        if (!$culture->isModifiableOrSuppressible()) {
            $this->addFlash('warning', 'Cette culture n est plus modifiable ni supprimable.');

            return $this->redirectToRoute('app_culture_show', ['id' => $culture->getId()]);
        }

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

    #[Route('/{id}/publier', name: 'publish', methods: ['POST'])]
    public function publish(
        int $id,
        Request $request,
        CultureRepository $cultureRepository,
        EntityManagerInterface $entityManager,
    ): Response {
        if ($accessResponse = $this->redirectUnlessAgriculteur()) {
            return $accessResponse;
        }

        $utilisateurId = $this->getAuthenticatedUserId();
        $culture = $this->findOwnedCulture($id, $utilisateurId, $cultureRepository);

        if ($this->isCsrfTokenValid('publish_culture_'.$culture->getId(), (string) $request->request->get('_token'))) {
            if (!$culture->canBePublishedBy($utilisateurId)) {
                $this->addFlash('warning', 'Cette culture ne peut pas etre publiee.');
            } else {
                $culture
                    ->setEtat(Culture::ETAT_EN_VENTE)
                    ->setDatePublication(new \DateTime());

                $entityManager->flush();
                $this->addFlash('success', 'La culture est maintenant publiee a la vente.');
            }
        }

        return $this->redirectToRoute('app_culture_show', ['id' => $culture->getId()]);
    }

    #[Route('/{id}/annuler-publication', name: 'cancel_publication', methods: ['POST'])]
    public function cancelPublication(
        int $id,
        Request $request,
        CultureRepository $cultureRepository,
        EntityManagerInterface $entityManager,
    ): Response {
        if ($accessResponse = $this->redirectUnlessAgriculteur()) {
            return $accessResponse;
        }

        $utilisateurId = $this->getAuthenticatedUserId();
        $culture = $this->findOwnedCulture($id, $utilisateurId, $cultureRepository);

        if ($this->isCsrfTokenValid('cancel_publication_culture_'.$culture->getId(), (string) $request->request->get('_token'))) {
            if (!$culture->canCancelPublicationBy($utilisateurId)) {
                $this->addFlash('warning', 'Cette publication ne peut pas etre annulee.');
            } else {
                $culture
                    ->setEtat(Culture::ETAT_EN_COURS)
                    ->setDatePublication(null);

                $entityManager->flush();
                $this->addFlash('success', 'La culture a ete retiree de la vente.');
            }
        }

        return $this->redirectToRoute('app_culture_show', ['id' => $culture->getId()]);
    }

    #[Route('/{id}/acheter', name: 'buy', methods: ['POST'])]
    public function buy(
        int $id,
        Request $request,
        CultureRepository $cultureRepository,
        EntityManagerInterface $entityManager,
    ): Response {
        if ($accessResponse = $this->redirectUnlessAgriculteur()) {
            return $accessResponse;
        }

        $utilisateur = $this->getAuthenticatedUser();
        $culture = $this->findAccessibleCulture($id, $utilisateur->getId(), $cultureRepository);

        if ($this->isCsrfTokenValid('buy_culture_'.$culture->getId(), (string) $request->request->get('_token'))) {
            if (!$culture->canBeBoughtBy($utilisateur->getId())) {
                $this->addFlash('warning', 'Cette culture n est plus disponible a l achat.');
            } else {
                $culture
                    ->setAcheteur($utilisateur)
                    ->setEtat(Culture::ETAT_VENDUE)
                    ->setDateVente(new \DateTime());

                $entityManager->flush();
                $this->addFlash('success', 'L achat de la culture a bien ete enregistre.');
            }
        }

        return $this->redirectToRoute('app_culture_show', ['id' => $culture->getId()]);
    }

    #[Route('/{id}/recolter', name: 'harvest', methods: ['POST'])]
    public function harvest(
        int $id,
        Request $request,
        CultureRepository $cultureRepository,
        EntityManagerInterface $entityManager,
    ): Response {
        if ($accessResponse = $this->redirectUnlessAgriculteur()) {
            return $accessResponse;
        }

        $utilisateurId = $this->getAuthenticatedUserId();
        $culture = $this->findAccessibleCulture($id, $utilisateurId, $cultureRepository);

        if ($this->isCsrfTokenValid('harvest_culture_'.$culture->getId(), (string) $request->request->get('_token'))) {
            if (!$culture->canBeHarvestedBy($utilisateurId)) {
                $this->addFlash('warning', 'Vous ne pouvez pas recolter cette culture.');
            } else {
                $culture
                    ->setEtat(Culture::ETAT_RECOLTEE)
                    ->setDateRecolte(new \DateTime());

                $entityManager->flush();
                $this->addFlash('success', 'La culture a ete recoltee avec succes.');
            }
        }

        return $this->redirectToRoute('app_culture_show', ['id' => $culture->getId()]);
    }

    #[Route('/{id}/supprimer', name: 'delete', methods: ['POST'])]
    public function delete(
        int $id,
        Request $request,
        CultureRepository $cultureRepository,
        EntityManagerInterface $entityManager,
    ): Response {
        if ($accessResponse = $this->redirectUnlessAgriculteur()) {
            return $accessResponse;
        }

        $culture = $this->findOwnedCulture($id, $this->getAuthenticatedUserId(), $cultureRepository);

        if (!$culture->isModifiableOrSuppressible()) {
            $this->addFlash('warning', 'Cette culture n est plus modifiable ni supprimable.');

            return $this->redirectToRoute('app_culture_show', ['id' => $culture->getId()]);
        }

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
        $culture->setEtat($culture->getEtat() ?: Culture::ETAT_EN_COURS);

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
            }

            $dateRecolte = $culture->getDateRecolte();
            if ($dateRecolte instanceof \DateTimeInterface) {
                $today = new \DateTimeImmutable('today');
                $selectedDate = \DateTimeImmutable::createFromInterface($dateRecolte)->setTime(0, 0);

                if ($selectedDate < $today) {
                    $form->get('dateRecolte')->addError(
                        new FormError('La date de recolte doit etre egale ou posterieure a aujourd hui.')
                    );
                }
            }

            $recolteEstime = $culture->getRecolteEstime();
            if (null !== $recolteEstime && (float) $recolteEstime < 0) {
                $form->get('recolteEstime')->addError(
                    new FormError('La recolte estimee doit etre superieure ou egale a 0.')
                );
            }

            if ($selectedParcelle instanceof Parcelle
                && !$surfaceService->canAssignSurface($selectedParcelle, $culture->getSuperficie(), $culture->getId())) {
                $form->get('superficie')->addError(
                    new FormError(sprintf(
                        'La superficie de la culture depasse la surface encore disponible pour cette parcelle. %s',
                        $surfaceService->createSurfaceExceededMessage($selectedParcelle, $culture->getId())
                    ))
                );
            }

            if ($form->isValid()) {
                $culture
                    ->setProprietaireId($agriculteurId)
                    ->setEtat($culture->getId() ? $culture->getEtat() : Culture::ETAT_EN_COURS)
                    ->setDateCreation($culture->getDateCreation() ?? new \DateTime());

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

    private function findCulture(int $id, CultureRepository $cultureRepository): Culture
    {
        $culture = $cultureRepository->find($id);

        if (!$culture instanceof Culture) {
            throw $this->createNotFoundException('Culture introuvable.');
        }

        return $culture;
    }

    private function findOwnedCulture(int $id, int $agriculteurId, CultureRepository $cultureRepository): Culture
    {
        $culture = $cultureRepository->findOneForProprietaire($id, $agriculteurId);

        if (!$culture instanceof Culture) {
            throw $this->createNotFoundException('Culture introuvable.');
        }

        return $culture;
    }

    private function findAccessibleCulture(int $id, int $utilisateurId, CultureRepository $cultureRepository): Culture
    {
        $culture = $this->findCulture($id, $cultureRepository);

        if (!$culture->canBeViewedBy($utilisateurId)) {
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
                '%s - %s (disponible: %.0f m2)',
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

    /**
     * @param Culture[] $cultures
     * @return Culture[]
     */
    private function sortCulturesByEtatPriority(array $cultures): array
    {
        $priorities = [
            Culture::ETAT_EN_VENTE => 0,
            Culture::ETAT_EN_COURS => 1,
            Culture::ETAT_VENDUE => 2,
            Culture::ETAT_RECOLTEE => 3,
        ];

        usort($cultures, static function (Culture $left, Culture $right) use ($priorities): int {
            $leftPriority = $priorities[$left->getEtat()] ?? 99;
            $rightPriority = $priorities[$right->getEtat()] ?? 99;

            if ($leftPriority !== $rightPriority) {
                return $leftPriority <=> $rightPriority;
            }

            $leftDate = $left->getDateCreation()?->getTimestamp() ?? 0;
            $rightDate = $right->getDateCreation()?->getTimestamp() ?? 0;

            if ($leftDate !== $rightDate) {
                return $rightDate <=> $leftDate;
            }

            return ($right->getId() ?? 0) <=> ($left->getId() ?? 0);
        });

        return $cultures;
    }

    private function redirectUnlessAgriculteur(): ?Response
    {
        if ($this->isGranted(Role::AGRICULTEUR->value)) {
            return null;
        }

        $this->addFlash('danger', self::AGRICULTEUR_ONLY_MESSAGE);

        return $this->redirectToRoute('site_home');
    }

    private function getAuthenticatedUser(): Utilisateur
    {
        $user = $this->getUser();

        if (!$user instanceof Utilisateur || null === $user->getId()) {
            throw $this->createAccessDeniedException('Utilisateur non connecte.');
        }

        return $user;
    }

    private function getAuthenticatedUserId(): int
    {
        return $this->getAuthenticatedUser()->getId();
    }
}
