<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class SiteController extends AbstractController
{
    #[Route('/', name: 'site_home', methods: ['GET'])]
    public function home(): Response
    {
        return $this->render('site/index.html.twig');
    }

    #[Route('/about', name: 'site_about', methods: ['GET'])]
    public function about(): Response
    {
        return $this->render('site/about.html.twig');
    }

    #[Route('/services', name: 'site_services', methods: ['GET'])]
    public function services(): Response
    {
        return $this->render('site/services.html.twig');
    }

    #[Route('/testimonials', name: 'site_testimonials', methods: ['GET'])]
    public function testimonials(): Response
    {
        return $this->render('site/testimonials.html.twig');
    }

    #[Route('/blog', name: 'site_blog', methods: ['GET'])]
    public function blog(): Response
    {
        return $this->render('site/blog.html.twig');
    }

    #[Route('/blog-details', name: 'site_blog_details', methods: ['GET'])]
    public function blogDetails(): Response
    {
        return $this->render('site/blog-details.html.twig');
    }

    #[Route('/contact', name: 'site_contact', methods: ['GET'])]
    public function contact(): Response
    {
        return $this->render('site/contact.html.twig');
    }

    #[Route('/profil', name: 'app_profile', methods: ['GET'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function profile(): Response
    {
        $user = $this->getUser();

        if (!$user instanceof Utilisateur) {
            throw $this->createAccessDeniedException('Utilisateur non connecte.');
        }

        return $this->render('site/profilUtilisateur.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/admin/utilisateurs', name: 'app_admin_users', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function adminUsers(UtilisateurRepository $utilisateurRepository): Response
    {
        $users = $utilisateurRepository->findBy([], ['id' => 'DESC']);

        return $this->render('site/listeUtilisateurs.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/admin/utilisateurs/{id}/image/{type}', name: 'app_admin_user_image', methods: ['GET'], requirements: ['type' => 'signature|carte|certification'])]
    #[IsGranted('ROLE_ADMIN')]
    public function adminUserImage(Utilisateur $targetUser, string $type): BinaryFileResponse
    {
        $storedPath = match ($type) {
            'signature' => $targetUser->getSignature(),
            'carte' => $targetUser->getCartePro(),
            'certification' => $targetUser->getCertification(),
            default => null,
        };

        return $this->createUserImageResponse($storedPath);
    }

    #[Route('/admin/utilisateurs/{id}/supprimer', name: 'app_admin_user_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function adminUserDelete(Utilisateur $targetUser, Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isCsrfTokenValid('delete_user_' . $targetUser->getId(), (string) $request->request->get('_token'))) {
            $this->addFlash('error', 'Jeton CSRF invalide.');

            return $this->redirectToRoute('app_admin_users');
        }

        $connectedUser = $this->getAuthenticatedUser();
        if ($connectedUser->getId() === $targetUser->getId()) {
            $this->addFlash('error', 'Vous ne pouvez pas supprimer votre propre compte ADMIN.');

            return $this->redirectToRoute('app_admin_users');
        }

        try {
            $fullName = trim((string) ($targetUser->getNom() . ' ' . $targetUser->getPrenom()));
            $entityManager->remove($targetUser);
            $entityManager->flush();

            $this->addFlash('success', sprintf('Utilisateur "%s" supprime avec succes.', $fullName !== '' ? $fullName : 'inconnu'));
        } catch (\Throwable $exception) {
            $this->addFlash('error', 'Suppression impossible pour cet utilisateur. Verifiez les dependances liees.');
        }

        return $this->redirectToRoute('app_admin_users');
    }

    #[Route('/profil/image/signature', name: 'app_profile_signature_image', methods: ['GET'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function profileSignatureImage(): BinaryFileResponse
    {
        $user = $this->getAuthenticatedUser();

        return $this->createUserImageResponse($user->getSignature());
    }

    #[Route('/profil/image/carte', name: 'app_profile_carte_image', methods: ['GET'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function profileCarteImage(): BinaryFileResponse
    {
        $user = $this->getAuthenticatedUser();

        return $this->createUserImageResponse($user->getCartePro());
    }

    #[Route('/profil/image/certification', name: 'app_profile_certification_image', methods: ['GET'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function profileCertificationImage(): BinaryFileResponse
    {
        $user = $this->getAuthenticatedUser();

        return $this->createUserImageResponse($user->getCertification());
    }

    private function getAuthenticatedUser(): Utilisateur
    {
        $user = $this->getUser();

        if (!$user instanceof Utilisateur) {
            throw $this->createAccessDeniedException('Utilisateur non connecte.');
        }

        return $user;
    }

    private function createUserImageResponse(?string $storedPath): BinaryFileResponse
    {
        $resolvedPath = $this->resolveStoredImagePath($storedPath);

        if ($resolvedPath === null) {
            throw $this->createNotFoundException('Image introuvable.');
        }

        $response = new BinaryFileResponse($resolvedPath);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_INLINE, basename($resolvedPath));

        return $response;
    }

    private function resolveStoredImagePath(?string $storedPath): ?string
    {
        $path = trim((string) $storedPath);
        if ($path === '') {
            return null;
        }

        if ($this->isAbsolutePath($path) && is_file($path)) {
            return $path;
        }

        $normalized = str_replace('\\', '/', ltrim($path, '/\\'));
        $projectDir = (string) $this->getParameter('kernel.project_dir');
        $candidate = $projectDir . '/public/' . $normalized;

        if (is_file($candidate)) {
            return $candidate;
        }

        return null;
    }

    private function isAbsolutePath(string $path): bool
    {
        return (bool) preg_match('/^[A-Za-z]:\\\\/', $path) || str_starts_with($path, '/');
    }
}