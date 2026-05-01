<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Culture;
use App\Repository\AnnonceRepository;
use App\Repository\CultureRepository;
use App\Repository\ParcelleRepository;
use App\Repository\ReservationRepository;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin', name: 'app_admin_')]
#[IsGranted('ROLE_ADMIN')]
final class AdminDashboardController extends AbstractController
{
    private const CULTURE_ETAT_LABELS = [
        Culture::ETAT_EN_COURS => 'EN_COURS',
        Culture::ETAT_EN_VENTE => 'EN_VENTE',
        Culture::ETAT_VENDUE => 'VENDUE',
        Culture::ETAT_RECOLTEE => 'RECOLTEE',
    ];

    private const CULTURE_TYPE_LABELS = [
        'BLE',
        'ORGE',
        'MAIS',
        'POMME_DE_TERRE',
        'TOMATE',
        'OLIVIER',
        'AGRUMES',
        'VIGNE',
        'PASTECQUE',
        'FRAISE',
        'LEGUMES',
        'AUTRE',
    ];

    private const PARCELLE_TYPE_TERRE_LABELS = [
        'ARGILEUSE',
        'SABLEUSE',
        'LIMONEUSE',
        'CALCAIRE',
        'HUMIFERE',
        'SALINE',
        'MIXTE',
        'AUTRE',
    ];

    #[Route('', name: 'dashboard', methods: ['GET'])]
    public function index(
        AnnonceRepository $annonceRepository,
        ReservationRepository $reservationRepository,
        ParcelleRepository $parcelleRepository,
        CultureRepository $cultureRepository,
    ): Response {
        return $this->render('admin/dashboard.html.twig', [
            'annonceCount' => $annonceRepository->count([]),
            'reservationCount' => $reservationRepository->count([]),
            'parcelleCount' => $parcelleRepository->count([]),
            'cultureCount' => $cultureRepository->count([]),
            'pendingReservations' => $reservationRepository->findLatestPending(),
            'latestAnnonces' => $annonceRepository->findBy([], ['createdAt' => 'DESC'], 5),
        ]);
    }

    #[Route('/stats', name: 'stats', methods: ['GET'])]
    public function stats(
        CultureRepository $cultureRepository,
        ParcelleRepository $parcelleRepository,
        ChartBuilderInterface $chartBuilder,
    ): Response {
        $cultures = $cultureRepository->findAll();
        $parcelles = $parcelleRepository->findAll();

        $cultureEtatCounts = $this->countCulturesByEtat($cultures);
        $cultureTypeCounts = $this->countCulturesByType($cultures);
        $parcelleTypeCounts = $this->countParcellesByTypeTerre($parcelles);

        $cultureEtatChart = $chartBuilder->createChart(Chart::TYPE_PIE);
        $cultureEtatChart->setData([
            'labels' => array_values(self::CULTURE_ETAT_LABELS),
            'datasets' => [[
                'label' => 'Cultures par etat',
                'data' => array_values($cultureEtatCounts),
                'backgroundColor' => ['#198754', '#ffc107', '#0d6efd', '#6c757d'],
            ]],
        ]);
        $cultureEtatChart->setOptions([
            'plugins' => [
                'legend' => ['position' => 'bottom'],
            ],
            'maintainAspectRatio' => false,
        ]);

        $cultureTypeChart = $chartBuilder->createChart(Chart::TYPE_BAR);
        $cultureTypeChart->setData([
            'labels' => self::CULTURE_TYPE_LABELS,
            'datasets' => [[
                'label' => 'Nombre de cultures',
                'data' => array_values($cultureTypeCounts),
                'backgroundColor' => '#198754',
                'borderRadius' => 6,
            ]],
        ]);
        $cultureTypeChart->setOptions([
            'plugins' => [
                'legend' => ['display' => false],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => ['precision' => 0],
                ],
            ],
            'maintainAspectRatio' => false,
        ]);

        $parcelleTypeChart = $chartBuilder->createChart(Chart::TYPE_DOUGHNUT);
        $parcelleTypeChart->setData([
            'labels' => self::PARCELLE_TYPE_TERRE_LABELS,
            'datasets' => [[
                'label' => 'Parcelles par type de terre',
                'data' => array_values($parcelleTypeCounts),
                'backgroundColor' => ['#14532d', '#22c55e', '#84cc16', '#f59e0b', '#0ea5e9', '#6366f1', '#a855f7', '#64748b'],
            ]],
        ]);
        $parcelleTypeChart->setOptions([
            'plugins' => [
                'legend' => ['position' => 'bottom'],
            ],
            'maintainAspectRatio' => false,
        ]);

        return $this->render('admin/stats.html.twig', [
            'culture_etat_chart' => $cultureEtatChart,
            'culture_type_chart' => $cultureTypeChart,
            'parcelle_type_chart' => $parcelleTypeChart,
        ]);
    }

    /**
     * @param Culture[] $cultures
     * @return array<string, int>
     */
    private function countCulturesByEtat(array $cultures): array
    {
        $counts = array_fill_keys(array_keys(self::CULTURE_ETAT_LABELS), 0);

        foreach ($cultures as $culture) {
            $etat = $culture->getEtat();

            if (isset($counts[$etat])) {
                ++$counts[$etat];
            }
        }

        return $counts;
    }

    /**
     * @param Culture[] $cultures
     * @return array<string, int>
     */
    private function countCulturesByType(array $cultures): array
    {
        $counts = array_fill_keys(self::CULTURE_TYPE_LABELS, 0);

        foreach ($cultures as $culture) {
            $typeCulture = strtoupper(trim((string) $culture->getTypeCulture()));

            if ('' === $typeCulture) {
                continue;
            }

            if (!isset($counts[$typeCulture])) {
                $typeCulture = 'AUTRE';
            }

            ++$counts[$typeCulture];
        }

        return $counts;
    }

    /**
     * @param \App\Entity\Parcelle[] $parcelles
     * @return array<string, int>
     */
    private function countParcellesByTypeTerre(array $parcelles): array
    {
        $counts = array_fill_keys(self::PARCELLE_TYPE_TERRE_LABELS, 0);

        foreach ($parcelles as $parcelle) {
            $typeTerre = strtoupper(trim((string) $parcelle->getTypeTerre()));

            if ('' === $typeTerre) {
                continue;
            }

            if (!isset($counts[$typeTerre])) {
                $typeTerre = 'AUTRE';
            }

            ++$counts[$typeTerre];
        }

        return $counts;
    }
}
