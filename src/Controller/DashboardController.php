<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/dashboard', name: 'api_dashboard_')]
final class DashboardController
{
    #[Route('/statistics', name: 'statistics', methods: ['GET'])]
    public function getStatistics(): JsonResponse
    {
        return new JsonResponse([
            'success' => true,
            'data' => [
                'parcelles' => 5,
                'cultures' => 12,
                'waterPerWeek' => 1250,
            ],
        ]);
    }
}
