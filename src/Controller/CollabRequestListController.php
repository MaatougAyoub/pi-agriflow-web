<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\CollabRequest;
use App\Repository\CollabRequestRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/collab-requests')]
class CollabRequestListController extends AbstractController
{
    #[Route('', name: 'collab_request_list', methods: ['GET'])]
    public function index(Request $request, CollabRequestRepository $repo): Response
    {
        $search = $request->query->get('search', '');
        $status = $request->query->get('status', 'Tous');
        
        try {
            if ($search || $status !== 'Tous') {
                $requests = $this->filterRequests($repo, $search, $status);
            } else {
                $requests = $repo->findAll();
            }
            
            $resultsCount = count($requests);
            
            return $this->render('collab_request/list.html.twig', [
                'requests' => $requests,
                'search' => $search,
                'status' => $status,
                'resultsCount' => $resultsCount,
                'statuses' => ['Tous', 'PENDING', 'APPROVED', 'REJECTED', 'CLOSED']
            ]);
        } catch (\Exception $e) {
            $this->addFlash('error', 'Impossible de charger les demandes: ' . $e->getMessage());
            return $this->render('collab_request/list.html.twig', [
                'requests' => [],
                'search' => $search,
                'status' => $status,
                'resultsCount' => 0,
                'statuses' => ['Tous', 'PENDING', 'APPROVED', 'REJECTED', 'CLOSED']
            ]);
        }
    }
    
    #[Route('/{id}/details', name: 'collab_request_details', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function details(int $id, CollabRequestRepository $repo): Response
    {
        $request = $repo->find($id);
        if ($request === null) {
            $this->addFlash('warning', 'Aucune sélection', 'Veuillez sélectionner une demande.');
            return $this->redirectToRoute('collab_request_list');
        }
        
        return $this->render('collab_request/details.html.twig', [
            'request' => $request
        ]);
    }
    
    #[Route('/{id}/approve', name: 'collab_request_approve', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function approve(int $id, CollabRequestRepository $repo, Request $request): Response
    {
        $collabRequest = $repo->find($id);
        if ($collabRequest === null) {
            $this->addFlash('warning', 'Aucune sélection', 'Veuillez sélectionner une demande.');
            return $this->redirectToRoute('collab_request_list');
        }
        
        if ($this->isCsrfTokenValid('approve_' . $id, $request->request->get('_token'))) {
            try {
                $collabRequest->setStatus('APPROVED');
                $repo->save($collabRequest, true);
                $this->addFlash('success', 'Demande validée avec succès !');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Impossible de mettre à jour: ' . $e->getMessage());
            }
        }
        
        return $this->redirectToRoute('collab_request_list');
    }
    
    #[Route('/{id}/reject', name: 'collab_request_reject', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function reject(int $id, CollabRequestRepository $repo, Request $request): Response
    {
        $collabRequest = $repo->find($id);
        if ($collabRequest === null) {
            $this->addFlash('warning', 'Aucune sélection', 'Veuillez sélectionner une demande.');
            return $this->redirectToRoute('collab_request_list');
        }
        
        if ($this->isCsrfTokenValid('reject_' . $id, $request->request->get('_token'))) {
            try {
                $collabRequest->setStatus('REJECTED');
                $repo->save($collabRequest, true);
                $this->addFlash('info', 'Demande rejetée');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Impossible de mettre à jour: ' . $e->getMessage());
            }
        }
        
        return $this->redirectToRoute('collab_request_list');
    }
    
    #[Route('/{id}/delete', name: 'collab_request_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(int $id, CollabRequestRepository $repo, Request $request): Response
    {
        $collabRequest = $repo->find($id);
        if ($collabRequest === null) {
            $this->addFlash('warning', 'Aucune sélection', 'Veuillez sélectionner une demande à supprimer.');
            return $this->redirectToRoute('collab_request_list');
        }
        
        if ($this->isCsrfTokenValid('delete_' . $id, $request->request->get('_token'))) {
            try {
                $repo->remove($collabRequest, true);
                $this->addFlash('success', 'Demande supprimée avec succès !');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Impossible de supprimer: ' . $e->getMessage());
            }
        }
        
        return $this->redirectToRoute('collab_request_list');
    }
    
    private function filterRequests(CollabRequestRepository $repo, string $keyword, string $status): array
    {
        $allRequests = $repo->findAll();
        $filteredRequests = [];
        
        foreach ($allRequests as $req) {
            $matchesKeyword = empty($keyword) || 
                (stripos($req->getTitle(), $keyword) !== false) ||
                (stripos($req->getDescription(), $keyword) !== false);
            
            $matchesStatus = $status === 'Tous' || $req->getStatus() === $status;
            
            if ($matchesKeyword && $matchesStatus) {
                $filteredRequests[] = $req;
            }
        }
        
        return $filteredRequests;
    }
}
