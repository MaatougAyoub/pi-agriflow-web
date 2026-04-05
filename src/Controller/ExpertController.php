<?php

namespace App\Controller;

use App\Entity\PlanIrrigationJour;
use App\Entity\ProduitPhytosanitaire;
use App\Repository\DiagnosticRepository;
use App\Repository\PlanIrrigationRepository;
use App\Repository\PlanIrrigationJourRepository;
use App\Repository\ProduitPhytosanitaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/expert')]
class ExpertController extends AbstractController
{
    #[Route('/', name: 'expert_home')]
    public function home(DiagnosticRepository $diagRepo, ProduitPhytosanitaireRepository $produitRepo): Response
    {
        return $this->render('expert/home.html.twig', [
            'diagnostics' => $diagRepo->findAll(),
            'total_produits' => count($produitRepo->findAll()),
        ]);
    }

    #[Route('/diagnostics', name: 'expert_diagnostics')]
    public function diagnostics(DiagnosticRepository $repo): Response
    {
        return $this->render('expert/diagnostics.html.twig', [
            'diagnostics' => $repo->findAll(),
        ]);
    }

    #[Route('/diagnostics/{id}/repondre', name: 'expert_diagnostic_repondre', methods: ['POST'])]
    public function repondre(int $id, Request $request, DiagnosticRepository $repo, EntityManagerInterface $em): Response
    {
        $diagnostic = $repo->find($id);
        if ($diagnostic) {
            $diagnostic->setReponseExpert($request->request->get('reponse'));
            $diagnostic->setStatut('traite');
            $em->flush();
        }
        return $this->redirectToRoute('expert_diagnostics');
    }

    #[Route('/irrigation', name: 'expert_irrigation')]
    public function irrigation(PlanIrrigationRepository $repo): Response
    {
        return $this->render('expert/irrigation.html.twig', [
            'plans' => $repo->findAllWithCulture(),
        ]);
    }

    #[Route('/irrigation/{id}', name: 'expert_irrigation_detail')]
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
        return $this->render('expert/irrigation_detail.html.twig', [
            'plan' => $plan,
            'jourData' => $jourData,
        ]);
    }

    #[Route('/irrigation/{id}/save', name: 'expert_irrigation_save', methods: ['POST'])]
    public function irrigationSave(int $id, Request $request, PlanIrrigationRepository $planRepo, PlanIrrigationJourRepository $jourRepo, EntityManagerInterface $em): Response
    {
        $plan = $planRepo->findOneWithJours($id);
        if (!$plan) {
            throw $this->createNotFoundException('Plan introuvable');
        }
        $joursKeys = [1, 2, 3, 4, 5, 6, 7];
        $existing = [];
        foreach ($plan->getJours() as $jour) {
            $existing[$jour->getJour()] = $jour;
        }
        foreach ($joursKeys as $key) {
            if (isset($existing[$key])) {
                $jour = $existing[$key];
            } else {
                $jour = new PlanIrrigationJour();
                $jour->setPlanIrrigation($plan);
                $jour->setJour($key);
                $em->persist($jour);
            }
            $jour->setEauMm((float)$request->request->get('eau_' . $key, 0.0));
            $jour->setDureeMin((int)$request->request->get('duree_' . $key, 0));
            $jour->setTemperature((float)$request->request->get('temp_' . $key, 0.0));
            $jour->setHumidite((float)$request->request->get('hum_' . $key, 0.0));
            $jour->setPluieMm((float)$request->request->get('pluie_' . $key, 0.0));
        }
        $em->flush();
        return $this->redirectToRoute('expert_irrigation_detail', ['id' => $id]);
    }

    #[Route('/produits', name: 'expert_produits')]
    public function produits(ProduitPhytosanitaireRepository $repo): Response
    {
        return $this->render('expert/produits.html.twig', [
            'produits' => $repo->findAll(),
        ]);
    }

    #[Route('/produits/nouveau', name: 'expert_produit_new')]
    public function nouveauProduit(Request $request, EntityManagerInterface $em): Response
    {
        if ($request->isMethod('POST')) {
            $produit = new ProduitPhytosanitaire();
            $produit->setNomProduit($request->request->get('nomProduit'));
            $produit->setDosage($request->request->get('dosage'));
            $produit->setFrequenceApplication($request->request->get('frequence'));
            $produit->setRemarques($request->request->get('remarques'));
            $em->persist($produit);
            $em->flush();
            return $this->redirectToRoute('expert_produits');
        }
        return $this->render('expert/nouveau_produit.html.twig');
    }

    #[Route('/produits/{id}/supprimer', name: 'expert_produit_delete')]
    public function supprimerProduit(ProduitPhytosanitaire $produit, EntityManagerInterface $em): Response
    {
        $em->remove($produit);
        $em->flush();
        return $this->redirectToRoute('expert_produits');
    }
}