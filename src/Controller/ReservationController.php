<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Annonce;
use App\Entity\Reservation;
use App\Entity\Utilisateur;
use App\Enum\ReservationStatut;
use App\Form\FrontReservationType;
use App\Repository\ReservationRepository;
use App\Service\ReservationPricingService;
use App\Service\SellerMarketplaceService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class ReservationController extends AbstractController
{
    #[IsGranted('ROLE_AGRICULTEUR')]
    #[Route('/marketplace/{id}/reserve', name: 'app_reservation_create', methods: ['POST'])]
    public function create(
        Request $request,
        #[MapEntity(mapping: ['id' => 'id'])] Annonce $annonce,
        EntityManagerInterface $entityManager,
        ReservationPricingService $pricingService,
        ReservationRepository $reservationRepository,
        SellerMarketplaceService $sellerMarketplaceService
    ): Response {
        $user = $this->getUser();

        if (!$user instanceof Utilisateur || null === $user->getId()) {
            throw $this->createAccessDeniedException('Connexion agriculteur requise.');
        }

        try {
            $sellerMarketplaceService->ensureCanReserve($user, $annonce);
        } catch (\DomainException $exception) {
            $this->addFlash('danger', $exception->getMessage());

            return $this->redirectToRoute('app_marketplace_show', ['id' => $annonce->getId()]);
        }

        $reservation = (new Reservation())
            ->setAnnonce($annonce)
            ->setStatut(ReservationStatut::EN_ATTENTE)
            ->setClientId($user->getId());

        $form = $this->createForm(FrontReservationType::class, $reservation, [
            'action' => $this->generateUrl('app_reservation_create', ['id' => $annonce->getId()]),
            'method' => 'POST',
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $pricingService->hydrateReservation($reservation);

                $entityManager->persist($reservation);
                $entityManager->flush();

                $this->addFlash('success', 'Reservation enregistree avec succes.');

                return $this->redirectToRoute('app_marketplace_show', ['id' => $annonce->getId()]);
            } catch (\DomainException $exception) {
                $form->addError(new FormError($exception->getMessage()));
            }
        }

        $this->addFlash('danger', 'Le formulaire de reservation contient des erreurs.');

        return $this->render('marketplace/show.html.twig', [
            'annonce' => $annonce,
            'reservationForm' => $form->createView(),
            'recentReservations' => $reservationRepository->findBy(
                ['annonce' => $annonce],
                ['createdAt' => 'DESC'],
                5
            ),
            'visual' => $this->buildAnnonceVisual($annonce),
            'canReserve' => true,
            'isAgriculteurViewer' => true,
            'isAdminViewer' => false,
            'isOwnerViewer' => false,
        ], new Response(status: Response::HTTP_UNPROCESSABLE_ENTITY));
    }

    #[IsGranted('ROLE_AGRICULTEUR')]
    #[Route('/mes-reservations', name: 'app_reservation_my', methods: ['GET'])]
    public function myReservations(ReservationRepository $reservationRepository): Response
    {
        $user = $this->getUser();

        if (!$user instanceof Utilisateur || null === $user->getId()) {
            throw $this->createAccessDeniedException('Connexion agriculteur requise.');
        }

        return $this->render('reservation/my_reservations.html.twig', [
            'reservations' => $reservationRepository->findByClientIdForMarketplace($user->getId()),
        ]);
    }

    /**
     * @return array{image: string, alt: string, position: string}
     */
    private function buildAnnonceVisual(Annonce $annonce): array
    {
        $title = strtolower((string) $annonce->getTitre());
        $category = strtolower((string) $annonce->getCategorie());

        if (str_contains($title, 'tracteur') || str_contains($category, 'materiel')) {
            return [
                'image' => 'uploads/marketplace/tracteur-cover.jpg',
                'alt' => 'Tracteur agricole sur terrain laboure',
                'position' => 'center center',
            ];
        }

        if (str_contains($title, 'pompe') || str_contains($category, 'irrigation')) {
            return [
                'image' => 'uploads/marketplace/pompe-irrigation-cover.jpg',
                'alt' => 'Pompe d irrigation mobile',
                'position' => 'center center',
            ];
        }

        if (str_contains($title, 'tomate') || str_contains($category, 'produits')) {
            return [
                'image' => 'uploads/marketplace/tomates-caisses-cover.jpg',
                'alt' => 'Caisses de tomates fraiches',
                'position' => 'center center',
            ];
        }

        return [
            'image' => 'template/assets/img/hero_4.jpg',
            'alt' => (string) $annonce->getTitre(),
            'position' => 'center center',
        ];
    }
}
