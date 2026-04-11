<?php

namespace App\Controller;

use App\Entity\Diagnosti;
use App\Entity\PlansIrrigation;
use App\Repository\CultureRepository;
use App\Repository\DiagnostiRepository;
use App\Repository\PlansIrrigationRepository;
use App\Repository\PlansIrrigationJourRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/agriculteur')]
class AgriculteurController extends AbstractController
{
    // ==================== DIAGNOSTICS ====================

    #[Route('/diagnostics', name: 'agriculteur_diagnostics')]
    public function diagnostics(DiagnostiRepository $repo): Response
    {
        $diagnostics = $repo->findBy(['utilisateur' => $this->getUser()]);
        return $this->render('agriculteur/diagnostics.html.twig', [
            'diagnostics' => $diagnostics,
        ]);
    }

    #[Route('/diagnostics/nouveau', name: 'agriculteur_diagnostic_new')]
    public function nouveauDiagnostic(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        if ($request->isMethod('POST')) {
            $diagnostic = new Diagnosti();
            $diagnostic->setNomCulture($request->request->get('nomCulture'));
            $diagnostic->setDescription($request->request->get('description'));
            $diagnostic->setStatut('en_attente');
            $diagnostic->setDateEnvoi(new \DateTime());
            $diagnostic->setUtilisateur($this->getUser());

            $uploadedFile = $request->files->get('image');
            if ($uploadedFile) {
                $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $extension = $uploadedFile->getClientOriginalExtension();
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
                if (!in_array(strtolower($extension), $allowedExtensions)) {
                    $this->addFlash('danger', 'Format non supporté.');
                } else {
                    $newFilename = $safeFilename . '-' . uniqid() . '.' . $extension;
                    try {
                        $uploadedFile->move(
                            $this->getParameter('diagnostics_images_directory'),
                            $newFilename
                        );
                        $diagnostic->setImagePath($newFilename);
                    } catch (FileException $e) {
                        $this->addFlash('danger', 'Erreur upload image.');
                    }
                }
            }

            $em->persist($diagnostic);
            $em->flush();
            $this->addFlash('success', 'Diagnostic envoyé avec succès.');
            return $this->redirectToRoute('agriculteur_diagnostics');
        }

        return $this->render('agriculteur/nouveau_diagnostic.html.twig');
    }

    #[Route('/diagnostics/{id}', name: 'agriculteur_diagnostic_detail')]
    public function diagnosticDetail(int $id, DiagnostiRepository $repo): Response
    {
        $diagnostic = $repo->find($id);
        if (!$diagnostic || $diagnostic->getUtilisateur() !== $this->getUser()) {
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
        $user = $this->getUser();
        // Récupérer les plans liés aux cultures de cet agriculteur
        $plans = $planRepo->findByProprietaire($user->getId());

        return $this->render('agriculteur/irrigation.html.twig', [
            'plans' => $plans,
        ]);
    }

    #[Route('/irrigation/nouveau', name: 'agriculteur_irrigation_new', methods: ['GET', 'POST'])]
    public function nouveauPlan(
        Request $request,
        EntityManagerInterface $em,
        CultureRepository $cultureRepo,
        PlansIrrigationRepository $planRepo
    ): Response {
        $user = $this->getUser();
        $cultures = $cultureRepo->findBy(['proprietaire_id' => $user->getId()]);

        if ($request->isMethod('POST')) {
            $cultureId = $request->request->get('culture');
            $volumeEau = (float)$request->request->get('volume_eau', 0);

            $culture = $cultureRepo->find($cultureId);
            if (!$culture) {
                $this->addFlash('danger', 'Culture invalide.');
                return $this->redirectToRoute('agriculteur_irrigation_new');
            }

            // Trouver le dernier ID pour éviter l'erreur "No identity value"
            $lastId = $em->getRepository(PlansIrrigation::class)
                ->createQueryBuilder('p')
                ->select('MAX(p.plan_id)')
                ->getQuery()
                ->getSingleScalarResult();
            $newId = ($lastId ?: 0) + 1;

            $plan = new PlansIrrigation();
            $plan->setPlanId($newId);
            $plan->setCulture($culture);
            $plan->setNomCulture($culture->getNom());
            $plan->setVolumeEauPropose($volumeEau);
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

        $jours = $jourRepo->findBy(['plan_id' => $id]);
        $jourData = [];
        foreach ($jours as $jour) {
            $jourData[$jour->getJour()] = $jour;
        }

        return $this->render('agriculteur/irrigation_detail.html.twig', [
            'plan'     => $plan,
            'jourData' => $jourData,
        ]);
    }
}