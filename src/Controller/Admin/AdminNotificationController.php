<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Reclamation;
use App\Entity\Utilisateur;
use App\Repository\ReclamationRepository;
use App\Repository\UtilisateurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin', name: 'app_admin_')]
#[IsGranted('ROLE_ADMIN')]
final class AdminNotificationController extends AbstractController
{
    #[Route('/notifications', name: 'notifications_index', methods: ['GET'])]
    public function index(
        Request $request,
        ReclamationRepository $reclamationRepository,
        UtilisateurRepository $utilisateurRepository
    ): Response
    {
        $limit = $request->query->getInt('limit', 50);
        $normalizedLimit = max(1, min($limit, 200));

        $reclamationNotifications = array_map(
            function (Reclamation $reclamation): array {
                $author = $reclamation->getUtilisateur();

                return [
                    'type' => 'reclamation',
                    'id' => $reclamation->getId(),
                    'title' => (string) $reclamation->getTitre(),
                    'status' => strtoupper((string) $reclamation->getStatut()),
                    'category' => strtoupper((string) $reclamation->getCategorie()),
                    'author' => $this->formatUserIdentity($author),
                    'content' => (string) $reclamation->getDescription(),
                    'date' => $reclamation->getDateCreation(),
                ];
            },
            $reclamationRepository->findNotificationHistoryForAdmin($normalizedLimit)
        );

        $registrationNotifications = array_map(
            function (Utilisateur $utilisateur): array {
                $role = strtoupper((string) $utilisateur->getRole());
                $normalizedRole = str_starts_with($role, 'ROLE_') ? substr($role, 5) : $role;
                $labelRole = '' !== $normalizedRole ? $normalizedRole : 'UTILISATEUR';

                return [
                    'type' => 'inscription',
                    'id' => $utilisateur->getId(),
                    'title' => $this->formatUserIdentity($utilisateur),
                    'status' => 'NOUVEAU',
                    'category' => $labelRole,
                    'author' => $this->formatUserIdentity($utilisateur),
                    'content' => sprintf(
                        'Inscription de %s (%s).',
                        $this->formatUserIdentity($utilisateur),
                        (string) $utilisateur->getEmail()
                    ),
                    'date' => $utilisateur->getDateCreation(),
                ];
            },
            $utilisateurRepository->findLatestRegistrationsForAdminNotifications($normalizedLimit)
        );

        $notificationsHistory = array_merge($reclamationNotifications, $registrationNotifications);

        usort($notificationsHistory, static function (array $left, array $right): int {
            $leftDate = $left['date'] instanceof \DateTimeInterface ? $left['date']->getTimestamp() : 0;
            $rightDate = $right['date'] instanceof \DateTimeInterface ? $right['date']->getTimestamp() : 0;

            if ($leftDate !== $rightDate) {
                return $rightDate <=> $leftDate;
            }

            return ((int) ($right['id'] ?? 0)) <=> ((int) ($left['id'] ?? 0));
        });

        $notificationsHistory = array_slice($notificationsHistory, 0, $normalizedLimit);

        return $this->render('admin/notifications/index.html.twig', [
            'notifications_history' => $notificationsHistory,
            'pending_count' => $reclamationRepository->countPending(),
            'history_limit' => $normalizedLimit,
        ]);
    }

    private function formatUserIdentity(?Utilisateur $utilisateur): string
    {
        if (!$utilisateur instanceof Utilisateur) {
            return 'Utilisateur inconnu';
        }

        $fullName = trim((string) (($utilisateur->getNom() ?? '').' '.($utilisateur->getPrenom() ?? '')));

        return '' !== $fullName ? $fullName : ((string) $utilisateur->getEmail() ?: 'Utilisateur inconnu');
    }
}
