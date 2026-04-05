<?php

declare(strict_types=1);

namespace App\Controller\Seller;

use App\Entity\Annonce;
use App\Entity\Utilisateur;
use App\Form\AnnonceFormType;
use App\Repository\AnnonceRepository;
use App\Repository\ReservationRepository;
use App\Service\SellerMarketplaceService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/mon-espace/annonces', name: 'app_seller_annonce_')]
#[IsGranted('ROLE_AGRICULTEUR')]
final class SellerAnnonceController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(
        AnnonceRepository $annonceRepository,
        ReservationRepository $reservationRepository
    ): Response {
        $user = $this->getSellerUser();
        $annonces = $annonceRepository->findByOwnerId($user->getId());
        $receivedReservations = $reservationRepository->findReceivedByOwnerId($user->getId());

        return $this->render('seller/annonce/index.html.twig', [
            'annonces' => $annonces,
            'stats' => [
                'annonces' => count($annonces),
                'demandes' => count($receivedReservations),
                'enAttente' => count(array_filter(
                    $receivedReservations,
                    static fn ($reservation) => $reservation->getStatut()->value === 'EN_ATTENTE'
                )),
            ],
        ]);
    }

    #[Route('/nouvelle', name: 'new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        SellerMarketplaceService $sellerMarketplaceService
    ): Response {
        $user = $this->getSellerUser();
        $annonce = new Annonce();
        $sellerMarketplaceService->assignAnnonceOwner($annonce, $user);

        $form = $this->createForm(AnnonceFormType::class, $annonce);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($annonce);
            $entityManager->flush();

            $this->addFlash('success', 'Annonce publiee avec succes.');

            return $this->redirectToRoute('app_seller_annonce_index');
        }

        return $this->render('seller/annonce/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/modifier', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        #[MapEntity(mapping: ['id' => 'id'])] Annonce $annonce,
        EntityManagerInterface $entityManager,
        SellerMarketplaceService $sellerMarketplaceService
    ): Response {
        $user = $this->getSellerUser();
        $this->denyAccessUnlessGrantedToOwner($sellerMarketplaceService, $user, $annonce);

        $form = $this->createForm(AnnonceFormType::class, $annonce);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Annonce modifiee avec succes.');

            return $this->redirectToRoute('app_seller_annonce_index');
        }

        return $this->render('seller/annonce/edit.html.twig', [
            'annonce' => $annonce,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/supprimer', name: 'delete', methods: ['POST'])]
    public function delete(
        Request $request,
        #[MapEntity(mapping: ['id' => 'id'])] Annonce $annonce,
        EntityManagerInterface $entityManager,
        SellerMarketplaceService $sellerMarketplaceService
    ): Response {
        $user = $this->getSellerUser();
        $this->denyAccessUnlessGrantedToOwner($sellerMarketplaceService, $user, $annonce);

        if ($this->isCsrfTokenValid('delete-seller-annonce-'.$annonce->getId(), (string) $request->request->get('_token'))) {
            $entityManager->remove($annonce);
            $entityManager->flush();

            $this->addFlash('success', 'Annonce supprimee avec succes.');
        }

        return $this->redirectToRoute('app_seller_annonce_index');
    }

    private function getSellerUser(): Utilisateur
    {
        $user = $this->getUser();

        if (!$user instanceof Utilisateur || null === $user->getId()) {
            throw $this->createAccessDeniedException('Connexion agriculteur requise.');
        }

        return $user;
    }

    private function denyAccessUnlessGrantedToOwner(
        SellerMarketplaceService $sellerMarketplaceService,
        Utilisateur $user,
        Annonce $annonce
    ): void {
        if (!$sellerMarketplaceService->isAnnonceOwner($user, $annonce)) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas modifier cette annonce.');
        }
    }
}
