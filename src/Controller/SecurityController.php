<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Service\BrevoEmailService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

final class SecurityController extends AbstractController
{
    private const PASSWORD_PATTERN = '/^(?=.*[A-Z])(?=.*\d).{7,}$/';
    private const SESSION_FORGOT_PASSWORD_PENDING = 'forgot_password_pending';
    private const SESSION_FORGOT_PASSWORD_CODE = 'forgot_password_code';

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

    #[Route('/forgot-password', name: 'app_forgot_password', methods: ['GET', 'POST'])]
    public function forgotPassword(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
        LoggerInterface $logger,
        BrevoEmailService $brevoEmailService
    ): Response {
        $session = $request->getSession();
        $email = trim((string) $request->request->get('email', ''));
        $verifiedEmail = trim((string) $request->request->get('verified_email', ''));
        $emailError = null;
        $passwordError = null;
        $showResetForm = false;

        if ($request->isMethod('POST')) {
            $step = (string) $request->request->get('step', 'email');

            if ('email' === $step) {
                if (!$this->isCsrfTokenValid('forgot-password-email', (string) $request->request->get('_csrf_token'))) {
                    $emailError = 'Session invalide. Veuillez reessayer.';
                } elseif ('' === $email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $emailError = 'Veuillez saisir un email valide.';
                } else {
                    $user = $entityManager->getRepository(Utilisateur::class)->findOneBy(['email' => $email]);

                    if (!$user instanceof Utilisateur) {
                        $emailError = 'Aucun compte n\'est enregistre avec cet email.';
                    } else {
                        $showResetForm = true;
                        $verifiedEmail = $email;
                    }
                }
            }

            if ('reset' === $step) {
                $showResetForm = true;

                if (!$this->isCsrfTokenValid('forgot-password-reset', (string) $request->request->get('_csrf_token'))) {
                    $passwordError = 'Session invalide. Veuillez reessayer.';
                } elseif ('' === $verifiedEmail || !filter_var($verifiedEmail, FILTER_VALIDATE_EMAIL)) {
                    $emailError = 'Veuillez verifier votre email avant de modifier le mot de passe.';
                    $showResetForm = false;
                } else {
                    $user = $entityManager->getRepository(Utilisateur::class)->findOneBy(['email' => $verifiedEmail]);

                    if (!$user instanceof Utilisateur) {
                        $emailError = 'Aucun compte n\'est enregistre avec cet email.';
                        $showResetForm = false;
                    } else {
                        $newPassword = (string) $request->request->get('new_password', '');
                        $confirmPassword = (string) $request->request->get('confirm_password', '');

                        if (!preg_match(self::PASSWORD_PATTERN, $newPassword)) {
                            $passwordError = 'Le mot de passe doit contenir au moins 7 caracteres, un chiffre et une majuscule.';
                        } elseif ($newPassword !== $confirmPassword) {
                            $passwordError = 'La confirmation du mot de passe ne correspond pas.';
                        } else {
                            $session->set(self::SESSION_FORGOT_PASSWORD_PENDING, [
                                'email' => $verifiedEmail,
                                'password_hash' => $passwordHasher->hashPassword($user, $newPassword),
                            ]);

                            try {
                                $this->generateAndSendForgotPasswordCode($session, $verifiedEmail, $logger, $brevoEmailService, 'Reinitialisation du mot de passe');

                                return $this->redirectToRoute('app_forgot_password_verify');
                            } catch (\Throwable $exception) {
                                $passwordError = $exception->getMessage();
                            }
                        }
                    }
                }
            }
        }

        return $this->render('security/forgot_password.html.twig', [
            'email' => $email,
            'verified_email' => $verifiedEmail,
            'email_error' => $emailError,
            'password_error' => $passwordError,
            'show_reset_form' => $showResetForm,
        ]);
    }

