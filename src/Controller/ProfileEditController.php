<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Service\BrevoEmailService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class ProfileEditController extends AbstractController
{
    private const SIGNATURE_DIR = 'C:\\xampp\\htdocs\\signatures';
    private const CARTE_DIR = 'C:\\xampp\\htdocs\\cartes';
    private const CERTIFICATION_DIR = 'C:\\xampp\\htdocs\\certifications';
    private const SESSION_PENDING = 'profile_edit_pending';
    private const SESSION_CODE = 'profile_edit_code';

    #[Route('/profil/modifier', name: 'app_profile_edit', methods: ['GET', 'POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function edit(Request $request, EntityManagerInterface $entityManager, LoggerInterface $logger, BrevoEmailService $brevoEmailService): Response
    {
        $user = $this->getAuthenticatedUser();
        $role = $this->normalizeRole((string) $user->getRole());
        $session = $request->getSession();

        $errors = [];

        $old = [
            'nom' => (string) $user->getNom(),
            'prenom' => (string) $user->getPrenom(),
            'email' => (string) $user->getEmail(),
            'adresse' => (string) $user->getAdresse(),
        ];

        if ($request->isMethod('POST')) {
            $nom = trim((string) $request->request->get('nom'));
            $prenom = trim((string) $request->request->get('prenom'));
            $email = trim((string) $request->request->get('email'));
            $adresse = trim((string) $request->request->get('adresse'));

            $signatureFile = $request->files->get('signature');
            $carteProFile = $request->files->get('carte_pro');
            $certificationFile = $request->files->get('certification');

            if ($nom === '') {
                $errors[] = 'Veuillez entrer votre nom.';
            }
            if ($prenom === '') {
                $errors[] = 'Veuillez entrer votre prenom.';
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Veuillez entrer un email valide.';
            }

            if (!$signatureFile instanceof UploadedFile && trim((string) $user->getSignature()) === '') {
                $errors[] = 'Veuillez ajouter votre signature.';
            }

            if ($role === 'AGRICULTEUR') {
                if ($adresse === '') {
                    $errors[] = 'Veuillez entrer votre adresse.';
                }
                if (!$carteProFile instanceof UploadedFile && trim((string) $user->getCartePro()) === '') {
                    $errors[] = 'Veuillez ajouter la carte professionnelle.';
                }
            }

            if ($role === 'EXPERT') {
                if (!$certificationFile instanceof UploadedFile && trim((string) $user->getCertification()) === '') {
                    $errors[] = 'Veuillez ajouter la certification.';
                }
            }

            if (!$errors) {
                $existingUser = $entityManager->getRepository(Utilisateur::class)->findOneBy(['email' => $email]);
                if ($existingUser && $existingUser->getId() !== $user->getId()) {
                    $errors[] = 'Un compte existe deja avec cet email.';
                }
            }

            if (!$errors) {
                $signaturePath = $signatureFile instanceof UploadedFile
                    ? $this->storeUploadedFile($signatureFile, self::SIGNATURE_DIR)
                    : (string) $user->getSignature();

                $carteProPath = $user->getCartePro();
                if ($role === 'AGRICULTEUR' && $carteProFile instanceof UploadedFile) {
                    $carteProPath = $this->storeUploadedFile($carteProFile, self::CARTE_DIR);
                }

                $certificationPath = $user->getCertification();
                if ($role === 'EXPERT' && $certificationFile instanceof UploadedFile) {
                    $certificationPath = $this->storeUploadedFile($certificationFile, self::CERTIFICATION_DIR);
                }

                $session->set(self::SESSION_PENDING, [
                    'user_id' => $user->getId(),
                    'role' => $role,
                    'nom' => $nom,
                    'prenom' => $prenom,
                    'email' => $email,
                    'signature_path' => $signaturePath,
                    'adresse' => $role === 'AGRICULTEUR' ? $adresse : $user->getAdresse(),
                    'carte_pro_path' => $role === 'AGRICULTEUR' ? $carteProPath : $user->getCartePro(),
                    'certification_path' => $role === 'EXPERT' ? $certificationPath : $user->getCertification(),
                ]);

                try {
                    $this->generateAndSendVerificationCode($session, $email, $logger, $brevoEmailService, 'Modification du profil');

                    return $this->redirectToRoute('app_profile_edit_verify');
                } catch (\Throwable $exception) {
                    $errors[] = $exception->getMessage();
                }
            }

            $old = [
                'nom' => $nom,
                'prenom' => $prenom,
                'email' => $email,
                'adresse' => $adresse,
            ];
        }

        return $this->render('site/profile_edit.html.twig', [
            'user' => $user,
            'role' => $role,
            'errors' => $errors,
            'old' => $old,
            'has_signature' => trim((string) $user->getSignature()) !== '',
            'has_carte_pro' => trim((string) $user->getCartePro()) !== '',
            'has_certification' => trim((string) $user->getCertification()) !== '',
        ]);
    }

    #[Route('/profil/modifier/verifier', name: 'app_profile_edit_verify', methods: ['GET', 'POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function verify(Request $request, EntityManagerInterface $entityManager, LoggerInterface $logger, BrevoEmailService $brevoEmailService): Response
    {
        $user = $this->getAuthenticatedUser();
        $session = $request->getSession();
        $pending = $session->get(self::SESSION_PENDING);
        $error = null;
        $notice = null;

        if (!is_array($pending) || (int) ($pending['user_id'] ?? 0) !== $user->getId()) {
            $session->remove(self::SESSION_PENDING);
            $session->remove(self::SESSION_CODE);

            return $this->redirectToRoute('app_profile_edit');
        }

        if (!$session->has(self::SESSION_CODE)) {
            try {
                $this->generateAndSendVerificationCode($session, (string) ($pending['email'] ?? $user->getEmail()), $logger, $brevoEmailService, 'Modification du profil');
                $notice = 'Un code de verification a ete envoye par email.';
            } catch (\Throwable $exception) {
                $error = $exception->getMessage();
            }
        }

        if ($request->isMethod('POST')) {
            $action = (string) $request->request->get('action', 'verify');

            if ('resend' === $action) {
                try {
                    $this->generateAndSendVerificationCode($session, (string) ($pending['email'] ?? $user->getEmail()), $logger, $brevoEmailService, 'Modification du profil');
                    $notice = 'Un nouveau code a ete envoye par email.';
                } catch (\Throwable $exception) {
                    $error = $exception->getMessage();
                }
            } else {
                $submittedCode = trim((string) $request->request->get('verification_code'));
                $expectedCode = (string) $session->get(self::SESSION_CODE, '');

                if (!preg_match('/^\d{6}$/', $submittedCode) || $submittedCode !== $expectedCode) {
                    $error = 'Code invalide. Un nouveau code a ete genere.';

                    try {
                        $this->generateAndSendVerificationCode($session, (string) ($pending['email'] ?? $user->getEmail()), $logger, $brevoEmailService, 'Modification du profil');
                    } catch (\Throwable $exception) {
                        $error .= ' ' . $exception->getMessage();
                    }
                } else {
                    $newEmail = (string) ($pending['email'] ?? '');
                    $existingUser = $entityManager->getRepository(Utilisateur::class)->findOneBy(['email' => $newEmail]);

                    if ($existingUser && $existingUser->getId() !== $user->getId()) {
                        $error = 'Un compte existe deja avec cet email.';
                    } else {
                        $role = strtoupper((string) ($pending['role'] ?? $this->normalizeRole((string) $user->getRole())));

                        $user->setNom((string) ($pending['nom'] ?? $user->getNom()));
                        $user->setPrenom((string) ($pending['prenom'] ?? $user->getPrenom()));
                        $user->setEmail($newEmail);
                        $user->setSignature((string) ($pending['signature_path'] ?? $user->getSignature()));

                        if ($role === 'AGRICULTEUR') {
                            $user->setAdresse((string) ($pending['adresse'] ?? $user->getAdresse()));
                            $user->setCartePro((string) ($pending['carte_pro_path'] ?? $user->getCartePro()));
                        }

                        if ($role === 'EXPERT') {
                            $user->setCertification((string) ($pending['certification_path'] ?? $user->getCertification()));
                        }

                        $entityManager->flush();

                        $session->remove(self::SESSION_PENDING);
                        $session->remove(self::SESSION_CODE);

                        $this->addFlash('success', 'Profil modifie avec succes.');

                        return $this->redirectToRoute('app_profile');
                    }
                }
            }
        }

        return $this->render('site/profile_edit_verify.html.twig', [
            'error' => $error,
            'notice' => $notice,
            'email' => (string) ($pending['email'] ?? ''),
        ]);
    }

    private function getAuthenticatedUser(): Utilisateur
    {
        $user = $this->getUser();

        if (!$user instanceof Utilisateur) {
            throw $this->createAccessDeniedException('Utilisateur non connecte.');
        }

        return $user;
    }

    private function normalizeRole(string $role): string
    {
        $normalized = strtoupper(trim($role));

        if (str_starts_with($normalized, 'ROLE_')) {
            return substr($normalized, 5);
        }

        return $normalized;
    }

    private function storeUploadedFile(?UploadedFile $file, string $relativeDirectory): string
    {
        if (!$file instanceof UploadedFile) {
            throw new \InvalidArgumentException('Missing upload file.');
        }

        $targetDirectory = $this->resolveUploadDirectory($relativeDirectory);

        if (!is_dir($targetDirectory)) {
            mkdir($targetDirectory, 0775, true);
        }

        $safeBaseName = pathinfo((string) $file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeBaseName = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $safeBaseName) ?: 'file';
        $newFileName = $safeBaseName . '_' . uniqid('', true) . '.' . $file->guessExtension();

        try {
            $file->move($targetDirectory, $newFileName);
        } catch (FileException $exception) {
            throw new \RuntimeException('Unable to save uploaded file: ' . $exception->getMessage(), 0, $exception);
        }

        if ($this->isAbsolutePath($relativeDirectory)) {
            return $targetDirectory . DIRECTORY_SEPARATOR . $newFileName;
        }

        return $relativeDirectory . '/' . $newFileName;
    }

    private function resolveUploadDirectory(string $directory): string
    {
        $directory = rtrim($directory, "\\/");

        if ($this->isAbsolutePath($directory)) {
            return $directory;
        }

        $projectDir = $this->getParameter('kernel.project_dir');
        if (!is_string($projectDir)) {
            throw new \RuntimeException('Invalid project directory parameter.');
        }

        return $projectDir . '/public/' . $directory;
    }

    private function isAbsolutePath(string $path): bool
    {
        return (bool) preg_match('/^[A-Za-z]:\\\\/', $path) || str_starts_with($path, '/');
    }

    private function generateAndSendVerificationCode(SessionInterface $session, string $email, LoggerInterface $logger, BrevoEmailService $brevoEmailService, string $context): string
    {
        $code = (string) random_int(100000, 999999);
        $session->set(self::SESSION_CODE, $code);

        $logger->info('Profile edit verification code generated', [
            'email' => $email,
            'code' => $code,
            'context' => $context,
        ]);

        $brevoEmailService->sendVerificationCode($email, $code, $context);

        return $code;
    }
}
