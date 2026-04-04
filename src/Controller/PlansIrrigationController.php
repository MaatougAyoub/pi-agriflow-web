<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/irrigation-plans', name: 'api_irrigation_plans_')]
final class PlansIrrigationController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        return new JsonResponse([
            'success' => true,
            'data' => [
                ['id' => 1, 'name' => 'Plan Tomate', 'culture' => 'Tomate', 'waterNeeded' => 500],
                ['id' => 2, 'name' => 'Plan Blé', 'culture' => 'Blé', 'waterNeeded' => 300],
            ],
        ]);
    }

    #[Route('/create', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        return new JsonResponse(['success' => true], JsonResponse::HTTP_CREATED);
    }
}
