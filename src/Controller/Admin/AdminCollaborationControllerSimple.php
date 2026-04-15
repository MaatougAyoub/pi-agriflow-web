<?php

namespace App\Controller\Admin;

use App\Entity\CollabRequest;
use App\Repository\CollabApplicationRepository;
use App\Repository\CollabRequestRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/collaborations-simple')]
class AdminCollaborationControllerSimple extends AbstractController
{
    #[Route('', name: 'app_admin_collab_simple_index', methods: ['GET'])]
    public function index(CollabRequestRepository $requestRepo, CollabApplicationRepository $appRepo): Response
    {
        try {
            // Récupérer TOUTES les demandes sans filtre
            $allRequests = $requestRepo->findAll();
            
            // Compter les candidatures pour chaque demande
            $applicationCounts = [];
            foreach ($allRequests as $req) {
                try {
                    $applicationCounts[$req->getId()] = $appRepo->countByRequest($req);
                } catch (\Exception $e) {
                    $applicationCounts[$req->getId()] = 0;
                }
            }

            return $this->render('admin/collaborations/simple_index.html.twig', [
                'requests' => $allRequests,
                'applicationCounts' => $applicationCounts,
                'totalRequests' => count($allRequests),
            ]);
            
        } catch (\Exception $e) {
            // En cas d'erreur, afficher les détails
            return $this->render('admin/collaborations/error.html.twig', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    #[Route('/{id}/approve', name: 'app_admin_collab_simple_approve', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function approve(
        CollabRequest $collabRequest,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        if ($this->isCsrfTokenValid('approve_' . $collabRequest->getId(), $request->request->get('_token'))) {
            $collabRequest->setStatus('APPROVED');
            $collabRequest->setUpdatedAt(new \DateTime());
            $em->flush();
            $this->addFlash('success', 'Demande approuvée avec succès.');
        }

        return $this->redirectToRoute('app_admin_collab_simple_index');
    }

    #[Route('/{id}/reject', name: 'app_admin_collab_simple_reject', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function reject(
        CollabRequest $collabRequest,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        if ($this->isCsrfTokenValid('reject_' . $collabRequest->getId(), $request->request->get('_token'))) {
            $collabRequest->setStatus('REJECTED');
            $collabRequest->setUpdatedAt(new \DateTime());
            $em->flush();
            $this->addFlash('info', 'Demande rejetée.');
        }

        return $this->redirectToRoute('app_admin_collab_simple_index');
    }
}
