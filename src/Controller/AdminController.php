<?php

namespace App\Controller;

use App\Repository\CultureRepository;
use App\Repository\ParcelleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin', name: 'app_admin_')]
final class AdminController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(
        ParcelleRepository $parcelleRepository,
        CultureRepository $cultureRepository,
    ): Response {
        return $this->render('admin/index.html.twig', [
            'parcelle_count' => $parcelleRepository->count([]),
            'culture_count' => $cultureRepository->count([]),
        ]);
    }
}
