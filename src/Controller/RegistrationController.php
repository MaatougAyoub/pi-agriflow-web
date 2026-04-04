<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Routing\Attribute\Route;

final class RegistrationController extends AbstractController
{
    private const SIGNATURE_DIR = 'C:\\xampp\\htdocs\\signatures';
    private const CARTE_DIR = 'C:\\xampp\\htdocs\\cartes';
    private const CERTIFICATION_DIR = 'C:\\xampp\\htdocs\\certifications';
    private const SESSION_PENDING = 'registration_pending';
    private const SESSION_CODE = 'registration_code';

    #[Route('/register', name: 'app_register', methods: ['GET', 'POST'])]
    public function register(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, LoggerInterface $logger): Response
    {
        $errors = [];
        $selectedRole = strtoupper((string) $request->request->get('role', 'AGRICULTEUR'));
        $session = $request->getSession();

        if ($request->isMethod('POST')) {
            $nom = trim((string) $request->request->get('nom'));
            $prenom = trim((string) $request->request->get('prenom'));
            $cin = trim((string) $request->request->get('cin'));
            $email = trim((string) $request->request->get('email'));
            $password = (string) $request->request->get('password');
            $passwordConfirm = (string) $request->request->get('password_confirm');
            $signatureFile = $request->files->get('signature');
            $nomAr = trim((string) $request->request->get('nom_ar'));
            $prenomAr = trim((string) $request->request->get('prenom_ar'));
            $adresse = trim((string) $request->request->get('adresse'));
            $parcelles = trim((string) $request->request->get('parcelles'));
            $carteProFile = $request->files->get('carte_pro');
            $certificationFile = $request->files->get('certification');

            if ($nom === '') {
                $errors[] = 'Veuillez entrer votre nom.';
            }
            if ($prenom === '') {
                $errors[] = 'Veuillez entrer votre prenom.';
            }
            if (!preg_match('/^\d{8}$/', $cin)) {
                $errors[] = 'Veuillez entrer un CIN valide (8 chiffres).';
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Veuillez entrer un email valide.';
            }
            if (!preg_match('/^(?=.*[A-Z])(?=.*\d).{7,}$/', $password)) {
                $errors[] = 'Veuillez entrer un mot de passe avec au moins 7 caracteres, un chiffre et une lettre majuscule.';
            }
            if ($password !== $passwordConfirm) {
                $errors[] = 'Les mots de passe ne correspondent pas.';
            }
            if (!$signatureFile instanceof UploadedFile || !$signatureFile->getClientOriginalName()) {
                $errors[] = 'Veuillez ajouter votre signature.';
            }
            if (!in_array($selectedRole, ['ADMIN', 'EXPERT', 'AGRICULTEUR'], true)) {
                $errors[] = 'Veuillez choisir un type d\'utilisateur valide.';
            }

            if ($selectedRole === 'AGRICULTEUR') {
                if ($nomAr === '') {
                    $errors[] = 'Veuillez entrer le nom arabe (agriculteur).';
                }
                if ($prenomAr === '') {
                    $errors[] = 'Veuillez entrer le prenom arabe (agriculteur).';
                }
                if ($adresse === '') {
                    $errors[] = 'Veuillez entrer l\'adresse (agriculteur).';
                }
                if (!$carteProFile instanceof UploadedFile || !$carteProFile->getClientOriginalName()) {
                    $errors[] = 'Veuillez ajouter la carte professionnelle (agriculteur).';
                }
            }

            if ($selectedRole === 'EXPERT') {
                if (!$certificationFile instanceof UploadedFile || !$certificationFile->getClientOriginalName()) {
                    $errors[] = 'Veuillez ajouter la certification (expert).';
                }
            }

            if (!$errors) {
                $existingUser = $entityManager->getRepository(Utilisateur::class)->findOneBy(['email' => $email]);
                if ($existingUser) {
                    $errors[] = 'Un compte existe deja avec cet email.';
                }
            }

            if (!$errors) {
                $signaturePath = $this->storeUploadedFile($signatureFile, self::SIGNATURE_DIR);
                $carteProPath = $selectedRole === 'AGRICULTEUR' ? $this->storeUploadedFile($carteProFile, self::CARTE_DIR) : null;
                $certificationPath = $selectedRole === 'EXPERT' ? $this->storeUploadedFile($certificationFile, self::CERTIFICATION_DIR) : null;

                $tempUser = new Utilisateur();
                $session->set(self::SESSION_PENDING, [
                    'nom' => $nom,
                    'prenom' => $prenom,
                    'cin' => (int) $cin,
                    'email' => $email,
                    'password_hash' => $passwordHasher->hashPassword($tempUser, $password),
                    'role' => $selectedRole,
                    'signature_path' => $signaturePath,
                    'carte_pro_path' => $carteProPath,
                    'certification_path' => $certificationPath,
                    'nom_ar' => $selectedRole === 'AGRICULTEUR' ? $nomAr : null,
                    'prenom_ar' => $selectedRole === 'AGRICULTEUR' ? $prenomAr : null,
                    'adresse' => $selectedRole === 'AGRICULTEUR' ? $adresse : null,
                    'parcelles' => $selectedRole === 'AGRICULTEUR' ? $parcelles : null,
                ]);

                $this->generateAndLogVerificationCode($session, $email, $logger);

                return $this->redirectToRoute('app_register_verify');
            }
        }

        return $this->render('security/register.html.twig', [
            'errors' => $errors,
            'success_message' => null,
            'selected_role' => $selectedRole,
            'old' => [
                'nom' => (string) $request->request->get('nom', ''),
                'prenom' => (string) $request->request->get('prenom', ''),
                'cin' => (string) $request->request->get('cin', ''),
                'email' => (string) $request->request->get('email', ''),
                'nom_ar' => (string) $request->request->get('nom_ar', ''),
                'prenom_ar' => (string) $request->request->get('prenom_ar', ''),
                'adresse' => (string) $request->request->get('adresse', ''),
                'parcelles' => (string) $request->request->get('parcelles', ''),
            ],
        ]);
    }

