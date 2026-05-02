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

#[Route('/admin/collaborations')]
class AdminCollaborationController extends AbstractController
{
    /**
     * Dashboard admin : toutes les demandes de collaboration avec filtres et tri
     */
    #[Route('', name: 'app_admin_collab_index', methods: ['GET'])]
    public function index(Request $request, CollabRequestRepository $requestRepo, CollabApplicationRepository $appRepo): Response
    {
        $filterStatus = $request->query->get('status', '');
        $sortField = $request->query->get('sort', 'createdAt');
        $sortDirParam = $request->query->get('dir', 'DESC');
        $sortDir = is_string($sortDirParam) ? $sortDirParam : 'DESC';
        $search = $request->query->get('q', '');

        // Valider le tri
        $allowedSorts = ['createdAt', 'title', 'salary', 'startDate', 'neededPeople'];
        if (!in_array($sortField, $allowedSorts)) {
            $sortField = 'createdAt';
        }
        $sortDir = strtoupper($sortDir) === 'ASC' ? 'ASC' : 'DESC';

        $qb = $requestRepo->createQueryBuilder('r');

        // Filtre par statut
        if ($filterStatus && in_array($filterStatus, ['PENDING', 'APPROVED', 'REJECTED'])) {
            $qb->andWhere('r.status = :status')->setParameter('status', $filterStatus);
        }

        // Recherche
        if ($search) {
            $qb->andWhere('r.title LIKE :q OR r.publisher LIKE :q OR r.location LIKE :q')
               ->setParameter('q', '%' . $search . '%');
        }

        // Tri
        $qb->orderBy('r.' . $sortField, $sortDir);

        $requests = $qb->getQuery()->getResult();

        // Stats globales (sans filtre)
        $allRequests = $requestRepo->createQueryBuilder('r')
            ->select('r')
            ->orderBy('r.createdAt', 'DESC')
            ->getQuery()
            ->getResult();

        $applicationCounts = [];
        foreach ($requests as $req) {
            $applicationCounts[$req->getId()] = $appRepo->countByRequest($req);
        }

        return $this->render('admin/collaborations/index.html.twig', [
            'requests' => $requests,
            'allRequests' => $allRequests,
            'applicationCounts' => $applicationCounts,
            'filterStatus' => $filterStatus,
            'sortField' => $sortField,
            'sortDir' => $sortDir,
            'search' => $search,
        ]);
    }

    /**
     * Approuver ou rejeter une demande (admin)
     */
    #[Route('/{id}/status/{action}', name: 'app_admin_collab_update_status', methods: ['POST'], requirements: ['id' => '\d+', 'action' => 'approve|reject'])]
    public function updateStatus(
        CollabRequest $collabRequest,
        string $action,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        $token = $request->request->get('_token');
        $token = is_string($token) ? $token : null;

        if ($this->isCsrfTokenValid('admin_status' . $collabRequest->getId(), $token)) {
            if ($action === 'approve') {
                $collabRequest->setStatus('APPROVED');
                $this->addFlash('success', 'Demande approuvée avec succès.');
            } else {
                $collabRequest->setStatus('REJECTED');
                $this->addFlash('info', 'Demande rejetée.');
            }
            $collabRequest->setUpdatedAt(new \DateTime());
            $em->flush();
        }

        return $this->redirectToRoute('app_admin_collab_index');
    }
}
