<?php

declare(strict_types=1);

namespace App\Controller\Seller;

use App\Entity\Reservation;
use App\Entity\Utilisateur;
use App\Repository\AnnonceRepository;
use App\Repository\ReservationRepository;
use App\Service\SellerMarketplaceService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/mon-espace/demandes', name: 'app_seller_request_')]
#[IsGranted('ROLE_AGRICULTEUR')]
final class SellerRequestController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(
        Request $request,
        ReservationRepository $reservationRepository,
        AnnonceRepository $annonceRepository
    ): Response {
        $user = $this->getSellerUser();
        $annonceId = $request->query->getInt('annonce');
        $selectedAnnonce = null;

        if ($annonceId > 0) {
            $selectedAnnonce = $annonceRepository->find($annonceId);

            if (null === $selectedAnnonce || $selectedAnnonce->getProprietaireId() !== $user->getId()) {
                throw $this->createAccessDeniedException('Annonce non accessible.');
            }
        }

        $reservations = $reservationRepository->findReceivedByOwnerId(
            $user->getId(),
            $annonceId > 0 ? $annonceId : null
        );

        return $this->render('seller/request/index.html.twig', [
            'reservations' => $reservations,
            'selectedAnnonce' => $selectedAnnonce,
            'annonces' => $annonceRepository->findByOwnerId($user->getId()),
        ]);
    }

    #[Route('/{id}/accepter', name: 'accept', methods: ['POST'])]
    public function accept(
        Request $request,
        #[MapEntity(mapping: ['id' => 'id'])] Reservation $reservation,
        EntityManagerInterface $entityManager,
        SellerMarketplaceService $sellerMarketplaceService
    ): Response {
        $user = $this->getSellerUser();
        $this->denyAccessUnlessGrantedToOwner($sellerMarketplaceService, $user, $reservation);

        if ($this->isCsrfTokenValid('accept-seller-reservation-'.$reservation->getId(), (string) $request->request->get('_token'))) {
            try {
                // houni nbadlou statut reservation w stock annonce fi service wahda bech logique tab9a majem3a
                $sellerMarketplaceService->acceptReservation($reservation);
                $entityManager->flush();
                $this->addFlash('success', 'Demande acceptee avec succes.');
            } catch (\DomainException $exception) {
                $this->addFlash('danger', $exception->getMessage());
            }
        }

        return $this->redirectBackToRequests($reservation);
    }

    #[Route('/{id}/refuser', name: 'refuse', methods: ['POST'])]
    public function refuse(
        Request $request,
        #[MapEntity(mapping: ['id' => 'id'])] Reservation $reservation,
        EntityManagerInterface $entityManager,
        SellerMarketplaceService $sellerMarketplaceService
    ): Response {
        $user = $this->getSellerUser();
        $this->denyAccessUnlessGrantedToOwner($sellerMarketplaceService, $user, $reservation);

        if ($this->isCsrfTokenValid('refuse-seller-reservation-'.$reservation->getId(), (string) $request->request->get('_token'))) {
            try {
                $sellerMarketplaceService->refuseReservation($reservation);
                $entityManager->flush();
                $this->addFlash('success', 'Demande refusee avec succes.');
            } catch (\DomainException $exception) {
                $this->addFlash('danger', $exception->getMessage());
            }
        }

        return $this->redirectBackToRequests($reservation);
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
        Reservation $reservation
    ): void {
        if (!$sellerMarketplaceService->isReservationOwner($user, $reservation)) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas gerer cette demande.');
        }
    }

    private function redirectBackToRequests(Reservation $reservation): Response
    {
        return $this->redirectToRoute('app_seller_request_index', [
            'annonce' => $reservation->getAnnonce()?->getId(),
        ]);
    }
}
