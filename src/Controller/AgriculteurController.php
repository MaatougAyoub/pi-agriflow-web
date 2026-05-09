<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Entity\Diagnosti;
use App\Entity\PlansIrrigation;
use App\Repository\CultureRepository;
use App\Repository\DiagnostiRepository;
use App\Repository\PlansIrrigationRepository;
use App\Repository\PlansIrrigationJourRepository;
use App\Service\AIService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/agriculteur')]
class AgriculteurController extends AbstractController
{
    // ==================== DIAGNOSTICS ====================

    #[Route('/diagnostics', name: 'agriculteur_diagnostics')]
    public function diagnostics(DiagnostiRepository $repo): Response
    {
        $user = $this->getAuthenticatedUtilisateur();
        $diagnostics = $repo->findBy(['utilisateur' => $user]);
        return $this->render('agriculteur/diagnostics.html.twig', [
            'diagnostics' => $diagnostics,
        ]);
    }

    #[Route('/diagnostics/nouveau', name: 'agriculteur_diagnostic_new')]
    public function nouveauDiagnostic(
        Request $request,
        EntityManagerInterface $em,
        SluggerInterface $slugger,
        CultureRepository $cultureRepo,
    ): Response {
        $user     = $this->getAuthenticatedUtilisateur();
        $cultures = $cultureRepo->findBy(['proprietaire_id' => $user->getId()]);

        if ($request->isMethod('POST')) {
            $diagnostic = new Diagnosti();
            $diagnostic->setNomCulture((string) $request->request->get('nomCulture', ''));
            $diagnostic->setDescription((string) $request->request->get('description', ''));
            $diagnostic->setStatut('en_attente');
            $diagnostic->setDateEnvoi(new \DateTime());
            $diagnostic->setUtilisateur($user);

            $uploadedFile = $request->files->get('image');
            if ($uploadedFile) {
                $extension         = $uploadedFile->getClientOriginalExtension();
                $originalFilename  = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename      = $slugger->slug($originalFilename);
                $newFilename       = $safeFilename . '-' . uniqid() . '.' . $extension;

                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                if (in_array(strtolower($extension), $allowedExtensions)) {
                    try {
                        $projectDir = $this->getParameter('kernel.project_dir');
                        if (!is_string($projectDir)) {
                            throw new \RuntimeException('Chemin du projet invalide.');
                        }

                        $uploadDir = $projectDir . '/public/uploads/diagnostics';
                        if (!is_dir($uploadDir)) {
                            mkdir($uploadDir, 0777, true);
                        }
                        $uploadedFile->move($uploadDir, $newFilename);
                        $diagnostic->setImagePath($newFilename);
                    } catch (\Exception $e) {
                        $this->addFlash('danger', 'Erreur upload image.');
                    }
                }
            }

            $em->persist($diagnostic);
            $em->flush();

            
            $this->addFlash('success', 'Diagnostic envoyé avec succès.');
            return $this->redirectToRoute('agriculteur_diagnostics');
        }

        return $this->render('agriculteur/nouveau_diagnostic.html.twig', [
            'cultures' => $cultures,
        ]);
    }


    #[Route('/diagnostics/analyser-ia', name: 'agriculteur_ia_analyser', methods: ['POST'])]
    public function analyserIA(Request $request, AIService $aiService): JsonResponse
    {
        try {
            $uploadedFile = $request->files->get('image');
            
            if (!$uploadedFile) {
                return $this->json(['error' => 'Aucune image fournie.'], 400);
            }
            
            if ($uploadedFile->getError() !== UPLOAD_ERR_OK) {
                return $this->json(['error' => 'Erreur lors de l\'upload (code: ' . $uploadedFile->getError() . ').'], 400);
            }
            
            // Limite de taille (4 Mo)
            $maxSize = 4 * 1024 * 1024;
            if ($uploadedFile->getSize() > $maxSize) {
                return $this->json(['error' => 'L\'image ne doit pas dépasser 4 Mo.'], 400);
            }
            
            // ============================================================
            // CORRECTION : Déterminer le MIME type à partir de l'extension
            // au lieu de $uploadedFile->getMimeType() qui nécessite fileinfo
            // ============================================================
            $extension = strtolower($uploadedFile->getClientOriginalExtension());
            
            $mimeMap = [
                'jpg'  => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'png'  => 'image/png',
                'gif'  => 'image/gif',
                'webp' => 'image/webp',
            ];
            
            if (!isset($mimeMap[$extension])) {
                return $this->json([
                    'error' => 'Format non supporté (.' . $extension . '). Utilisez JPG, PNG, GIF ou WEBP.'
                ], 400);
            }
            
            $mimeType = $mimeMap[$extension];
            
            // Lecture et encodage de l'image
            $imageContent = file_get_contents($uploadedFile->getPathname());
            if ($imageContent === false) {
                return $this->json(['error' => 'Impossible de lire l\'image.'], 500);
            }
            
            $base64 = base64_encode($imageContent);
            
            // Appel au service IA
            $description = $aiService->analyserImage($base64, $mimeType);
            
            return $this->json([
                'description' => $description,
                'success'     => true,
            ]);
            
        } catch (\RuntimeException $e) {
            return $this->json([
                'error'   => $e->getMessage(),
                'success' => false,
            ], 500);
        } catch (\Exception $e) {
            return $this->json([
                'error'   => 'Erreur inattendue : ' . $e->getMessage(),
                'success' => false,
            ], 500);
        }
    }



