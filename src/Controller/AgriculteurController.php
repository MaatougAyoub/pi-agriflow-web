<?php

namespace App\Controller;

use App\Entity\Diagnostic;
use App\Repository\CultureRepository;
use App\Repository\DiagnosticRepository;
use App\Repository\PlanIrrigationRepository;
use App\Repository\PlanIrrigationJourRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/agriculteur')]
class AgriculteurController extends AbstractController
{
    #[Route('/', name: 'agriculteur_home')]
    public function home(PlanIrrigationRepository $planRepo): Response
    {
        return $this->render('agriculteur/home.html.twig', [
            'plans' => $planRepo->findAllWithCulture(),
        ]);
    }

    #[Route('/irrigation', name: 'agriculteur_irrigation')]
    public function irrigation(PlanIrrigationRepository $repo): Response
    {
        return $this->render('agriculteur/irrigation.html.twig', [
            'plans' => $repo->findAllWithCulture(),
        ]);
    }

    #[Route('/irrigation/{id}', name: 'agriculteur_irrigation_detail')]
    public function irrigationDetail(int $id, PlanIrrigationRepository $planRepo, PlanIrrigationJourRepository $jourRepo): Response
    {
        $plan = $planRepo->findOneWithJours($id);
        if (!$plan) {
            throw $this->createNotFoundException('Plan introuvable');
        }
        $jourData = [];
        foreach ($plan->getJours() as $jour) {
            $jourData[$jour->getJour()] = $jour;
        }
        return $this->render('agriculteur/irrigation_detail.html.twig', [
            'plan' => $plan,
            'jourData' => $jourData,
        ]);
    }

    #[Route('/diagnostics', name: 'agriculteur_diagnostics')]
    public function diagnostics(DiagnosticRepository $repo): Response
    {
        return $this->render('agriculteur/diagnostics.html.twig', [
            'diagnostics' => $repo->findAll(),
        ]);
    }

    #[Route('/diagnostics/nouveau', name: 'agriculteur_diagnostic_new')]
    public function nouveauDiagnostic(Request $request, EntityManagerInterface $em, CultureRepository $cultureRepo): Response
    {
        if ($request->isMethod('POST')) {
            $diagnostic = new Diagnostic();
            $diagnostic->setNomCulture($request->request->get('nomCulture'));
            $diagnostic->setDescription($request->request->get('description'));
            $diagnostic->setIdAgriculteur(1);
            $diagnostic->setStatut('en_attente');
            $em->persist($diagnostic);
            $em->flush();
            return $this->redirectToRoute('agriculteur_diagnostics');
        }
        return $this->render('agriculteur/nouveau_diagnostic.html.twig', [
            'cultures' => $cultureRepo->findAll(),
        ]);
    }

    #[Route('/diagnostics/{id}', name: 'agriculteur_diagnostic_detail')]
    public function diagnosticDetail(Diagnostic $diagnostic): Response
    {
        return $this->render('agriculteur/diagnostic_detail.html.twig', [
            'diagnostic' => $diagnostic,
        ]);
    }
}