    #[Route('/forgot-password/verify', name: 'app_forgot_password_verify', methods: ['GET', 'POST'])]
    public function forgotPasswordVerify(
        Request $request,
        EntityManagerInterface $entityManager,
        LoggerInterface $logger,
        BrevoEmailService $brevoEmailService
    ): Response {
        $session = $request->getSession();
        $pending = $session->get(self::SESSION_FORGOT_PASSWORD_PENDING);
        $error = null;
        $notice = null;

        if (
            !is_array($pending)
            || '' === trim((string) ($pending['email'] ?? ''))
            || '' === trim((string) ($pending['password_hash'] ?? ''))
        ) {
            return $this->redirectToRoute('app_forgot_password');
        }

        $email = (string) $pending['email'];

        if (!$session->has(self::SESSION_FORGOT_PASSWORD_CODE)) {
            try {
                $this->generateAndSendForgotPasswordCode($session, $email, $logger, $brevoEmailService, 'Reinitialisation du mot de passe');
                $notice = 'Un code de verification a ete envoye par email.';
            } catch (\Throwable $exception) {
                $error = $exception->getMessage();
            }
        }

        if ($request->isMethod('POST')) {
            $action = (string) $request->request->get('action', 'verify');

            if ('resend' === $action) {
                try {
                    $this->generateAndSendForgotPasswordCode($session, $email, $logger, $brevoEmailService, 'Reinitialisation du mot de passe');
                    $notice = 'Un nouveau code a ete envoye par email.';
                } catch (\Throwable $exception) {
                    $error = $exception->getMessage();
                }
            } else {
                $submittedCode = trim((string) $request->request->get('verification_code'));
                $expectedCode = (string) $session->get(self::SESSION_FORGOT_PASSWORD_CODE, '');

                if (!preg_match('/^\d{6}$/', $submittedCode) || $submittedCode !== $expectedCode) {
                    $error = 'Le code n\'est pas correct, veuillez entrer le nouveau code.';

                    try {
                        $this->generateAndSendForgotPasswordCode($session, $email, $logger, $brevoEmailService, 'Reinitialisation du mot de passe');
                    } catch (\Throwable $exception) {
                        $error .= ' ' . $exception->getMessage();
                    }
                } else {
                    $user = $entityManager->getRepository(Utilisateur::class)->findOneBy(['email' => $email]);

                    if (!$user instanceof Utilisateur) {
                        $session->remove(self::SESSION_FORGOT_PASSWORD_PENDING);
                        $session->remove(self::SESSION_FORGOT_PASSWORD_CODE);
                        $this->addFlash('error', 'Aucun compte n\'est enregistre avec cet email.');

                        return $this->redirectToRoute('app_forgot_password');
                    }

                    $user->setMotDePasse((string) $pending['password_hash']);
                    $entityManager->flush();

                    $session->remove(self::SESSION_FORGOT_PASSWORD_PENDING);
                    $session->remove(self::SESSION_FORGOT_PASSWORD_CODE);

                    $this->addFlash('success', 'Mot de passe modifie avec succes. Vous pouvez maintenant vous connecter.');

                    return $this->redirectToRoute('app_login');
                }
            }
        }

        return $this->render('security/forgot_password_verify.html.twig', [
            'error' => $error,
            'notice' => $notice,
            'email' => $email,
        ], null !== $error ? new Response(status: Response::HTTP_UNPROCESSABLE_ENTITY) : null);
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

    private function generateAndSendForgotPasswordCode($session, string $email, LoggerInterface $logger, BrevoEmailService $brevoEmailService, string $context): string
    {
        $code = (string) random_int(100000, 999999);
        $session->set(self::SESSION_FORGOT_PASSWORD_CODE, $code);

        $logger->info('Forgot password verification code generated', [
            'email' => $email,
            'code' => $code,
            'context' => $context,
        ]);

        $brevoEmailService->sendVerificationCode($email, $code, $context);

        return $code;
    }
}
