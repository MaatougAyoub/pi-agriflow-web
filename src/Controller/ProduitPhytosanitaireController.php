<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\ProduitPhytosanitaire;
use App\Repository\ProduitPhytosanitaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/produits', name: 'api_produits_')]
final class ProduitPhytosanitaireController
{
    public function __construct(
        private readonly ProduitPhytosanitaireRepository $repository,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    #[Route('', name: 'index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $produits = $this->repository->findAll();

        $data = array_map(static function (ProduitPhytosanitaire $p): array {
            return [
                'id' => $p->getId(),
                'nomProduit' => $p->getNomProduit(),
                'dosage' => $p->getDosage(),
                'frequenceApplication' => $p->getFrequenceApplication(),
                'remarques' => $p->getRemarques(),
            ];
        }, $produits);

        return new JsonResponse([
            'success' => true,
            'data' => $data,
        ]);
    }

    #[Route('/create', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $payload = json_decode($request->getContent(), true) ?? [];

        $produit = new ProduitPhytosanitaire();
        $produit->setNomProduit((string) ($payload['nomProduit'] ?? ''));
        $produit->setDosage($payload['dosage'] ?? null);
        $produit->setFrequenceApplication($payload['frequenceApplication'] ?? null);
        $produit->setRemarques($payload['remarques'] ?? null);

        $this->entityManager->persist($produit);
        $this->entityManager->flush();

        return new JsonResponse(['success' => true, 'id' => $produit->getId()], JsonResponse::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'], requirements: ['id' => '\d+'])]
    public function delete(int $id): JsonResponse
    {
        $produit = $this->repository->find($id);

        if (null === $produit) {
            return new JsonResponse(['success' => false, 'message' => 'Produit not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($produit);
        $this->entityManager->flush();

        return new JsonResponse(['success' => true]);
    }
}
