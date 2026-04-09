<?php

namespace App\Controller;

use App\Entity\CollabApplication;
use App\Entity\CollabRequest;
use App\Form\CollabApplicationType;
use App\Form\CollabRequestType;
use App\Repository\CollabApplicationRepository;
use App\Repository\CollabRequestRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/collaborations')]
class CollaborationController extends AbstractController
{
    private const INDEX_SORTS = ['date_desc', 'date_asc', 'salary_desc', 'salary_asc', 'title_asc'];

    private const MY_REQUESTS_SORTS = ['date_desc', 'date_asc', 'title_asc', 'salary_desc', 'salary_asc'];

    private const MY_APPLICATIONS_SORTS = ['date_desc', 'date_asc', 'status_asc'];

    /**
     * Liste des demandes de collaboration approuvées (publiques)
     */
    #[Route('', name: 'app_collab_index', methods: ['GET'])]
    public function index(Request $request, CollabRequestRepository $repo): Response
    {
        $search = trim((string) $request->query->get('q', ''));
        $listSort = $request->query->get('sort', 'date_desc');
        $location = trim((string) $request->query->get('location', ''));
        if (!\in_array($listSort, self::INDEX_SORTS, true)) {
            $listSort = 'date_desc';
        }

        $locationFilter = $location !== '' ? $location : null;

        if ($search !== '') {
            $requests = $repo->searchFiltered($search, $locationFilter, $listSort);
        } else {
            $requests = $repo->findApprovedFiltered($locationFilter, $listSort);
        }

        return $this->render('collaborations/index.html.twig', [
            'requests' => $requests,
            'search' => $search,
            'list_sort' => $listSort,
            'filter_location' => $location,
        ]);
    }

