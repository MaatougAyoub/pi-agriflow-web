<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\CollabApplication;
use App\Entity\CollabRequest;
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

#[Route('/admin/collab', name: 'admin_collab_')]
#[IsGranted('ROLE_ADMIN')]
final class CollabAdminController extends AbstractController
{
    // ── Requests list ────────────────────────────────────────────────────────

    #[Route('/requests', name: 'requests', methods: ['GET'])]
    public function requests(Request $request, CollabRequestRepository $repo): Response
    {
        $page      = max(1, (int) $request->query->get('page', 1));
        $status    = $request->query->get('status', '');
        $paginator = $repo->paginateAll($page, 15, $status ?: null);
        $total     = count($paginator);
        $lastPage  = (int) ceil($total / 15);

        return $this->render('admin/collab/requests.html.twig', [
            'requests'  => $paginator,
            'page'      => $page,
            'lastPage'  => max(1, $lastPage),
            'total'     => $total,
            'statuses'  => CollabRequest::STATUSES,
            'current_status' => $status,
        ]);
    }

    // ── Request detail (with applications + ranking) ─────────────────────────

    #[Route('/requests/{id}', name: 'request_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function requestShow(
        int $id,
        CollabRequestRepository $repo,
        CandidateMatchingService $matchingService,
    ): Response {
        $collabRequest = $repo->findWithApplications($id);
        if ($collabRequest === null) {
            throw $this->createNotFoundException('Demande introuvable.');
        }

        $applications = $collabRequest->getApplications()->toArray();
        $ranking      = $matchingService->rankApplications($applications, $collabRequest);

        return $this->render('admin/collab/request_show.html.twig', [
            'collabRequest' => $collabRequest,
            'ranking'       => $ranking,
            'statuses'      => CollabApplication::STATUSES,
        ]);
    }

    // ── Change request status ────────────────────────────────────────────────

    #[Route('/requests/{id}/status', name: 'request_status', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function changeRequestStatus(
        int $id,
        Request $request,
        CollabRequestRepository $repo,
        CollabRequestService $service,
    ): Response {
        $collabRequest = $repo->find($id);
        if ($collabRequest === null) {
            throw $this->createNotFoundException('Demande introuvable.');
        }

        $newStatus = $request->request->get('status', '');
        if ($this->isCsrfTokenValid('req_status_'.$id, $request->request->get('_token'))) {
            try {
                $service->changeStatus($collabRequest, $newStatus);
                $this->addFlash('success', 'Statut de la demande mis à jour.');
            } catch (\Throwable $e) {
                $this->addFlash('danger', $e->getMessage());
            }
        }

        return $this->redirectToRoute('admin_collab_request_show', ['id' => $id]);
    }

    // ── Delete request (admin) ───────────────────────────────────────────────

    #[Route('/requests/{id}/delete', name: 'request_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function deleteRequest(
        int $id,
        Request $request,
        CollabRequestRepository $repo,
        CollabRequestService $service,
    ): Response {
        $collabRequest = $repo->find($id);
        if ($collabRequest === null) {
            throw $this->createNotFoundException('Demande introuvable.');
        }

        if ($this->isCsrfTokenValid('admin_delete_req_'.$id, $request->request->get('_token'))) {
            $service->delete($collabRequest);
            $this->addFlash('success', 'Demande supprimée.');
        }

        return $this->redirectToRoute('admin_collab_requests');
    }

    // ── Applications list ────────────────────────────────────────────────────

    #[Route('/applications', name: 'applications', methods: ['GET'])]
    public function applications(Request $request, CollabApplicationRepository $repo): Response
    {
        $page      = max(1, (int) $request->query->get('page', 1));
        $status    = $request->query->get('status', '');
        $paginator = $repo->paginateAll($page, 15, $status ?: null);
        $total     = count($paginator);
        $lastPage  = (int) ceil($total / 15);

        return $this->render('admin/collab/applications.html.twig', [
            'applications'   => $paginator,
            'page'           => $page,
            'lastPage'       => max(1, $lastPage),
            'total'          => $total,
            'statuses'       => CollabApplication::STATUSES,
            'current_status' => $status,
        ]);
    }

    // ── Change application status ────────────────────────────────────────────

    #[Route('/applications/{id}/status', name: 'application_status', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function changeApplicationStatus(
        int $id,
        Request $request,
        CollabApplicationRepository $repo,
        CollabApplicationService $service,
    ): Response {
        $application = $repo->find($id);
        if ($application === null) {
            throw $this->createNotFoundException('Candidature introuvable.');
        }

        $newStatus   = $request->request->get('status', '');
        $redirectUrl = $request->request->get('redirect', null);

        if ($this->isCsrfTokenValid('app_status_'.$id, $request->request->get('_token'))) {
            try {
                $service->updateStatus($application, $newStatus);
                $this->addFlash('success', 'Statut de la candidature mis à jour.');
            } catch (\Throwable $e) {
                $this->addFlash('danger', $e->getMessage());
            }
        }

        if ($redirectUrl !== null) {
            return $this->redirect($redirectUrl);
        }

        return $this->redirectToRoute('admin_collab_applications');
    }

    // ── Delete application ───────────────────────────────────────────────────

    #[Route('/applications/{id}/delete', name: 'application_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function deleteApplication(
        int $id,
        Request $request,
        CollabApplicationRepository $repo,
        CollabApplicationService $service,
    ): Response {
        $application = $repo->find($id);
        if ($application === null) {
            throw $this->createNotFoundException('Candidature introuvable.');
        }

        if ($this->isCsrfTokenValid('admin_delete_app_'.$id, $request->request->get('_token'))) {
            $service->delete($application);
            $this->addFlash('success', 'Candidature supprimée.');
        }

        return $this->redirectToRoute('admin_collab_applications');
    }
}
