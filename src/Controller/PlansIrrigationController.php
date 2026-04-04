<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\PlansIrrigationJour;
use App\Repository\PlansIrrigationJourRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/irrigation-plans', name: 'api_irrigation_plans_')]
final class PlansIrrigationController
{
    public function __construct(
        private readonly PlansIrrigationJourRepository $repository,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    #[Route('', name: 'index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $plans = $this->repository->findAll();

        $data = array_map(static function (PlansIrrigationJour $plan): array {
            return [
                'id' => $plan->getId(),
                'planId' => $plan->getPlanId(),
                'jour' => $plan->getJour(),
                'eauMm' => $plan->getEauMm(),
                'tempsMin' => $plan->getTempsMin(),
                'tempC' => $plan->getTempC(),
                'semaineDebut' => $plan->getSemaineDebut()?->format('Y-m-d'),
                'humidite' => $plan->getHumidite(),
                'pluie' => $plan->getPluie(),
            ];
        }, $plans);

        return new JsonResponse([
            'success' => true,
            'data' => $data,
        ]);
    }

    #[Route('/create', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $payload = json_decode($request->getContent(), true) ?? [];

        $plan = new PlansIrrigationJour();
        $plan->setPlanId((int) ($payload['planId'] ?? 0));
        $plan->setJour((string) ($payload['jour'] ?? ''));
        $plan->setEauMm(isset($payload['eauMm']) ? (float) $payload['eauMm'] : null);
        $plan->setTempsMin(isset($payload['tempsMin']) ? (int) $payload['tempsMin'] : null);
        $plan->setTempC(isset($payload['tempC']) ? (float) $payload['tempC'] : null);
        $plan->setSemaineDebut(isset($payload['semaineDebut']) ? new \DateTime($payload['semaineDebut']) : null);
        $plan->setHumidite(isset($payload['humidite']) ? (float) $payload['humidite'] : null);
        $plan->setPluie(isset($payload['pluie']) ? (float) $payload['pluie'] : null);

        $this->entityManager->persist($plan);
        $this->entityManager->flush();

        return new JsonResponse(['success' => true, 'id' => $plan->getId()], JsonResponse::HTTP_CREATED);
    }
}