    /**
     * Créer une nouvelle demande de collaboration
     */
    #[Route('/new', name: 'app_collab_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('warning', 'Vous devez être connecté pour créer une demande.');
            return $this->redirectToRoute('app_login');
        }

        $collabRequest = new CollabRequest();
        $form = $this->createForm(CollabRequestType::class, $collabRequest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $collabRequest->setRequester($user);
            $collabRequest->setPublisher($user->getPrenom() . ' ' . $user->getNom());
            $collabRequest->setStatus('PENDING');
            $collabRequest->setCreatedAt(new \DateTime());
            $collabRequest->setUpdatedAt(new \DateTime());

            $em->persist($collabRequest);
            $em->flush();

            $this->addFlash('success', '✅ Votre demande de collaboration a été créée avec succès ! Elle est en attente de validation par l\'administrateur.');
            return $this->redirectToRoute('app_collab_index');
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            $this->addCollabFormErrorFlash($form);
        }

        return $this->render('collaborations/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Mes demandes de collaboration
     */
    #[Route('/mes-demandes', name: 'app_collab_my_requests', methods: ['GET'])]
    public function myRequests(Request $request, CollabRequestRepository $requestRepo, CollabApplicationRepository $appRepo): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $status = (string) $request->query->get('status', 'ALL');
        $listSort = $request->query->get('sort', 'date_desc');
        if (!\in_array($listSort, self::MY_REQUESTS_SORTS, true)) {
            $listSort = 'date_desc';
        }

        $requests = $requestRepo->findByRequesterFiltered($user, $status, $listSort);

        // Compter les candidatures pour chaque demande
        $applicationCounts = [];
        foreach ($requests as $req) {
            $applicationCounts[$req->getId()] = $appRepo->countByRequest($req);
        }

        return $this->render('collaborations/my_requests.html.twig', [
            'requests' => $requests,
            'applicationCounts' => $applicationCounts,
            'filter_status' => $status,
            'list_sort' => $listSort,
        ]);
    }

    /**
     * Mes candidatures envoyées
     */
    #[Route('/mes-candidatures', name: 'app_collab_my_applications', methods: ['GET'])]
    public function myApplications(Request $request, CollabApplicationRepository $repo): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $status = (string) $request->query->get('status', 'ALL');
        $listSort = $request->query->get('sort', 'date_desc');
        if (!\in_array($listSort, self::MY_APPLICATIONS_SORTS, true)) {
            $listSort = 'date_desc';
        }

        $applications = $repo->findByCandidateFiltered($user, $status, $listSort);

        return $this->render('collaborations/my_applications.html.twig', [
            'applications' => $applications,
            'filter_status' => $status,
            'list_sort' => $listSort,
        ]);
    }

    /**
     * Voir les détails d'une demande
     */
    #[Route('/{id}', name: 'app_collab_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(CollabRequest $collabRequest, CollabApplicationRepository $appRepo): Response
    {
        $user = $this->getUser();
        $hasApplied = false;
        if ($user) {
            $hasApplied = $appRepo->hasApplied($user, $collabRequest);
        }

        $applicationCount = $appRepo->countByRequest($collabRequest);

        return $this->render('collaborations/show.html.twig', [
            'request' => $collabRequest,
            'hasApplied' => $hasApplied,
            'applicationCount' => $applicationCount,
        ]);
    }

    /**
     * Modifier une demande
     */
    #[Route('/{id}/edit', name: 'app_collab_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function edit(CollabRequest $collabRequest, Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if (!$user || $collabRequest->getRequester() !== $user) {
            $this->addFlash('danger', 'Vous ne pouvez modifier que vos propres demandes.');
            return $this->redirectToRoute('app_collab_index');
        }

        $form = $this->createForm(CollabRequestType::class, $collabRequest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $collabRequest->setUpdatedAt(new \DateTime());
            $em->flush();

            $this->addFlash('success', 'La demande a été modifiée avec succès.');
            return $this->redirectToRoute('app_collab_my_requests');
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            $this->addCollabFormErrorFlash($form);
        }

        return $this->render('collaborations/edit.html.twig', [
            'form' => $form->createView(),
            'request' => $collabRequest,
        ]);
    }

    /**
     * Supprimer une demande
     */
    #[Route('/{id}/delete', name: 'app_collab_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(CollabRequest $collabRequest, Request $request, EntityManagerInterface $em, CollabApplicationRepository $appRepo): Response
    {
        $user = $this->getUser();
        if (!$user || $collabRequest->getRequester() !== $user) {
            $this->addFlash('danger', 'Vous ne pouvez supprimer que vos propres demandes.');
            return $this->redirectToRoute('app_collab_index');
        }

        if ($this->isCsrfTokenValid('delete' . $collabRequest->getId(), $request->request->get('_token'))) {
            // Supprimer d'abord les candidatures associées
            $applications = $appRepo->findByRequest($collabRequest);
            foreach ($applications as $app) {
                $em->remove($app);
            }
            $em->remove($collabRequest);
            $em->flush();

            $this->addFlash('success', 'La demande et toutes ses candidatures ont été supprimées.');
        }

        return $this->redirectToRoute('app_collab_my_requests');
    }

    /**
     * Postuler à une demande de collaboration
     */
    #[Route('/{id}/postuler', name: 'app_collab_apply', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function apply(CollabRequest $collabRequest, Request $request, EntityManagerInterface $em, CollabApplicationRepository $appRepo): Response
    {
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('warning', 'Vous devez être connecté pour postuler.');
            return $this->redirectToRoute('app_login');
        }

        // Vérifier qu'on ne postule pas à sa propre demande
        if ($collabRequest->getRequester() === $user) {
            $this->addFlash('warning', 'Vous ne pouvez pas postuler à votre propre demande.');
            return $this->redirectToRoute('app_collab_show', ['id' => $collabRequest->getId()]);
        }

        // Vérifier si déjà postulé
        if ($appRepo->hasApplied($user, $collabRequest)) {
            $this->addFlash('info', 'Vous avez déjà postulé à cette demande.');
            return $this->redirectToRoute('app_collab_show', ['id' => $collabRequest->getId()]);
        }

        $application = new CollabApplication();
        // Pré-remplir avec les infos du profil
        $application->setFullName($user->getPrenom() . ' ' . $user->getNom());
        $application->setEmail($user->getEmail());

        $form = $this->createForm(CollabApplicationType::class, $application);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $application->setRequest($collabRequest);
            $application->setCandidate($user);
            $application->setStatus('PENDING');
            $application->setAppliedAt(new \DateTime());
            $application->setUpdatedAt(new \DateTime());
            $application->setYearsOfExperience((int) $application->getYearsOfExperience());
            $salary = $application->getExpectedSalary();
            $application->setExpectedSalary(number_format((float) ($salary ?? 0), 2, '.', ''));

            $em->persist($application);
            $em->flush();

            $this->addFlash('success', 'Votre candidature a bien été enregistrée. Vous pouvez la suivre dans « Mes candidatures ».');
            return $this->redirectToRoute('app_collab_index');
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            $this->addCollabFormErrorFlash($form);
        }

        return $this->render('collaborations/apply.html.twig', [
            'form' => $form->createView(),
            'request' => $collabRequest,
        ]);
    }

    /**
     * Voir les candidatures reçues pour une de mes demandes
     */
    #[Route('/{id}/candidatures', name: 'app_collab_view_applications', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function viewApplications(CollabRequest $collabRequest, CollabApplicationRepository $appRepo): Response
    {
        $user = $this->getUser();
        if (!$user || $collabRequest->getRequester() !== $user) {
            $this->addFlash('danger', 'Accès refusé.');
            return $this->redirectToRoute('app_collab_index');
        }

        $applications = $appRepo->findByRequest($collabRequest);

        return $this->render('collaborations/view_applications.html.twig', [
            'request' => $collabRequest,
            'applications' => $applications,
        ]);
    }

    /**
     * Accepter ou refuser une candidature
     */
    #[Route('/candidature/{id}/{action}', name: 'app_collab_update_app_status', methods: ['POST'], requirements: ['id' => '\d+', 'action' => 'approve|reject'])]
    public function updateApplicationStatus(
        CollabApplication $application,
        string $action,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        $user = $this->getUser();
        $collabRequest = $application->getRequest();

        if (!$user || $collabRequest->getRequester() !== $user) {
            $this->addFlash('danger', 'Accès refusé.');
            return $this->redirectToRoute('app_collab_index');
        }

        if ($this->isCsrfTokenValid('status' . $application->getId(), $request->request->get('_token'))) {
            if ($action === 'approve') {
                $application->setStatus('APPROVED');
                $this->addFlash('success', 'Candidature acceptée !');
            } else {
                $application->setStatus('REJECTED');
                $this->addFlash('info', 'Candidature refusée.');
            }
            $application->setUpdatedAt(new \DateTime());
            $em->flush();
        }

        return $this->redirectToRoute('app_collab_view_applications', ['id' => $collabRequest->getId()]);
    }

    private function addCollabFormErrorFlash(FormInterface $form): void
    {
        $messages = [];
        foreach ($form->getErrors(true) as $error) {
            $messages[] = $error->getMessage();
        }
        $messages = array_values(array_unique($messages));
        $this->addFlash(
            'danger',
            $messages !== []
                ? implode(' ', $messages)
                : 'Le formulaire contient des erreurs. Vérifiez les champs affichés ci-dessous.'
        );
    }
}
