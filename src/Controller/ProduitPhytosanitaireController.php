<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProduitPhytosanitaireController extends AbstractController
{
    #[Route('/produit/phytosanitaire', name: 'app_produit_phytosanitaire')]
    public function index(): Response
    {
        return $this->render('produit_phytosanitaire/index.html.twig', [
            'controller_name' => 'ProduitPhytosanitaireController',
        ]);
    }
}
