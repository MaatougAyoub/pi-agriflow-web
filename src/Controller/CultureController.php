<?php

namespace App\Controller;

use App\Entity\Culture;
use App\Entity\CultureHistory;
use App\Entity\Parcelle;
use App\Entity\Utilisateur;
use App\Enum\Role;
use App\Form\CultureType;
use App\Repository\CultureHistoryRepository;
use App\Repository\CultureRepository;
use App\Repository\ParcelleRepository;
use App\Repository\UtilisateurRepository;
use App\Service\CultureYieldEstimatorService;
use App\Service\ParcelleCultureSurfaceService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Snappy\Pdf;
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
        UtilisateurRepository $utilisateurRepository,
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
            'owner_names_by_id' => $this->buildUserDisplayNamesById($cultures, $utilisateurRepository),
        ]);
    }

    #[Route('/ajouter', name: 'new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        ParcelleRepository $parcelleRepository,
        CultureYieldEstimatorService $yieldEstimatorService,
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
            $yieldEstimatorService,
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
        CultureHistoryRepository $cultureHistoryRepository,
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
            'can_undo_harvest' => $this->canUndoHarvest($culture, $utilisateurId),
            'can_view_parcelle' => $isOwnerViewer && $parcelle instanceof Parcelle,
            'can_generate_contract' => $culture->hasAcheteur() && ($isOwnerViewer || $isBuyerViewer),
            'history_entries' => $cultureHistoryRepository->findByCultureOrderedDesc($culture),
        ]);
    }

    #[Route('/{id}/contrat-vente', name: 'sale_contract_pdf', methods: ['GET'])]
    public function saleContractPdf(
        int $id,
        CultureRepository $cultureRepository,
        ParcelleRepository $parcelleRepository,
        UtilisateurRepository $utilisateurRepository,
        Pdf $pdf,
    ): Response {
        if ($accessResponse = $this->redirectUnlessAgriculteur()) {
            return $accessResponse;
        }

        $utilisateurId = $this->getAuthenticatedUserId();
        $culture = $this->findAccessibleCulture($id, $utilisateurId, $cultureRepository);
        $isOwnerViewer = $culture->isOwnedBy($utilisateurId);
        $isBuyerViewer = $culture->isBoughtBy($utilisateurId);

        if (!$culture->hasAcheteur() || (!$isOwnerViewer && !$isBuyerViewer)) {
            throw $this->createAccessDeniedException('Ce contrat de vente n est pas accessible.');
        }

        $parcelle = $culture->getParcelleId() ? $parcelleRepository->find($culture->getParcelleId()) : null;
        $vendeur = $isOwnerViewer ? $this->getAuthenticatedUser() : $utilisateurRepository->find($culture->getProprietaireId());
        $acheteur = $culture->getAcheteur();

        if (!$vendeur instanceof Utilisateur || !$acheteur instanceof Utilisateur) {
            throw $this->createNotFoundException('Les informations du contrat sont incompletes.');
        }

        $html = $this->renderView('culture/sale_contract_pdf.html.twig', [
            'culture' => $culture,
            'parcelle' => $parcelle,
            'vendeur' => $vendeur,
            'acheteur' => $acheteur,
            'vendeur_signature_src' => $this->buildPdfSignatureSource($vendeur->getSignature()),
            'acheteur_signature_src' => $this->buildPdfSignatureSource($acheteur->getSignature()),
            'generation_date' => new \DateTimeImmutable(),
        ]);

        $output = $pdf->getOutputFromHtml($html, [
            'enable-local-file-access' => true,
        ]);

        $fileName = sprintf('contrat-vente-culture-%d.pdf', $culture->getId());

        return new Response($output, Response::HTTP_OK, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => sprintf('inline; filename="%s"', $fileName),
        ]);
    }

    #[Route('/{id}/modifier', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(
        int $id,
        Request $request,
        CultureRepository $cultureRepository,
        ParcelleRepository $parcelleRepository,
        CultureYieldEstimatorService $yieldEstimatorService,
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
            $yieldEstimatorService,
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
                $prixVente = $request->request->get('prix_vente');

                if (!is_numeric($prixVente) || (float) $prixVente <= 0) {
                    $this->addFlash('warning', 'Veuillez saisir un prix de vente valide avant de publier la culture.');
                    return $this->redirectToRoute('app_culture_show', ['id' => $culture->getId()]);
                }

                $culture
                    ->setPrixVente((float) $prixVente)
                    ->setEtat(Culture::ETAT_EN_VENTE)
                    ->setDatePublication(new \DateTime());

                $this->recordCultureHistory(
                    $entityManager,
                    $culture,
                    CultureHistory::ACTION_PUBLISHED,
                    sprintf('Mise en vente a %.2f TND.', (float) $prixVente),
                    $this->getAuthenticatedUser()
                );
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

                $this->recordCultureHistory(
                    $entityManager,
                    $culture,
                    CultureHistory::ACTION_PUBLICATION_CANCELLED,
                    'Retrait de la culture de la vente.',
                    $this->getAuthenticatedUser()
                );
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

                $this->recordCultureHistory(
                    $entityManager,
                    $culture,
                    CultureHistory::ACTION_PURCHASED,
                    'Achat de la culture.',
                    $utilisateur
                );
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

                $this->recordCultureHistory(
                    $entityManager,
                    $culture,
                    CultureHistory::ACTION_HARVESTED,
                    'Recolte de la culture.',
                    $this->getAuthenticatedUser()
                );
                $entityManager->flush();
                $this->addFlash('success', 'La culture a ete recoltee avec succes.');
            }
        }

        return $this->redirectToRoute('app_culture_show', ['id' => $culture->getId()]);
    }

    #[Route('/{id}/annuler-recolte', name: 'undo_harvest', methods: ['POST'])]
    public function undoHarvest(
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

        if ($this->isCsrfTokenValid('undo_harvest_culture_'.$culture->getId(), (string) $request->request->get('_token'))) {
            if (!$this->canUndoHarvest($culture, $utilisateurId)) {
                $this->addFlash('warning', 'Cette recolte ne peut pas etre annulee.');
            } else {
                $previousState = $this->determineStateAfterHarvestUndo($culture);

                $culture
                    ->setEtat($previousState)
                    ->setDateRecolte(null);

                $this->recordCultureHistory(
                    $entityManager,
                    $culture,
                    CultureHistory::ACTION_HARVEST_CANCELLED,
                    sprintf('Annulation de la recolte. Retour a l etat %s.', $previousState),
                    $this->getAuthenticatedUser()
                );
                $entityManager->flush();
                $this->addFlash('success', 'La recolte a ete annulee avec succes.');
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
        CultureYieldEstimatorService $yieldEstimatorService,
        ParcelleCultureSurfaceService $surfaceService,
        EntityManagerInterface $entityManager,
        string $template,
        string $successMessage,
    ): Response {
        $isNewCulture = null === $culture->getId();
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
                $today = new \DateTime('today');
                $selectedDate = \DateTime::createFromInterface($dateRecolte)->setTime(0, 0);

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
                    ->setProprietaire($entityManager->getReference(Utilisateur::class, $agriculteurId))
                    ->setEtat($culture->getId() ? $culture->getEtat() : Culture::ETAT_EN_COURS)
                    ->setRecolteEstime((string) $yieldEstimatorService->estimate($culture->getTypeCulture(), (float) $culture->getSuperficie()))
                    ->setDateCreation($culture->getDateCreation() ?? new \DateTime());

                $entityManager->persist($culture);
                $this->recordCultureHistory(
                    $entityManager,
                    $culture,
                    $isNewCulture ? CultureHistory::ACTION_CREATED : CultureHistory::ACTION_UPDATED,
                    $isNewCulture ? 'Creation de la culture.' : 'Modification des informations de la culture.',
                    $this->getAuthenticatedUser()
                );
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
            'yield_coefficients' => $yieldEstimatorService->getCoefficients(),
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

    private function recordCultureHistory(
        EntityManagerInterface $entityManager,
        Culture $culture,
        string $action,
        ?string $details = null,
        ?Utilisateur $utilisateur = null,
    ): void {
        $historyEntry = (new CultureHistory())
            ->setCulture($culture)
            ->setAction($action)
            ->setPerformedAt(new \DateTime())
            ->setUtilisateur($utilisateur)
            ->setDetails($details);

        $entityManager->persist($historyEntry);
    }

    private function canUndoHarvest(Culture $culture, ?int $utilisateurId): bool
    {
        if (!$culture->isRecoltee() || null === $utilisateurId) {
            return false;
        }

        if ($culture->hasAcheteur()) {
            return $culture->isBoughtBy($utilisateurId);
        }

        return $culture->isOwnedBy($utilisateurId);
    }

    private function determineStateAfterHarvestUndo(Culture $culture): string
    {
        if ($culture->hasAcheteur()) {
            return Culture::ETAT_VENDUE;
        }

        if (null !== $culture->getDatePublication()) {
            return Culture::ETAT_EN_VENTE;
        }

        return Culture::ETAT_EN_COURS;
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

    /* private function buildPdfSignatureSource(?string $signaturePath): ?string
    {
        if (null === $signaturePath || '' === trim($signaturePath)) {
            return null;
        }

        $publicPath = rtrim((string) $this->getParameter('kernel.project_dir'), '\\/').DIRECTORY_SEPARATOR.'public';
        $relativePath = ltrim(trim($signaturePath), '\\/');
        $absolutePath = $publicPath.DIRECTORY_SEPARATOR.str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relativePath);

        if (!is_file($absolutePath)) {
            return null;
        }

        return 'file:///'.str_replace(DIRECTORY_SEPARATOR, '/', $absolutePath);
    } */

    private function buildPdfSignatureSource(?string $signaturePath): ?string
    {
        $signaturePath = trim((string) $signaturePath);

        if ('' === $signaturePath) {
            return null;
        }

        $normalizedPath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $signaturePath);

        if ($this->isAbsoluteFilesystemPath($signaturePath) && is_file($normalizedPath)) {
            return 'file:///'.str_replace(DIRECTORY_SEPARATOR, '/', $normalizedPath);
        }

        $publicPath = rtrim((string) $this->getParameter('kernel.project_dir'), '\\/').DIRECTORY_SEPARATOR.'public';
        $relativePath = ltrim($signaturePath, '\\/');
        $absolutePath = $publicPath.DIRECTORY_SEPARATOR.str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relativePath);

        if (!is_file($absolutePath)) {
            return null;
        }

        return 'file:///'.str_replace(DIRECTORY_SEPARATOR, '/', $absolutePath);
    }

    private function isAbsoluteFilesystemPath(string $path): bool
    {
        return 1 === preg_match('/^[A-Za-z]:[\\\\\\/]/', $path)
            || str_starts_with($path, '\\\\');
    }

    /**
     * @param Culture[] $cultures
     * @return array<int, string>
     */
    private function buildUserDisplayNamesById(array $cultures, UtilisateurRepository $utilisateurRepository): array
    {
        $ownerNamesById = [];
        $ownerIds = [];

        foreach ($cultures as $culture) {
            $ownerId = $culture->getProprietaireId();

            if (null !== $ownerId) {
                $ownerIds[$ownerId] = true;
            }
        }

        foreach (array_keys($ownerIds) as $ownerId) {
            $user = $utilisateurRepository->find($ownerId);

            if (!$user instanceof Utilisateur) {
                continue;
            }

            $fullName = trim(sprintf('%s %s', $user->getPrenom() ?? '', $user->getNom() ?? ''));
            $ownerNamesById[$ownerId] = '' !== $fullName ? $fullName : ((string) $user->getEmail() ?: 'Agriculteur');
        }

        return $ownerNamesById;
    }
}
