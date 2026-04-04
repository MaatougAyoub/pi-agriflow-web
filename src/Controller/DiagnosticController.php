<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/diagnostics', name: 'api_diagnostics_')]
final class DiagnosticController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        return new JsonResponse([
            'success' => true,
            'data' => [],
        ]);
    }

    #[Route('/create', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        return new JsonResponse(['success' => true], JsonResponse::HTTP_CREATED);
    }
}
