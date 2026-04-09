<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Annonce;
use App\Entity\Utilisateur;
use App\Form\AnnonceFormType;
use App\Repository\AnnonceRepository;
use App\Service\AnnonceAiAssistantService;
use App\Service\AnnonceGeocodingService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/annonces', name: 'app_admin_annonce_')]
#[IsGranted('ROLE_ADMIN')]
final class AdminAnnonceController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(
        Request $request,
        AnnonceRepository $annonceRepository,
        PaginatorInterface $paginator
    ): Response
    {
        return $this->render('admin/annonce/index.html.twig', [
            'annonces' => $paginator->paginate(
                $annonceRepository->createQueryBuilder('a')->orderBy('a.createdAt', 'DESC'),
                $request->query->getInt('page', 1),
                10
            ),
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        AnnonceGeocodingService $annonceGeocodingService
    ): Response
    {
        $annonce = new Annonce();

        if (($user = $this->getUser()) instanceof Utilisateur && null !== $user->getId()) {
            // houni n7otou proprietaire mta3 l admin connecte automatiquement bech ma n5alliwch champ technique yban
            $annonce->setProprietaireId($user->getId());
        }

        $form = $this->createForm(AnnonceFormType::class, $annonce);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $geocodingOutcome = $annonceGeocodingService->enrichAnnonce($annonce);
            $entityManager->persist($annonce);
            $entityManager->flush();

            $this->addFlash('success', 'Annonce ajoutee avec succes.');
            $this->flashGeocodingOutcome($geocodingOutcome);

            return $this->redirectToRoute('app_admin_annonce_index');
        }

        return $this->render('admin/annonce/new.html.twig', [
            'annonce' => $annonce,
            'form' => $form->createView(),
        ], $form->isSubmitted() ? new Response(status: Response::HTTP_UNPROCESSABLE_ENTITY) : null);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        #[MapEntity(mapping: ['id' => 'id'])] Annonce $annonce,
        EntityManagerInterface $entityManager,
        AnnonceGeocodingService $annonceGeocodingService
    ): Response {
        $form = $this->createForm(AnnonceFormType::class, $annonce);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $geocodingOutcome = $annonceGeocodingService->enrichAnnonce($annonce);
            $entityManager->flush();

            $this->addFlash('success', 'Annonce modifiee avec succes.');
            $this->flashGeocodingOutcome($geocodingOutcome);

            return $this->redirectToRoute('app_admin_annonce_index');
        }

        return $this->render('admin/annonce/edit.html.twig', [
            'annonce' => $annonce,
            'form' => $form->createView(),
        ], $form->isSubmitted() ? new Response(status: Response::HTTP_UNPROCESSABLE_ENTITY) : null);
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

    #[Route('/ai-assistant', name: 'ai_assistant', methods: ['POST'])]
    public function aiAssistant(
        Request $request,
        AnnonceAiAssistantService $annonceAiAssistantService
    ): JsonResponse {
        try {
            $payload = $request->toArray();
            $suggestions = $annonceAiAssistantService->generateSuggestions($payload);

            return new JsonResponse([
                'success' => true,
                'message' => 'Suggestions generees avec succes.',
                'suggestions' => $suggestions,
            ]);
        } catch (\DomainException $exception) {
            return new JsonResponse([
                'success' => false,
                'message' => $exception->getMessage(),
            ], Response::HTTP_SERVICE_UNAVAILABLE);
        } catch (\Throwable) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Assistant indisponible pour le moment.',
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param array{status: string, message: ?string} $outcome
     */
    private function flashGeocodingOutcome(array $outcome): void
    {
        if (null === $outcome['message']) {
            return;
        }

        $this->addFlash($outcome['status'] === 'matched' ? 'success' : 'warning', $outcome['message']);
    }
}