   #[Route('/diagnostics/{id}', name: 'agriculteur_diagnostic_detail')]
    public function diagnosticDetail(int $id, DiagnostiRepository $repo): Response
    {
        $user = $this->getAuthenticatedUtilisateur();
        $diagnostic = $repo->find($id);
        if (!$diagnostic || $diagnostic->getUtilisateur() !== $user) {
            throw $this->createNotFoundException('Diagnostic introuvable.');
        }
        return $this->render('agriculteur/diagnostic_detail.html.twig', [
            'diagnostic' => $diagnostic,
        ]);
    }

    // ==================== IRRIGATION ====================

    #[Route('/irrigation', name: 'agriculteur_irrigation')]
    public function irrigation(PlansIrrigationRepository $planRepo): Response
    {
        $plans = $planRepo->findByProprietaire((int) $this->getAuthenticatedUtilisateur()->getId());
        return $this->render('agriculteur/irrigation.html.twig', [
            'plans' => $plans,
        ]);
    }

    #[Route('/irrigation/nouveau', name: 'agriculteur_irrigation_new', methods: ['GET', 'POST'])]
    public function nouveauPlan(
        Request $request,
        EntityManagerInterface $em,
        CultureRepository $cultureRepo,
        PlansIrrigationRepository $planRepo,
    ): Response {
        $user     = $this->getAuthenticatedUtilisateur();
        $cultures = $cultureRepo->findBy(['proprietaire_id' => $user->getId()]);

        if ($request->isMethod('POST')) {
            $culture = $cultureRepo->find($request->request->get('culture'));
            if (!$culture) {
                $this->addFlash('danger', 'Culture invalide.');
                return $this->redirectToRoute('agriculteur_irrigation_new');
            }

            $lastId = $em->getRepository(PlansIrrigation::class)
                ->createQueryBuilder('p')
                ->select('MAX(p.plan_id)')
                ->getQuery()
                ->getSingleScalarResult();
            $nextId = (int) $lastId + 1;

            $plan = new PlansIrrigation();
            $plan->setPlanId($nextId);
            $plan->setCulture($culture);
            $plan->setNomCulture($culture->getNom());
            $plan->setVolumeEauPropose((float)$request->request->get('volume_eau', 0));
            $plan->setDateDemande(new \DateTime());
            $plan->setStatut('en_attente');

            $em->persist($plan);
            $em->flush();

            
            $this->addFlash('success', 'Plan créé avec succès.');
            return $this->redirectToRoute('agriculteur_irrigation');
        }

        return $this->render('agriculteur/nouveau_plan.html.twig', [
            'cultures' => $cultures,
        ]);
    }

    #[Route('/irrigation/{id}', name: 'agriculteur_irrigation_detail')]
    public function irrigationDetail(
        int $id,
        PlansIrrigationRepository $planRepo,
        PlansIrrigationJourRepository $jourRepo
    ): Response {
        $plan = $planRepo->find($id);
        if (!$plan) {
            throw $this->createNotFoundException('Plan introuvable.');
        }

        $jours    = $jourRepo->findBy(['plan' => $plan]);
        $jourData = [];
        foreach ($jours as $jour) {
            $jourKey = $jour->getJourKey();
            if ($jourKey !== null) {
                $jourData[$jourKey] = $jour;
            }
        }

        return $this->render('agriculteur/irrigation_detail.html.twig', [
            'plan'     => $plan,
            'jourData' => $jourData,
        ]);
    }
    #[Route('/diagnostics/{id}/ordonnance', name: 'agriculteur_diagnostic_pdf')]
    public function telechargerOrdonnance(int $id, DiagnostiRepository $repo): Response
    {
        $user = $this->getAuthenticatedUtilisateur();
        $diagnostic = $repo->find($id);

        if (!$diagnostic || $diagnostic->getUtilisateur() !== $user) {
            throw $this->createNotFoundException('Diagnostic introuvable.');
        }

        if (!$diagnostic->getReponseExpert()) {
            $this->addFlash('danger', 'Aucune réponse expert disponible.');
            return $this->redirectToRoute('agriculteur_diagnostic_detail', ['id' => $id]);
        }

        // Générer le HTML de l'ordonnance
        $html = $this->renderView('agriculteur/ordonnance_pdf.html.twig', [
            'diagnostic' => $diagnostic,
            'date'       => new \DateTime(),
        ]);

        // Configurer DomPDF
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', false);
        $options->set('defaultFont', 'Arial');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = 'ordonnance-' . $diagnostic->getNomCulture() . '-' .
            (new \DateTime())->format('d-m-Y') . '.pdf';

        return new Response(
            $dompdf->output(),
            200,
            [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]
        );
    }

    private function getAuthenticatedUtilisateur(): Utilisateur
    {
        $user = $this->getUser();

        if (!$user instanceof Utilisateur) {
            throw $this->createAccessDeniedException('Utilisateur non authentifié.');
        }

        return $user;
    }
}