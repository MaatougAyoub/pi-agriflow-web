<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\CollabApplication;
use App\Entity\CollabRequest;
use App\Form\CollabApplicationType;
use App\Form\CollabRequestType;
use App\Repository\CollabApplicationRepository;
use App\Repository\CollabRequestRepository;
use App\Service\CandidateMatchingService;
use App\Service\CollabApplicationService;
use App\Service\CollabRequestService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/collab', name: 'collab_')]
final class CollabController extends AbstractController
{
    // ── Explore / List ───────────────────────────────────────────────────────

    #[Route('', name: 'index', methods: ['GET'])]
    public function index(Request $request, CollabRequestRepository $repo): Response
    {
        $page      = max(1, (int) $request->query->get('page', 1));
        $paginator = $repo->paginateOpen($page, 9);
        $total     = count($paginator);
        $lastPage  = (int) ceil($total / 9);

        return $this->render('collab/index.html.twig', [
            'requests'  => $paginator,
            'page'      => $page,
            'lastPage'  => max(1, $lastPage),
            'total'     => $total,
        ]);
    }

    // ── Detail ───────────────────────────────────────────────────────────────

    #[Route('/{id}', name: 'show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(int $id, CollabRequestRepository $repo): Response
    {
        $collabRequest = $repo->findWithApplications($id);
        if ($collabRequest === null) {
            throw $this->createNotFoundException('Demande introuvable.');
        }

        return $this->render('collab/show.html.twig', ['collabRequest' => $collabRequest]);
    }

    // ── Publish (FO) ─────────────────────────────────────────────────────────

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function new(Request $request, CollabRequestService $service): Response
    {
        $collabRequest = new CollabRequest();
        $form          = $this->createForm(CollabRequestType::class, $collabRequest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $service->publish($collabRequest, $this->getUser());
                $this->addFlash('success', 'Votre demande a été publiée avec succès.');

                return $this->redirectToRoute('collab_show', ['id' => $collabRequest->getId()]);
            } catch (\DomainException $e) {
                $this->addFlash('danger', $e->getMessage());
            }
        }

        return $this->render('collab/new.html.twig', ['form' => $form]);
    }

    // ── Edit (FO – own request) ──────────────────────────────────────────────

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function edit(int $id, Request $request, CollabRequestRepository $repo, CollabRequestService $service): Response
    {
        $collabRequest = $repo->find($id);
        if ($collabRequest === null) {
            throw $this->createNotFoundException('Demande introuvable.');
        }

        if ($collabRequest->getRequester()?->getId() !== $this->getUser()->getId()) {
            throw $this->createAccessDeniedException('Vous ne pouvez modifier que vos propres demandes.');
        }

        $form = $this->createForm(CollabRequestType::class, $collabRequest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $service->update($collabRequest);
                $this->addFlash('success', 'Demande mise à jour avec succès.');

                return $this->redirectToRoute('collab_show', ['id' => $collabRequest->getId()]);
            } catch (\DomainException $e) {
                $this->addFlash('danger', $e->getMessage());
            }
        }

        return $this->render('collab/edit.html.twig', ['form' => $form, 'collabRequest' => $collabRequest]);
    }

    // ── Apply ────────────────────────────────────────────────────────────────

    #[Route('/{id}/apply', name: 'apply', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function apply(
        int $id,
        Request $request,
        CollabRequestRepository $reqRepo,
        CollabApplicationService $appService,
    ): Response {
        $collabRequest = $reqRepo->find($id);
        if ($collabRequest === null) {
            throw $this->createNotFoundException('Demande introuvable.');
        }

        /** @var \App\Entity\Utilisateur $me */
        $me = $this->getUser();

        $application = new CollabApplication();
        $application->setFullName($me->getPrenom().' '.$me->getNom());
        $application->setEmail($me->getEmail());

        $form = $this->createForm(CollabApplicationType::class, $application);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $appService->apply($application, $collabRequest, $me);
                $this->addFlash('success', 'Votre candidature a été soumise avec succès.');

                return $this->redirectToRoute('collab_my_applications');
            } catch (\DomainException $e) {
                $this->addFlash('danger', $e->getMessage());
            }
        }

        return $this->render('collab/apply.html.twig', [
            'form'          => $form,
            'collabRequest' => $collabRequest,
        ]);
    }

    // ── My requests ──────────────────────────────────────────────────────────

    #[Route('/my/requests', name: 'my_requests', methods: ['GET'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function myRequests(CollabRequestRepository $repo): Response
    {
        $requests = $repo->findByRequester($this->getUser()->getId());

        return $this->render('collab/my_requests.html.twig', ['requests' => $requests]);
    }

    // ── My applications ──────────────────────────────────────────────────────

    #[Route('/my/applications', name: 'my_applications', methods: ['GET'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function myApplications(CollabApplicationRepository $appRepo): Response
    {
        $applications = $appRepo->findByCandidate($this->getUser()->getId());

        return $this->render('collab/my_applications.html.twig', ['applications' => $applications]);
    }

    // ── Delete own request ───────────────────────────────────────────────────

    #[Route('/{id}/delete', name: 'delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function delete(int $id, Request $request, CollabRequestRepository $repo, CollabRequestService $service): Response
    {
        $collabRequest = $repo->find($id);
        if ($collabRequest === null) {
            throw $this->createNotFoundException('Demande introuvable.');
        }

        if ($collabRequest->getRequester()?->getId() !== $this->getUser()->getId()) {
            throw $this->createAccessDeniedException('Vous ne pouvez supprimer que vos propres demandes.');
        }

        if ($this->isCsrfTokenValid('delete_collab_'.$id, $request->request->get('_token'))) {
            $service->delete($collabRequest);
            $this->addFlash('success', 'Demande supprimée.');
        }

        return $this->redirectToRoute('collab_my_requests');
    }
}
