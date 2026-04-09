<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Reservation;
use App\Form\ReservationFormType;
use App\Repository\ReservationRepository;
use App\Service\ReservationPricingService;
use App\Service\ReservationPdfService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/reservations', name: 'app_admin_reservation_')]
#[IsGranted('ROLE_ADMIN')]
final class AdminReservationController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(
        Request $request,
        ReservationRepository $reservationRepository,
        PaginatorInterface $paginator
    ): Response
    {
        return $this->render('admin/reservation/index.html.twig', [
            'reservations' => $paginator->paginate(
                $reservationRepository->createAllForAdminQueryBuilder(),
                $request->query->getInt('page', 1),
                10
            ),
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        ReservationPricingService $pricingService
    ): Response {
        $reservation = new Reservation();
        $form = $this->createForm(ReservationFormType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $pricingService->hydrateReservation($reservation);

                $entityManager->persist($reservation);
                $entityManager->flush();

                $this->addFlash('success', 'Reservation ajoutee avec succes.');

                return $this->redirectToRoute('app_admin_reservation_index');
            } catch (\DomainException $exception) {
                $form->addError(new FormError($exception->getMessage()));
            }
        }

        return $this->render('admin/reservation/new.html.twig', [
            'reservation' => $reservation,
            'form' => $form->createView(),
        ], $form->isSubmitted() ? new Response(status: Response::HTTP_UNPROCESSABLE_ENTITY) : null);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        #[MapEntity(mapping: ['id' => 'id'])] Reservation $reservation,
        EntityManagerInterface $entityManager,
        ReservationPricingService $pricingService
    ): Response {
        $form = $this->createForm(ReservationFormType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $pricingService->hydrateReservation($reservation);
                $entityManager->flush();

                $this->addFlash('success', 'Reservation modifiee avec succes.');

                return $this->redirectToRoute('app_admin_reservation_index');
            } catch (\DomainException $exception) {
                $form->addError(new FormError($exception->getMessage()));
            }
        }

        return $this->render('admin/reservation/edit.html.twig', [
            'reservation' => $reservation,
            'form' => $form->createView(),
        ], $form->isSubmitted() ? new Response(status: Response::HTTP_UNPROCESSABLE_ENTITY) : null);
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(
        Request $request,
        #[MapEntity(mapping: ['id' => 'id'])] Reservation $reservation,
        EntityManagerInterface $entityManager
    ): Response
    {
        if ($this->isCsrfTokenValid('delete-reservation-'.$reservation->getId(), (string) $request->request->get('_token'))) {
            $entityManager->remove($reservation);
            $entityManager->flush();

            $this->addFlash('success', 'Reservation supprimee avec succes.');
        }

        return $this->redirectToRoute('app_admin_reservation_index');
    }

    #[Route('/{id}/devis', name: 'quote', methods: ['GET'])]
    public function quote(
        #[MapEntity(mapping: ['id' => 'id'])] Reservation $reservation,
        ReservationPdfService $reservationPdfService
    ): Response {
        return $reservationPdfService->streamReservationQuote($reservation, 'devis-admin');
    }
}
