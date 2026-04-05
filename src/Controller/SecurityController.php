<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

final class SecurityController extends AbstractController
{
    #[Route('/login', name: 'app_login', methods: ['GET', 'POST'])]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_post_login_redirect');
        }

        return $this->render('security/login.html.twig', [
            'last_username' => $authenticationUtils->getLastUsername(),
            'error' => $authenticationUtils->getLastAuthenticationError(),
        ]);
    }

    #[Route('/logout', name: 'app_logout', methods: ['GET'])]
    public function logout(): never
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route('/post-login', name: 'app_post_login_redirect', methods: ['GET'])]
    public function postLoginRedirect(): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $roles = $this->resolveUserRoles($user);

        if (in_array('ROLE_ADMIN', $roles, true)) {
            $this->addFlash('login_status', 'Connecte comme administrateur.');

            return $this->redirectToRoute('app_admin_dashboard');
        }

        if (in_array('ROLE_AGRICULTEUR', $roles, true)) {
            $this->addFlash('login_status', 'Connecte comme agriculteur.');

            return $this->redirectToRoute('app_marketplace_index');
        }

        if (in_array('ROLE_EXPERT', $roles, true)) {
            $this->addFlash('login_status', 'Connecte comme expert.');

            return $this->redirectToRoute('site_home');
        }

        $this->addFlash('login_status', 'Connecte avec votre compte.');

        return $this->redirectToRoute('site_home');
    }

    /**
     * @return string[]
     */
    private function resolveUserRoles(object $user): array
    {
        $roles = [];

        if (method_exists($user, 'getRoles')) {
            foreach ((array) $user->getRoles() as $role) {
                $normalizedRole = strtoupper(trim((string) $role));

                if ('' === $normalizedRole) {
                    continue;
                }

                $roles[] = str_starts_with($normalizedRole, 'ROLE_')
                    ? $normalizedRole
                    : 'ROLE_'.$normalizedRole;
            }
        }

        if (method_exists($user, 'getRole')) {
            $storedRole = strtoupper(trim((string) $user->getRole()));

            if ('' !== $storedRole) {
                $roles[] = str_starts_with($storedRole, 'ROLE_')
                    ? $storedRole
                    : 'ROLE_'.$storedRole;
            }
        }

        return array_values(array_unique($roles));
    }
}
