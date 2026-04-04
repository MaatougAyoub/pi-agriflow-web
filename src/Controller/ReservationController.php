<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Annonce;
use App\Entity\Reservation;
use App\Enum\ReservationStatut;
use App\Form\FrontReservationType;
use App\Repository\ReservationRepository;
use App\Service\ReservationPricingService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ReservationController extends AbstractController
{
    #[Route('/marketplace/{id}/reserve', name: 'app_reservation_create', methods: ['POST'])]
    public function create(
        Request $request,
        #[MapEntity(mapping: ['id' => 'id'])] Annonce $annonce,
        EntityManagerInterface $entityManager,
        ReservationPricingService $pricingService,
        ReservationRepository $reservationRepository
    ): Response {
        $reservation = (new Reservation())
            ->setAnnonce($annonce)
            ->setStatut(ReservationStatut::EN_ATTENTE);

        $form = $this->createForm(FrontReservationType::class, $reservation);
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
        ], new Response(status: Response::HTTP_UNPROCESSABLE_ENTITY));
    }
}
