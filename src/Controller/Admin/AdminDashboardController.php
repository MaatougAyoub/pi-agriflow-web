<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Repository\AnnonceRepository;
use App\Repository\ReservationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin', name: 'app_admin_')]
#[IsGranted('ROLE_ADMIN')]
final class AdminDashboardController extends AbstractController
{
    #[Route('', name: 'dashboard', methods: ['GET'])]
    public function index(
        AnnonceRepository $annonceRepository,
        ReservationRepository $reservationRepository
    ): Response {
        return $this->render('admin/dashboard.html.twig', [
            'annonceCount' => $annonceRepository->count([]),
            'reservationCount' => $reservationRepository->count([]),
            'pendingReservations' => $reservationRepository->findLatestPending(),
            'latestAnnonces' => $annonceRepository->findBy([], ['createdAt' => 'DESC'], 5),
        ]);
    }
}
