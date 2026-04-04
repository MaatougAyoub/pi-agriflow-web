<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\CultureRepository;
use App\Repository\ParcelleRepository;
use App\Repository\PlansIrrigationJourRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/dashboard', name: 'api_dashboard_')]
final class DashboardController
{
    public function __construct(
        private readonly ParcelleRepository $parcelleRepository,
        private readonly CultureRepository $cultureRepository,
        private readonly PlansIrrigationJourRepository $plansRepository,
    ) {
    }

    #[Route('/statistics', name: 'statistics', methods: ['GET'])]
    public function getStatistics(): JsonResponse
    {
        $parcelles = $this->parcelleRepository->count([]);
        $cultures = $this->cultureRepository->count([]);

        $plans = $this->plansRepository->findAll();
        $waterPerWeek = array_sum(array_map(
            static fn ($plan) => $plan->getEauMm() ?? 0.0,
            $plans
        ));

        return new JsonResponse([
            'success' => true,
            'data' => [
                'parcelles' => $parcelles,
                'cultures' => $cultures,
                'waterPerWeek' => $waterPerWeek,
            ],
        ]);
    }
}
