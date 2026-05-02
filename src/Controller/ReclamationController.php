<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Reclamation;
use App\Entity\Utilisateur;
use App\Form\ReclamationType;
use App\Repository\ReclamationRepository;
use App\Service\NotificationService;
use App\Service\ReclamationAiAssistantService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/reclamations', name: 'app_reclamation_')]
final class ReclamationController extends AbstractController
{
    private const CATEGORIES = ['TECHNIQUE', 'ACCESS', 'DELIVERY', 'PAIMENT', 'SERVICE', 'AUTRE'];

    #[Route('', name: 'index', methods: ['GET', 'POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function index(
        Request $request,
        ReclamationRepository $reclamationRepository,
        EntityManagerInterface $entityManager,
        PaginatorInterface $paginator,
        NotificationService $notificationService
    ): Response
    {
        $user = $this->getAuthenticatedUser();

        $activeTab = strtolower(trim((string) $request->query->get('tab', 'liste')));
        if (!in_array($activeTab, ['liste', 'ajouter'], true)) {
            $activeTab = 'liste';
        }

        $newReclamation = new Reclamation();
        $newReclamation->setUtilisateur($user);
        $newReclamation->setStatut('EN_ATTENTE');

        $form = $this->createForm(ReclamationType::class, $newReclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $activeTab = 'ajouter';
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $newReclamation->setUtilisateur($user);
            $newReclamation->setCategorie(strtoupper((string) $newReclamation->getCategorie()));
            $newReclamation->setDateCreation(new \DateTime());

            $entityManager->persist($newReclamation);
            $entityManager->flush();

            if ($this->isGranted('ROLE_AGRICULTEUR') || $this->isGranted('ROLE_EXPERT')) {
                $notificationService->notifyNewReclamation($newReclamation);
            }

            $this->addFlash('success', 'Reclamation ajoutee avec succes.');

            return $this->redirectToRoute('app_reclamation_index', ['tab' => 'ajouter']);
        }

        $query = trim((string) $request->query->get('q', ''));
        $selectedCategory = strtoupper(trim((string) $request->query->get('categorie', 'TOUTES')));
        if ($selectedCategory === '') {
            $selectedCategory = 'TOUTES';
        }

        if ($selectedCategory !== 'TOUTES' && !in_array($selectedCategory, self::CATEGORIES, true)) {
            $selectedCategory = 'TOUTES';
        }

        $reclamations = $paginator->paginate(
            $reclamationRepository->createSearchWithUserQueryBuilder(
                $query !== '' ? $query : null,
                $selectedCategory !== 'TOUTES' ? $selectedCategory : null
            ),
            $request->query->getInt('page', 1),
            8
        );

        $connectedUserId = $user->getId();
        $connectedIsAdmin = $this->isGranted('ROLE_ADMIN');
        $showActionsColumn = $connectedIsAdmin;

        if (!$showActionsColumn) {
            foreach ($reclamations as $reclamation) {
                if ($reclamation->getUtilisateur()?->getId() === $connectedUserId) {
                    $showActionsColumn = true;
                    break;
                }
            }
        }

        return $this->render('reclamation/index.html.twig', [
            'reclamations' => $reclamations,
            'search_query' => $query,
            'selected_category' => $selectedCategory,
            'categories' => self::CATEGORIES,
            'form' => $form->createView(),
            'active_tab' => $activeTab,
            'connected_user_id' => $connectedUserId,
            'connected_is_admin' => $connectedIsAdmin,
            'show_actions_column' => $showActionsColumn,
            'connected_identity' => $this->buildConnectedIdentity(),
        ]);
    }

    #[Route('/ajouter', name: 'add', methods: ['GET'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function add(): Response
    {
        return $this->redirectToRoute('app_reclamation_index', ['tab' => 'ajouter']);
    }

    #[Route('/admin/pending-summary', name: 'admin_pending_summary', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function adminPendingSummary(ReclamationRepository $reclamationRepository): JsonResponse
    {
        $count = $reclamationRepository->countPending();
        $latest = $reclamationRepository->findLatestPending();
        $author = $latest?->getUtilisateur();

        return new JsonResponse([
            'count' => $count,
            'latest_id' => $latest?->getId(),
            'latest_titre' => $latest?->getTitre(),
            'latest_date' => $latest?->getDateCreation()?->format('d/m/Y H:i'),
            'latest_auteur' => trim((string) (($author?->getNom() ?? '').' '.($author?->getPrenom() ?? ''))),
        ]);
    }

    #[Route('/generer-description', name: 'generate_description', methods: ['POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function generateDescription(Request $request, ReclamationAiAssistantService $reclamationAiAssistantService): JsonResponse
    {
        if (!$this->isCsrfTokenValid('reclamation_ai_generate', (string) $request->request->get('_token'))) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Jeton CSRF invalide.',
            ], Response::HTTP_FORBIDDEN);
        }

        $title = trim((string) $request->request->get('titre', ''));
        if ($title === '') {
            return new JsonResponse([
                'success' => false,
                'message' => 'Veuillez entrer un titre avant de generer une description.',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $description = $reclamationAiAssistantService->generateDescriptionFromTitle($title);

            return new JsonResponse([
                'success' => true,
                'description' => $description,
            ]);
        } catch (\DomainException $exception) {
            return new JsonResponse([
                'success' => false,
                'message' => $exception->getMessage(),
            ], Response::HTTP_SERVICE_UNAVAILABLE);
        } catch (\Throwable) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Generation IA indisponible pour le moment.',
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}/supprimer', name: 'delete', methods: ['POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function delete(Reclamation $reclamation, Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getAuthenticatedUser();

        if (!$this->isCsrfTokenValid('delete_reclamation_' . $reclamation->getId(), (string) $request->request->get('_token'))) {
            $this->addFlash('error', 'Jeton CSRF invalide.');

            return $this->redirectToRoute('app_reclamation_index');
        }

        $ownerId = $reclamation->getUtilisateur()?->getId();
        $canDelete = $this->isGranted('ROLE_ADMIN') || ($ownerId !== null && $ownerId === $user->getId());

        if (!$canDelete) {
            $this->addFlash('error', 'Vous n\'avez pas le droit de supprimer cette reclamation.');

            return $this->redirectToRoute('app_reclamation_index');
        }

        $entityManager->remove($reclamation);
        $entityManager->flush();

        $this->addFlash('success', 'Reclamation supprimee avec succes.');

        return $this->redirectToRoute('app_reclamation_index');
    }

    #[Route('/{id}/repondre', name: 'reply', methods: ['POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function reply(Reclamation $reclamation, Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('error', 'Seul un administrateur peut repondre aux reclamations.');

            return $this->redirectToRoute('app_reclamation_index');
        }

        if (!$this->isCsrfTokenValid('reply_reclamation_' . $reclamation->getId(), (string) $request->request->get('_token'))) {
            $this->addFlash('error', 'Jeton CSRF invalide.');

            return $this->redirectToRoute('app_reclamation_index');
        }

        $message = trim((string) $request->request->get('reponse_message', ''));
        if ($message === '') {
            $this->addFlash('error', 'La reponse est obligatoire.');

            return $this->redirectToRoute('app_reclamation_index');
        }

        $formatted = $this->buildConnectedIdentity() . ' : ' . $message;
        $existingResponse = trim((string) $reclamation->getReponse());
        $newResponse = $existingResponse !== '' ? $existingResponse . "\n" . $formatted : $formatted;

        $reclamation->setReponse($newResponse);

        $reclamation->setStatut('TRAITE');

        $entityManager->flush();

        $this->addFlash('success', 'Reponse enregistree avec succes.');

        return $this->redirectToRoute('app_reclamation_index');
    }

    private function getAuthenticatedUser(): Utilisateur
    {
        $user = $this->getUser();

        if (!$user instanceof Utilisateur) {
            throw $this->createAccessDeniedException('Utilisateur non connecte.');
        }

        return $user;
    }

    private function buildConnectedIdentity(): string
    {
        $user = $this->getAuthenticatedUser();
        $role = strtoupper((string) $user->getRole());
        if (str_starts_with($role, 'ROLE_')) {
            $role = substr($role, 5);
        }

        return trim((string) ($user->getNom() . ' ' . $user->getPrenom())) . ' (' . $role . ')';
    }
}
