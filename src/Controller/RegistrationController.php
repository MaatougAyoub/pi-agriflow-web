<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Service\BrevoEmailService;
use App\Service\Ocr\TesseractOcrService;
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
    private const REQUIRED_FARMER_WORD = 'فلاح';

    #[Route('/register', name: 'app_register', methods: ['GET', 'POST'])]
    public function register(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, LoggerInterface $logger, BrevoEmailService $brevoEmailService, TesseractOcrService $tesseractOcrService): Response
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

            if (!$errors && $selectedRole === 'AGRICULTEUR' && $carteProFile instanceof UploadedFile) {
                try {
                    $this->validateFarmerCardWithOcr($carteProFile, $nomAr, $prenomAr, $cin, $tesseractOcrService);
                } catch (\Throwable $exception) {
                    $errors[] = $exception->getMessage();

                    $logger->warning('Farmer card OCR validation failed during registration', [
                        'email' => $email,
                        'cin' => $cin,
                        'error' => $exception->getMessage(),
                    ]);
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

                try {
                    $this->generateAndSendVerificationCode($session, $email, $logger, $brevoEmailService, 'Inscription');

                    return $this->redirectToRoute('app_register_verify');
                } catch (\Throwable $exception) {
                    $errors[] = $exception->getMessage();
                }
            }
        }

        $errorResponse = null;
        if ($request->isMethod('POST') && $errors !== []) {
            $errorResponse = new Response(status: Response::HTTP_UNPROCESSABLE_ENTITY);
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
        ], $errorResponse);
    }

    #[Route('/register/verify', name: 'app_register_verify', methods: ['GET', 'POST'])]
    public function verify(Request $request, EntityManagerInterface $entityManager, LoggerInterface $logger, BrevoEmailService $brevoEmailService): Response
    {
        $session = $request->getSession();
        $pending = $session->get(self::SESSION_PENDING);
        $error = null;
        $notice = null;

        if (!$pending) {
            return $this->redirectToRoute('app_register');
        }

        if (!$session->has(self::SESSION_CODE)) {
            try {
                $this->generateAndSendVerificationCode($session, (string) ($pending['email'] ?? 'inconnu'), $logger, $brevoEmailService, 'Inscription');
                $notice = 'Un code de verification a ete envoye par email.';
            } catch (\Throwable $exception) {
                $error = $exception->getMessage();
            }
        }

        if ($request->isMethod('POST')) {
            $action = (string) $request->request->get('action', 'verify');

            if ('resend' === $action) {
                try {
                    $this->generateAndSendVerificationCode($session, (string) ($pending['email'] ?? 'inconnu'), $logger, $brevoEmailService, 'Inscription');
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
                        $this->generateAndSendVerificationCode($session, (string) ($pending['email'] ?? 'inconnu'), $logger, $brevoEmailService, 'Inscription');
                    } catch (\Throwable $exception) {
                        $error .= ' ' . $exception->getMessage();
                    }
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
        }

        return $this->render('security/verify_code.html.twig', [
            'error' => $error,
            'notice' => $notice,
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

    private function generateAndSendVerificationCode($session, string $email, LoggerInterface $logger, BrevoEmailService $brevoEmailService, string $context): string
    {
        $code = (string) random_int(100000, 999999);
        $session->set(self::SESSION_CODE, $code);

        $logger->info('Verification code generated', [
            'email' => $email,
            'code' => $code,
            'context' => $context,
        ]);

        $brevoEmailService->sendVerificationCode($email, $code, $context);

        return $code;
    }

    private function validateFarmerCardWithOcr(UploadedFile $carteProFile, string $nomAr, string $prenomAr, string $cin, TesseractOcrService $tesseractOcrService): void
    {
        $ocrText = $tesseractOcrService->extractText((string) $carteProFile->getPathname(), 'ara+eng')->getFullText();

        $normalizedText = $this->normalizeArabicText($ocrText);
        $compactText = str_replace(' ', '', $normalizedText);

        $nomArNormalized = str_replace(' ', '', $this->normalizeArabicText($nomAr));
        $prenomArNormalized = str_replace(' ', '', $this->normalizeArabicText($prenomAr));
        $requiredWord = str_replace(' ', '', $this->normalizeArabicText(self::REQUIRED_FARMER_WORD));
        $cinDigits = preg_replace('/\D+/', '', $this->normalizeDigits($cin)) ?: '';
        $ocrDigits = preg_replace('/\D+/', '', $this->normalizeDigits($ocrText)) ?: '';

        $ocrErrors = [];

        if ($nomArNormalized === '' || !$this->containsArabicValue($normalizedText, $compactText, $nomArNormalized)) {
            $ocrErrors[] = 'Le nom arabe n\'a pas ete detecte dans la carte professionnelle.';
        }

        if ($prenomArNormalized === '' || !$this->containsArabicValue($normalizedText, $compactText, $prenomArNormalized)) {
            $ocrErrors[] = 'Le prenom arabe n\'a pas ete detecte dans la carte professionnelle.';
        }

        if ($cinDigits === '' || !str_contains($ocrDigits, $cinDigits)) {
            $ocrErrors[] = 'Le CIN saisi ne correspond pas au CIN detecte dans la carte professionnelle.';
        }

        if ($requiredWord === '' || !str_contains($compactText, $requiredWord)) {
            $ocrErrors[] = 'Le mot "فلاح" est introuvable dans la carte professionnelle.';
        }

        if ($ocrErrors !== []) {
            throw new \RuntimeException('Verification OCR echouee: ' . implode(' ', $ocrErrors));
        }
    }

    private function normalizeArabicText(string $text): string
    {
        $text = trim($this->normalizeDigits($text));
        $text = strtr($text, [
            'أ' => 'ا',
            'إ' => 'ا',
            'آ' => 'ا',
            'ٱ' => 'ا',
            'ى' => 'ي',
            'ؤ' => 'و',
            'ئ' => 'ي',
            'ة' => 'ه',
            'ـ' => '',
        ]);

        $text = preg_replace('/[\x{0610}-\x{061A}\x{064B}-\x{065F}\x{06D6}-\x{06ED}]/u', '', $text) ?? $text;
        $text = preg_replace('/[^\p{Arabic}\p{N}\s]+/u', ' ', $text) ?? $text;
        $text = preg_replace('/\s+/u', ' ', $text) ?? $text;

        return trim($text);
    }

    private function normalizeDigits(string $text): string
    {
        return strtr($text, [
            '٠' => '0',
            '١' => '1',
            '٢' => '2',
            '٣' => '3',
            '٤' => '4',
            '٥' => '5',
            '٦' => '6',
            '٧' => '7',
            '٨' => '8',
            '٩' => '9',
            '۰' => '0',
            '۱' => '1',
            '۲' => '2',
            '۳' => '3',
            '۴' => '4',
            '۵' => '5',
            '۶' => '6',
            '۷' => '7',
            '۸' => '8',
            '۹' => '9',
        ]);
    }

    private function containsArabicValue(string $normalizedOcrText, string $compactOcrText, string $expectedCompact): bool
    {
        if ($expectedCompact === '') {
            return false;
        }

        if (str_contains($compactOcrText, $expectedCompact)) {
            return true;
        }

        $tokens = preg_split('/\s+/u', trim($normalizedOcrText)) ?: [];
        $candidateTokens = [];

        foreach ($tokens as $token) {
            $normalizedToken = str_replace(' ', '', $this->normalizeArabicText($token));
            if ($normalizedToken !== '') {
                $candidateTokens[] = $normalizedToken;
            }
        }

        $mergedCandidates = $candidateTokens;
        $tokenCount = count($candidateTokens);

        // Only combine adjacent original tokens once to avoid runaway array growth.
        for ($i = 0; $i < $tokenCount - 1; ++$i) {
            $mergedCandidates[] = $candidateTokens[$i] . $candidateTokens[$i + 1];
        }

        $expectedLatin = $this->arabicToLatinSkeleton($expectedCompact);
        $expectedLen = strlen($expectedLatin);

        if ($expectedLen === 0) {
            return false;
        }

        $maxDistance = max(1, (int) floor($expectedLen * 0.34));

        foreach ($mergedCandidates as $candidate) {
            if ($candidate === '' || strlen($candidate) < 2) {
                continue;
            }

            if (str_contains($candidate, $expectedCompact) || str_contains($expectedCompact, $candidate)) {
                return true;
            }

            $candidateLatin = $this->arabicToLatinSkeleton($candidate);
            if ($candidateLatin === '') {
                continue;
            }

            $distance = levenshtein($expectedLatin, $candidateLatin);
            if ($distance <= $maxDistance) {
                return true;
            }
        }

        return false;
    }

    private function arabicToLatinSkeleton(string $text): string
    {
        return strtr($text, [
            'ا' => 'a',
            'ب' => 'b',
            'ت' => 't',
            'ث' => 'v',
            'ج' => 'j',
            'ح' => 'h',
            'خ' => 'x',
            'د' => 'd',
            'ذ' => 'z',
            'ر' => 'r',
            'ز' => 'z',
            'س' => 's',
            'ش' => 'c',
            'ص' => 's',
            'ض' => 'd',
            'ط' => 't',
            'ظ' => 'z',
            'ع' => 'e',
            'غ' => 'g',
            'ف' => 'f',
            'ق' => 'q',
            'ك' => 'k',
            'ل' => 'l',
            'م' => 'm',
            'ن' => 'n',
            'ه' => 'h',
            'و' => 'w',
            'ي' => 'y',
        ]);
    }
}
