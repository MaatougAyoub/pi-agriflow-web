<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Diagnostic;
use App\Repository\DiagnosticRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/diagnostics', name: 'api_diagnostics_')]
final class DiagnosticController
{
    public function __construct(
        private readonly DiagnosticRepository $repository,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    #[Route('', name: 'index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $diagnostics = $this->repository->findAll();

        $data = array_map(static function (Diagnostic $d): array {
            return [
                'id' => $d->getId(),
                'idAgriculteur' => $d->getIdAgriculteur(),
                'nomCulture' => $d->getNomCulture(),
                'imagePath' => $d->getImagePath(),
                'description' => $d->getDescription(),
                'reponseExpert' => $d->getReponseExpert(),
                'statut' => $d->getStatut(),
                'dateEnvoi' => $d->getDateEnvoi()?->format('Y-m-d H:i:s'),
                'dateReponse' => $d->getDateReponse()?->format('Y-m-d H:i:s'),
            ];
        }, $diagnostics);

        return new JsonResponse([
            'success' => true,
            'data' => $data,
        ]);
    }

    #[Route('/create', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $payload = json_decode($request->getContent(), true) ?? [];

        $diagnostic = new Diagnostic();
        $diagnostic->setIdAgriculteur((int) ($payload['idAgriculteur'] ?? 0));
        $diagnostic->setNomCulture((string) ($payload['nomCulture'] ?? ''));
        $diagnostic->setImagePath($payload['imagePath'] ?? null);
        $diagnostic->setDescription((string) ($payload['description'] ?? ''));
        $diagnostic->setStatut($payload['statut'] ?? 'En attente');
        $diagnostic->setDateEnvoi(new \DateTime());

        $this->entityManager->persist($diagnostic);
        $this->entityManager->flush();

        return new JsonResponse(['success' => true, 'id' => $diagnostic->getId()], JsonResponse::HTTP_CREATED);
    }
}
