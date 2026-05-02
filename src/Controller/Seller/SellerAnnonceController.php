<?php

declare(strict_types=1);

namespace App\Controller\Seller;

use App\Entity\Annonce;
use App\Entity\Utilisateur;
use App\Enum\AnnonceStatut;
use App\Form\AnnonceFormType;
use App\Repository\AnnonceRepository;
use App\Repository\ReservationRepository;
use App\Service\AnnonceAiAssistantService;
use App\Service\AnnonceGeocodingService;
use App\Service\SellerMarketplaceService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/mon-espace/annonces', name: 'app_seller_annonce_')]
#[IsGranted('ROLE_AGRICULTEUR')]
final class SellerAnnonceController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(
        Request $request,
        AnnonceRepository $annonceRepository,
        ReservationRepository $reservationRepository,
        PaginatorInterface $paginator
    ): Response {
        $user = $this->getSellerUser();
        $ownerId = $user->getId();
        if ($ownerId === null) {
            throw $this->createAccessDeniedException('Connexion agriculteur requise.');
        }

        // owner: espace vendeur y7seb w ywarri ken data mta3 user connecte
        $allAnnonces = $annonceRepository->findByOwnerId($ownerId);
        $receivedReservations = $reservationRepository->findReceivedByOwnerId($ownerId);
        $annonces = $paginator->paginate(
            $annonceRepository->createByOwnerIdQueryBuilder($ownerId),
            $request->query->getInt('page', 1),
            8
        );
        $acceptedReservations = array_filter(
            $receivedReservations,
            static fn ($reservation) => $reservation->getStatut()->value === 'ACCEPTEE'
        );

        return $this->render('seller/annonce/index.html.twig', [
            'annonces' => $annonces,
            'stats' => [
                'annonces' => count($allAnnonces),
                'demandes' => count($receivedReservations),
                'enAttente' => count(array_filter(
                    $receivedReservations,
                    static fn ($reservation) => $reservation->getStatut()->value === 'EN_ATTENTE'
                )),
                'acceptees' => count($acceptedReservations),
                'revenuEstime' => array_reduce(
                    $acceptedReservations,
                    static fn (float $carry, $reservation): float => $carry + $reservation->getPrixTotalAsFloat(),
                    0.0
                ),
            ],
        ]);
    }

    #[Route('/nouvelle', name: 'new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        AnnonceGeocodingService $annonceGeocodingService,
        SellerMarketplaceService $sellerMarketplaceService
    ): Response {
        $user = $this->getSellerUser();
        $annonce = new Annonce();

        // owner: proprietaire ma yjich men form, yet7at direct men session
        $sellerMarketplaceService->assignAnnonceOwner($annonce, $user);

        $form = $this->createForm(AnnonceFormType::class, $annonce);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // geocoding: n7awlou n7assnou localisation ama ma nwa9fouch sauvegarde ken API tfaili
            $geocodingOutcome = $annonceGeocodingService->enrichAnnonce($annonce);
            $entityManager->persist($annonce);
            $entityManager->flush();

            $message = 'Annonce publiee avec succes.';

            if ($annonce->getStatut() === AnnonceStatut::DISPONIBLE) {
                $message .= ' Elle apparait maintenant dans le marketplace public.';
            } else {
                $message .= ' Elle reste visible dans votre espace vendeur tant que son statut n est pas Disponible.';
            }

            $this->addFlash('success', $message);
            $this->flashGeocodingOutcome($geocodingOutcome);

            return $this->redirectToRoute('app_seller_annonce_index');
        }

        return $this->render('seller/annonce/new.html.twig', [
            'form' => $form->createView(),
        ], $form->isSubmitted() ? new Response(status: Response::HTTP_UNPROCESSABLE_ENTITY) : null);
    }

    #[Route('/{id}/modifier', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        #[MapEntity(mapping: ['id' => 'id'])] Annonce $annonce,
        EntityManagerInterface $entityManager,
        AnnonceGeocodingService $annonceGeocodingService,
        SellerMarketplaceService $sellerMarketplaceService
    ): Response {
        $user = $this->getSellerUser();

        // security: vendeur ymodifi ken annonce mte3ou, moch annonce mta3 user e5er
        $this->denyAccessUnlessGrantedToOwner($sellerMarketplaceService, $user, $annonce);

        $form = $this->createForm(AnnonceFormType::class, $annonce);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // geocoding: edit zeda ya3mel refresh lel adresse normalisee ken localisation tbaddlet
            $geocodingOutcome = $annonceGeocodingService->enrichAnnonce($annonce);
            $entityManager->flush();

            $message = 'Annonce modifiee avec succes.';

            if ($annonce->getStatut() === AnnonceStatut::DISPONIBLE) {
                $message .= ' La fiche reste visible dans le marketplace public.';
            } else {
                $message .= ' Elle n apparait plus dans le marketplace public tant que son statut n est pas Disponible.';
            }

            $this->addFlash('success', $message);
            $this->flashGeocodingOutcome($geocodingOutcome);

            return $this->redirectToRoute('app_seller_annonce_index');
        }

        return $this->render('seller/annonce/edit.html.twig', [
            'annonce' => $annonce,
            'form' => $form->createView(),
        ], $form->isSubmitted() ? new Response(status: Response::HTTP_UNPROCESSABLE_ENTITY) : null);
    }

    #[Route('/{id}/supprimer', name: 'delete', methods: ['POST'])]
    public function delete(
        Request $request,
        #[MapEntity(mapping: ['id' => 'id'])] Annonce $annonce,
        EntityManagerInterface $entityManager,
        SellerMarketplaceService $sellerMarketplaceService
    ): Response {
        $user = $this->getSellerUser();

        // security: suppression zeda marbouta bel ownership mta3 annonce
        $this->denyAccessUnlessGrantedToOwner($sellerMarketplaceService, $user, $annonce);

        if ($this->isCsrfTokenValid('delete-seller-annonce-'.$annonce->getId(), (string) $request->request->get('_token'))) {
            $entityManager->remove($annonce);
            $entityManager->flush();

            $this->addFlash('success', 'Annonce supprimee avec succes.');
        }

        return $this->redirectToRoute('app_seller_annonce_index');
    }

    #[Route('/ai-assistant', name: 'ai_assistant', methods: ['POST'])]
    public function aiAssistant(
        Request $request,
        AnnonceAiAssistantService $annonceAiAssistantService
    ): JsonResponse {
        try {
            // ia: l assistant yrajja3 suggestions bark, ma ysajjel chay wahdou
            $payload = $request->toArray();
            $suggestions = $annonceAiAssistantService->generateSuggestions($payload);

            return new JsonResponse([
                'success' => true,
                'message' => 'Suggestions generees avec succes.',
                'suggestions' => $suggestions,
            ]);
        } catch (\DomainException $exception) {
            return new JsonResponse([
                'success' => false,
                'message' => $exception->getMessage(),
            ], Response::HTTP_SERVICE_UNAVAILABLE);
        } catch (\Throwable) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Assistant indisponible pour le moment.',
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    private function getSellerUser(): Utilisateur
    {
        $user = $this->getUser();

        if (!$user instanceof Utilisateur || null === $user->getId()) {
            throw $this->createAccessDeniedException('Connexion agriculteur requise.');
        }

        return $user;
    }

    private function denyAccessUnlessGrantedToOwner(
        SellerMarketplaceService $sellerMarketplaceService,
        Utilisateur $user,
        Annonce $annonce
    ): void {
        if (!$sellerMarketplaceService->isAnnonceOwner($user, $annonce)) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas modifier cette annonce.');
        }
    }

    /**
     * @param array{status: string, message: ?string} $outcome
     */
    private function flashGeocodingOutcome(array $outcome): void
    {
        if (null === $outcome['message']) {
            return;
        }

        $this->addFlash($outcome['status'] === 'matched' ? 'success' : 'warning', $outcome['message']);
    }
}