    #[Route('/register/verify', name: 'app_register_verify', methods: ['GET', 'POST'])]
    public function verify(Request $request, EntityManagerInterface $entityManager, LoggerInterface $logger): Response
    {
        $session = $request->getSession();
        $pending = $session->get(self::SESSION_PENDING);

        if (!$pending) {
            return $this->redirectToRoute('app_register');
        }

        if (!$session->has(self::SESSION_CODE)) {
            $this->generateAndLogVerificationCode($session, (string) ($pending['email'] ?? 'inconnu'), $logger);
        }

        $error = null;

        if ($request->isMethod('POST')) {
            $submittedCode = trim((string) $request->request->get('verification_code'));
            $expectedCode = (string) $session->get(self::SESSION_CODE, '');

            if (!preg_match('/^\d{6}$/', $submittedCode) || $submittedCode !== $expectedCode) {
                $error = 'Code invalide. Un nouveau code a ete genere.';
                $this->generateAndLogVerificationCode($session, (string) ($pending['email'] ?? 'inconnu'), $logger);
            } else {
                $existingUser = $entityManager->getRepository(Utilisateur::class)->findOneBy([
                    'email' => $pending['email'] ?? null,
                ]);

                if ($existingUser) {
                    $error = 'Un compte existe deja avec cet email.';
                } else {
                    $user = new Utilisateur();
                    $user->setNom((string) $pending['nom']);
                    $user->setPrenom((string) $pending['prenom']);
                    $user->setCin((int) $pending['cin']);
                    $user->setEmail((string) $pending['email']);
                    $user->setMotDePasse((string) $pending['password_hash']);
                    $user->setRole((string) $pending['role']);
                    $user->setDateCreation(new \DateTime());
                    $user->setSignature((string) $pending['signature_path']);
                    $user->setRevenu(null);
                    $user->setVerificationStatus('APPROVED');
                    $user->setVerificationReason(null);
                    $user->setVerificationScore(1.0);
                    $user->setNomAr($pending['nom_ar']);
                    $user->setPrenomAr($pending['prenom_ar']);
                    $user->setAdresse($pending['adresse']);
                    $user->setParcelles($pending['parcelles']);
                    $user->setCartePro($pending['carte_pro_path']);
                    $user->setCertification($pending['certification_path']);

                    $entityManager->persist($user);
                    $entityManager->flush();

                    $session->remove(self::SESSION_PENDING);
                    $session->remove(self::SESSION_CODE);

                    return $this->redirectToRoute('app_login');
                }
            }
        }

        return $this->render('security/verify_code.html.twig', [
            'error' => $error,
            'email' => (string) ($pending['email'] ?? ''),
        ]);
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

        return $projectDir . '/public/' . $directory;
    }

    private function isAbsolutePath(string $path): bool
    {
        return (bool) preg_match('/^[A-Za-z]:\\\\/', $path) || str_starts_with($path, '/');
    }

    private function generateAndLogVerificationCode($session, string $email, LoggerInterface $logger): string
    {
        $code = (string) random_int(100000, 999999);
        $session->set(self::SESSION_CODE, $code);
        $logger->info('Verification code generated', [
            'email' => $email,
            'code' => $code,
        ]);

        return $code;
    }
}
