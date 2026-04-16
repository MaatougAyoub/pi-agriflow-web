<?php

namespace App\Controller;

use App\Repository\CultureRepository;
use App\Repository\DiagnostiRepository;
use App\Repository\ParcelleRepository;
use App\Repository\PlansIrrigationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'dashboard_index')]
    public function index(
        CultureRepository $cultureRepo,
        PlansIrrigationRepository $planRepo,
        DiagnostiRepository $diagRepo,
        ParcelleRepository $parcelleRepo
    ): Response {
        $user     = $this->getUser();
        $isExpert = $this->isGranted('ROLE_EXPERT');

        // Cultures de l'utilisateur
        $cultures = $isExpert
            ? $cultureRepo->findAll()
            : $cultureRepo->findBy(['proprietaire_id' => $user->getId()]);

        // Plans irrigation
        $plans = $isExpert
            ? $planRepo->findAll()
            : $planRepo->findByProprietaire($user->getId());

        // Diagnostics
        $diagnostics = $isExpert
            ? $diagRepo->findAll()
            : $diagRepo->findBy(['utilisateur' => $user]);

        // Calcul besoin eau par culture pour le graphique
        $besoinEauData = [];
        $totalEauSemaine = 0;
        foreach ($cultures as $c) {
            $besoin = $this->calculerBesoinEau($c);
            $besoinSemaine = $besoin * 7;
            $besoinEauData[] = [
                'nom'    => $c->getNom(),
                'besoin' => round($besoinSemaine, 1),
                'type'   => $c->getTypeCulture() ?? 'AUTRE',
            ];
            $totalEauSemaine += $besoinSemaine;
        }

        // Répartition par type de culture pour le pie chart
        $typesData = [];
        foreach ($cultures as $c) {
            $type = $c->getTypeCulture() ?? 'AUTRE';
            $typesData[$type] = ($typesData[$type] ?? 0) + 1;
        }

        // Statistiques diagnostics
        $diagEnAttente = count(array_filter(
            $diagnostics,
            fn($d) => in_array($d->getStatut(), ['en_attente', null, ''])
        ));
        $diagTraites = count(array_filter(
            $diagnostics,
            fn($d) => $d->getStatut() === 'traite'
        ));

        // Plans par statut
        $plansParStatut = [];
        foreach ($plans as $p) {
            $s = $p->getStatut() ?? 'en_attente';
            $plansParStatut[$s] = ($plansParStatut[$s] ?? 0) + 1;
        }

        return $this->render('dashboard/index.html.twig', [
            'nb_cultures'       => count($cultures),
            'nb_parcelles'      => count(array_unique(array_map(
                fn($c) => $c->getParcelleId(), $cultures
            ))),
            'nb_plans'          => count($plans),
            'nb_diagnostics'    => count($diagnostics),
            'diag_en_attente'   => $diagEnAttente,
            'diag_traites'      => $diagTraites,
            'total_eau_semaine' => round($totalEauSemaine, 1),
            'besoin_eau_data'   => $besoinEauData,
            'types_data'        => $typesData,
            'plans_par_statut'  => $plansParStatut,
            'is_expert'         => $isExpert,
            'cultures'          => $cultures,
            'plans_recents'     => array_slice($plans, 0, 5),
            'diags_recents'     => array_slice($diagnostics, 0, 5),
        ]);
    }

    private function calculerBesoinEau($culture): float
    {
        $facteurs = [
            'BLE' => 2.0, 'ORGE' => 2.0, 'FRAISE' => 2.0, 'AUTRE' => 2.0,
            'MAIS' => 4.0, 'TOMATE' => 4.0,
            'POMME_DE_TERRE' => 3.0, 'AGRUMES' => 3.0, 'LEGUMES' => 3.0,
            'OLIVIER' => 1.0, 'VIGNE' => 1.0,
        ];
        $type      = strtoupper($culture->getTypeCulture() ?? 'AUTRE');
        $facteur   = $facteurs[$type] ?? 2.0;
        $superficie = $culture->getSuperficie() ?? 1.0;
        return $facteur * $superficie;
    }
}