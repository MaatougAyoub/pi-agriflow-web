<?php

namespace App\Controller;

use App\Entity\CollabApplication;
use App\Entity\CollabRequest;
use App\Form\CollabApplicationType;
use App\Form\CollabRequestType;
use App\Repository\CollabApplicationRepository;
use App\Repository\CollabRequestRepository;
use App\Service\WeatherService;
use App\Service\CandidateMatchingService;
use App\Service\GeminiAIService;
use App\Model\MatchScore;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Nucleos\DompdfBundle\Wrapper\DompdfWrapperInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
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
    public function index(Request $request, CollabRequestRepository $repo, PaginatorInterface $paginator): Response
    {
        $search = trim((string) $request->query->get('q', ''));
        $listSort = $request->query->get('sort', 'date_desc');
        $location = trim((string) $request->query->get('location', ''));
        if (!\in_array($listSort, self::INDEX_SORTS, true)) {
            $listSort = 'date_desc';
        }

        $locationFilter = $location !== '' ? $location : null;

        if ($search !== '') {
            $query = $repo->searchFilteredQuery($search, $locationFilter, $listSort);
        } else {
            $query = $repo->findApprovedFilteredQuery($locationFilter, $listSort);
        }

        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            6 // 6 items per page
        );

        return $this->render('collaborations/index.html.twig', [
            'pagination' => $pagination,
            'search' => $search,
            'list_sort' => $listSort,
            'filter_location' => $location,
        ]);
    }

    /**
     * Créer une nouvelle demande de collaboration
     */
    #[Route('/new', name: 'app_collab_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em, GeminiAIService $gemini): Response
    {
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('warning', 'Vous devez être connecté pour créer une demande.');
            return $this->redirectToRoute('app_login');
        }

        $collabRequest = new CollabRequest();
        $form = $this->createForm(CollabRequestType::class, $collabRequest);
        $form->handleRequest($request);

        // Debug: Vérifier si le formulaire est soumis
        if ($form->isSubmitted()) {
            $localReason = $this->detectBlockedContentReason(
                (string) $collabRequest->getTitle(),
                (string) $collabRequest->getDescription()
            );
            if ($localReason !== null) {
                $moderationError = '🤖 IA Modération : Votre annonce a été rejetée. Motif : '.$localReason;
                $form->addError(new FormError($moderationError));
                $this->addFlash('danger', $moderationError);

                return $this->render('collaborations/new.html.twig', [
                    'form' => $form->createView(),
                    'moderation_error' => $moderationError,
                ]);
            }

            if ($form->isValid()) {
                try {
                    $collabRequest->setRequester($user);
                    $collabRequest->setPublisher($user->getPrenom() . ' ' . $user->getNom());
                    $collabRequest->setStatus('PENDING');
                    $collabRequest->setCreatedAt(new \DateTime());
                    $collabRequest->setUpdatedAt(new \DateTime());

                    // IA : Modération de contenu (Métier 3)
                    $rejectionReason = $gemini->moderateContent($collabRequest->getTitle(), $collabRequest->getDescription());
                    if ($rejectionReason) {
                        $moderationError = '🤖 IA Modération : Votre annonce a été rejetée. Motif : '.$rejectionReason;
                        $form->addError(new FormError($moderationError));
                        $this->addFlash('danger', $moderationError);

                        return $this->render('collaborations/new.html.twig', [
                            'form' => $form->createView(),
                            'moderation_error' => $moderationError,
                        ]);
                    }

                    $em->persist($collabRequest);
                    $em->flush();

                    $this->addFlash('success', '✅ Votre demande a été créée ! Elle est en attente de validation par l\'administrateur.');
                    return $this->redirectToRoute('app_collab_index');
                } catch (\Exception $e) {
                    $this->addFlash('danger', '❌ Erreur lors de l\'enregistrement : ' . $e->getMessage());
                }
            } else {
                $this->addCollabFormErrorFlash($form);
            }
        }

        return $this->render('collaborations/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Mes demandes de collaboration
     */
    #[Route('/mes-demandes', name: 'app_collab_my_requests', methods: ['GET'])]
    public function myRequests(Request $request, CollabRequestRepository $requestRepo, CollabApplicationRepository $appRepo, PaginatorInterface $paginator): Response
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

        $query = $requestRepo->findByRequesterFilteredQuery($user, $status, $listSort);
        
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            8
        );

        // Compter les candidatures pour chaque demande (en optimisant si possible, ici on garde simple)
        $applicationCounts = [];
        foreach ($pagination->getItems() as $req) {
            $applicationCounts[$req->getId()] = $appRepo->countByRequest($req);
        }

        return $this->render('collaborations/my_requests.html.twig', [
            'pagination' => $pagination,
            'applicationCounts' => $applicationCounts,
            'filter_status' => $status,
            'list_sort' => $listSort,
        ]);
    }

    /**
     * Mes candidatures envoyées
     */
    #[Route('/mes-candidatures', name: 'app_collab_my_applications', methods: ['GET'])]
    public function myApplications(Request $request, CollabApplicationRepository $repo, PaginatorInterface $paginator): Response
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

        $query = $repo->findByCandidateFilteredQuery($user, $status, $listSort);

        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('collaborations/my_applications.html.twig', [
            'pagination' => $pagination,
            'filter_status' => $status,
            'list_sort' => $listSort,
        ]);
    }

    /**
     * Voir les détails d'une demande
     */
    #[Route('/{id}', name: 'app_collab_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(CollabRequest $collabRequest, CollabApplicationRepository $appRepo, WeatherService $weatherService): Response
    {
        $user = $this->getUser();
        $hasApplied = false;
        if ($user) {
            $hasApplied = $appRepo->hasApplied($user, $collabRequest);
        }

        $applicationCount = $appRepo->countByRequest($collabRequest);

        // API : Météo (API 1)
        $forecasts = $weatherService->getForecast(
            $collabRequest->getLatitude(),
            $collabRequest->getLongitude(),
            $collabRequest->getStartDate(),
            $collabRequest->getEndDate()
        );

        // Métier 4 : Évaluation des risques météo
        $weatherRisks = $weatherService->getRiskAssessment($forecasts);

        return $this->render('collaborations/show.html.twig', [
            'request' => $collabRequest,
            'hasApplied' => $hasApplied,
            'applicationCount' => $applicationCount,
            'forecasts' => $forecasts,
            'weatherRisks' => $weatherRisks,
        ]);
    }

    /**
     * Modifier une demande
     */
    #[Route('/{id}/edit', name: 'app_collab_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function edit(CollabRequest $collabRequest, Request $request, EntityManagerInterface $em, GeminiAIService $gemini): Response
    {
        $user = $this->getUser();
        if (!$user || $collabRequest->getRequester()?->getId() !== $user->getId()) {
            $this->addFlash('danger', 'Vous ne pouvez modifier que vos propres demandes.');
            return $this->redirectToRoute('app_collab_index');
        }

        $form = $this->createForm(CollabRequestType::class, $collabRequest);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $localReason = $this->detectBlockedContentReason(
                (string) $collabRequest->getTitle(),
                (string) $collabRequest->getDescription()
            );
            if ($localReason !== null) {
                $moderationError = '🤖 IA Modération : Votre annonce a été rejetée. Motif : '.$localReason;
                $form->addError(new FormError($moderationError));
                $this->addFlash('danger', $moderationError);

                return $this->render('collaborations/edit.html.twig', [
                    'form' => $form->createView(),
                    'request' => $collabRequest,
                    'moderation_error' => $moderationError,
                ]);
            }
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $rejectionReason = $gemini->moderateContent($collabRequest->getTitle(), $collabRequest->getDescription());
            if ($rejectionReason) {
                $moderationError = '🤖 IA Modération : Votre annonce a été rejetée. Motif : '.$rejectionReason;
                $form->addError(new FormError($moderationError));
                $this->addFlash('danger', $moderationError);

                return $this->render('collaborations/edit.html.twig', [
                    'form' => $form->createView(),
                    'request' => $collabRequest,
                    'moderation_error' => $moderationError,
                ]);
            }

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
        if (!$user || $collabRequest->getRequester()?->getId() !== $user->getId()) {
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
        if ($collabRequest->getRequester()?->getId() === $user->getId()) {
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
            try {
                $application->setRequest($collabRequest);
                $application->setCandidate($user);
                $application->setStatus('PENDING');
                $application->setAppliedAt(new \DateTime());
                $application->setUpdatedAt(new \DateTime());
                
                // Assurer des valeurs numériques valides
                $exp = $form->get('yearsOfExperience')->getData();
                $application->setYearsOfExperience((int) ($exp ?? 0));
                
                $salary = $form->get('expectedSalary')->getData();
                $application->setExpectedSalary((float) ($salary ?? 0));

                $em->persist($application);
                $em->flush();

                $this->addFlash('success', '✅ Félicitations ! Votre candidature pour "' . $collabRequest->getTitle() . '" a été envoyée avec succès.');
                return $this->redirectToRoute('app_collab_index');
            } catch (\Exception $e) {
                $this->addFlash('danger', '❌ Une erreur technique est survenue : ' . $e->getMessage());
            }
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash('warning', '⚠️ Le formulaire contient des erreurs. Veuillez vérifier les champs en rouge.');
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
    public function viewApplications(CollabRequest $collabRequest, CollabApplicationRepository $appRepo, CandidateMatchingService $matchingService, GeminiAIService $gemini): Response
    {
        $user = $this->getUser();
        if (!$user || $collabRequest->getRequester()?->getId() !== $user->getId()) {
            $this->addFlash('danger', 'Accès refusé.');
            return $this->redirectToRoute('app_collab_index');
        }

        $applications = $appRepo->findByRequest($collabRequest);
        
        // Métier 2 : Ranking intelligent
        $rankedScores = $matchingService->rankApplications($applications, $collabRequest);

        // IA : Analyse de fit pour le meilleur candidat (IA 2)
        $aiAnalyses = [];
        foreach ($applications as $app) {
            // On ne fait l'analyse IA que pour les scores > 50% pour économiser le quota, ou sur demande
            $score = 0;
            foreach ($rankedScores as $rs) {
                if ($rs->getApplication()->getId() === $app->getId()) { $score = $rs->getTotalScore(); break; }
            }
            if ($score > 50) {
                $aiAnalyses[$app->getId()] = $gemini->analyzeCandidateFit($collabRequest->getDescription(), $app->getMotivation());
            }
        }

        return $this->render('collaborations/view_applications.html.twig', [
            'request' => $collabRequest,
            'rankedScores' => $rankedScores,
            'aiAnalyses' => $aiAnalyses,
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
        EntityManagerInterface $em,
        CollabApplicationRepository $appRepo
    ): Response {
        $user = $this->getUser();
        $collabRequest = $application->getRequest();

        if (!$user || $collabRequest->getRequester() !== $user) {
            $this->addFlash('danger', 'Accès refusé.');
            return $this->redirectToRoute('app_collab_index');
        }

        if ($this->isCsrfTokenValid('status' . $application->getId(), $request->request->get('_token'))) {
            if ($action === 'approve') {
                // Métier 1 : Gestion des conflits (Ne pas dépasser le nombre de personnes nécessaires)
                $approvedCount = $appRepo->countApprovedByRequest($collabRequest);
                if ($approvedCount >= $collabRequest->getNeededPeople()) {
                    $this->addFlash('warning', '⚠️ Le nombre maximum de collaborateurs a déjà été atteint pour cette demande.');
                    return $this->redirectToRoute('app_collab_view_applications', ['id' => $collabRequest->getId()]);
                }

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

    /**
     * Métier 5 & Bundle 2 : Export PDF d'une demande (Dompdf)
     */
    #[Route('/{id}/export-pdf', name: 'app_collab_export_pdf', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function exportPdf(CollabRequest $collabRequest, DompdfWrapperInterface $dompdf): Response
    {
        $user = $this->getUser();
        if (!$user) return $this->redirectToRoute('app_login');

        $html = $this->renderView('collaborations/pdf_report.html.twig', [
            'request' => $collabRequest,
            'exportDate' => new \DateTime(),
        ]);

        $pdfContent = $dompdf->getPdf($html, [
            'paper' => 'A4',
            'orientation' => 'portrait',
        ]);

        return new Response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="rapport-collaboration-'.$collabRequest->getId().'.pdf"',
        ]);
    }

    /**
     * Améliorer la description via IA (AJAX) - IA 1
     */
    #[Route('/ai/improve-description', name: 'app_collab_ai_improve', methods: ['POST'])]
    public function aiImproveDescription(Request $request, GeminiAIService $gemini): Response
    {
        $data = json_decode($request->getContent(), true);
        $title = $data['title'] ?? '';
        $description = $data['description'] ?? '';

        if (empty($title) || empty($description)) {
            return $this->json(['error' => 'Titre et description requis'], 400);
        }

        try {
            $improved = $gemini->improveDescription($title, $description);
            // Si l'IA renvoie la même chose ou échoue silencieusement, on garde l'original
            return $this->json(['improved' => $improved]);
        } catch (\Exception $e) {
            return $this->json(['error' => 'IA indisponible pour le moment'], 500);
        }
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

    private function getFormErrors(FormInterface $form): array
    {
        $errors = [];
        foreach ($form->getErrors(true) as $error) {
            $errors[] = [
                'message' => $error->getMessage(),
                'field' => $error->getOrigin()?->getName() ?? 'unknown',
            ];
        }
        return $errors;
    }

    private function detectBlockedContentReason(string $title, string $description): ?string
    {
        $text = mb_strtolower(trim($title.' '.$description));
        if ($text === '') {
            return null;
        }

        $offTopicPatterns = [
            'crypto', 'cryptomonnaie', 'cryptomonnaies', 'bitcoin', 'ethereum',
            'iphone', 'samsung', 'playstation', 'casino', 'forex',
        ];
        foreach ($offTopicPatterns as $pattern) {
            if (str_contains($text, $pattern)) {
                return sprintf('Hors sujet pour AgriFlow : contenu détecté "%s".', $pattern);
            }
        }

        $spamPatterns = [
            'arnaque', 'escroquerie', 'fraude', 'spam',
            'gagnez', 'argent facile', 'revenu rapide',
        ];
        foreach ($spamPatterns as $pattern) {
            if (str_contains($text, $pattern)) {
                return sprintf('Contenu suspect ou inapproprié détecté : "%s".', $pattern);
            }
        }

        return null;
    }
}
