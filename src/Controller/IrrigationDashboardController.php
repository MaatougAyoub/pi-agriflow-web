<?php

namespace App\Controller;

use App\Repository\DiagnostiRepository;
use App\Repository\PlansIrrigationRepository;
use App\Repository\ProduitsPhytosanitaireRepository;
use App\Repository\CultureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class IrrigationDashboardController extends AbstractController
{
    #[Route('/irrigation/dashboard', name: 'irrigation_dashboard')]
    public function index(
        PlansIrrigationRepository $planRepo,
        DiagnostiRepository $diagRepo,
        ProduitsPhytosanitaireRepository $produitRepo
    ): Response {
        $isExpert = $this->isGranted('ROLE_EXPERT');
        return $this->render('irrigation/dashboard.html.twig', [
            'total_plans'         => count($planRepo->findAll()),
            'total_cultures'      => 0,
            'total_diagnostics'   => count($diagRepo->findAll()),
            'diagnostics_attente' => count($diagRepo->findBy(['statut' => 'en_attente'])),
            'total_produits'      => count($produitRepo->findAll()),
            'plans'               => $planRepo->findBy([], [], 5),
            'diagnostics'         => $diagRepo->findBy([], ['date_envoi' => 'DESC'], 5),
            'is_expert'           => $isExpert,
        ]);
    }
}
