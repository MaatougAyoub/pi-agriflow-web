<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Annonce;
use App\Entity\Utilisateur;
use App\Form\AnnonceFormType;
use App\Repository\AnnonceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/annonces', name: 'app_admin_annonce_')]
#[IsGranted('ROLE_ADMIN')]
final class AdminAnnonceController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(AnnonceRepository $annonceRepository): Response
    {
        return $this->render('admin/annonce/index.html.twig', [
            'annonces' => $annonceRepository->findBy([], ['createdAt' => 'DESC']),
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $annonce = new Annonce();

        if (($user = $this->getUser()) instanceof Utilisateur && null !== $user->getId()) {
            // houni n7otou proprietaire mta3 l admin connecte automatiquement bech ma n5alliwch champ technique yban
            $annonce->setProprietaireId($user->getId());
        }

        $form = $this->createForm(AnnonceFormType::class, $annonce);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($annonce);
            $entityManager->flush();

            $this->addFlash('success', 'Annonce ajoutee avec succes.');

            return $this->redirectToRoute('app_admin_annonce_index');
        }

        return $this->render('admin/annonce/new.html.twig', [
            'annonce' => $annonce,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        #[MapEntity(mapping: ['id' => 'id'])] Annonce $annonce,
        EntityManagerInterface $entityManager
    ): Response {
        $form = $this->createForm(AnnonceFormType::class, $annonce);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Annonce modifiee avec succes.');

            return $this->redirectToRoute('app_admin_annonce_index');
        }

        return $this->render('admin/annonce/edit.html.twig', [
            'annonce' => $annonce,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(
        Request $request,
        #[MapEntity(mapping: ['id' => 'id'])] Annonce $annonce,
        EntityManagerInterface $entityManager
    ): Response {
        if ($this->isCsrfTokenValid('delete-annonce-'.$annonce->getId(), (string) $request->request->get('_token'))) {
            $entityManager->remove($annonce);
            $entityManager->flush();

            $this->addFlash('success', 'Annonce supprimee avec succes.');
        }

        return $this->redirectToRoute('app_admin_annonce_index');
    }
}
