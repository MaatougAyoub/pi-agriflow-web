<?php

namespace App\Controller;

use App\Entity\PlansIrrigationJour;
use App\Entity\ProduitsPhytosanitaire;
use App\Repository\DiagnostiRepository;
use App\Repository\PlansIrrigationJourRepository;
use App\Repository\PlansIrrigationRepository;
use App\Repository\ProduitsPhytosanitaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/expert')]
class ExpertController extends AbstractController
{
    #[Route('/diagnostics', name: 'expert_diagnostics')]
    public function diagnostics(DiagnostiRepository $repo): Response
    {
        return $this->render('expert/diagnostics.html.twig', [
            'diagnostics' => $repo->findAll(),
        ]);
    }

    #[Route('/diagnostics/{id}/repondre', name: 'expert_diagnostic_repondre', methods: ['POST'])]
    public function repondre(int $id, Request $request, DiagnostiRepository $repo, EntityManagerInterface $em): Response
    {
        $diagnostic = $repo->find($id);
        if ($diagnostic) {
            $diagnostic->setReponseExpert($request->request->get('reponse'));
            $diagnostic->setStatut('traite');
            $diagnostic->setDateReponse(new \DateTime());
            $em->flush();
        }
        return $this->redirectToRoute('expert_diagnostics');
    }

    #[Route('/irrigation', name: 'expert_irrigation')]
    public function irrigation(PlansIrrigationRepository $repo): Response
    {
        return $this->render('expert/irrigation.html.twig', [
            'plans' => $repo->findAll(),
        ]);
    }

    #[Route('/irrigation/{id}', name: 'expert_irrigation_detail')]
    public function irrigationDetail(
        int $id,
        PlansIrrigationRepository $planRepo,
        PlansIrrigationJourRepository $jourRepo
    ): Response {
        $plan = $planRepo->find($id);
        if (!$plan) {
            throw $this->createNotFoundException('Plan non trouvé');
        }

        $jours = $jourRepo->findBy(['plan_id' => $id]);
        $jourData = [];
        foreach ($jours as $j) {
            $jourData[$j->getJour()] = $j;
        }

        return $this->render('expert/irrigation_detail.html.twig', [
            'plan'     => $plan,
            'jourData' => $jourData,
        ]);
    }

    #[Route('/irrigation/{id}/save', name: 'expert_irrigation_save', methods: ['POST'])]
    public function irrigationSave(
        int $id,
        Request $request,
        PlansIrrigationRepository $planRepo,
        PlansIrrigationJourRepository $jourRepo,
        EntityManagerInterface $em
    ): Response {
        $plan = $planRepo->find($id);
        if (!$plan) {
            throw $this->createNotFoundException('Plan non trouvé');
        }

        $joursKeys = ['LUN', 'MAR', 'MER', 'JEU', 'VEN', 'SAM', 'DIM'];
        $semaineDebut = new \DateTime('monday this week');

        foreach ($joursKeys as $jourKey) {
            // Récupérer ou créer l'entité pour ce jour
            $jour = $jourRepo->findOneBy(['plan_id' => $id, 'jour' => $jourKey]);
            if (!$jour) {
                $jour = new PlansIrrigationJour();
                $jour->setPlanId($id);
                $jour->setJour($jourKey);
                $jour->setSemaineDebut($semaineDebut);
                $em->persist($jour);
            }

            // Mise à jour des valeurs depuis le formulaire
            $jour->setEauMm((float) $request->request->get('eau_' . $jourKey, 0));
            $jour->setTempsMin((int) $request->request->get('duree_' . $jourKey, 0));
            $jour->setTempC((float) $request->request->get('temp_' . $jourKey, 0));
            $jour->setHumidite((float) $request->request->get('hum_' . $jourKey, 0));
            $jour->setPluie((float) $request->request->get('pluie_' . $jourKey, 0));
        }

        $plan->setStatut('rempli');
        $em->flush();

        $this->addFlash('success', 'Planning enregistré avec succès !');
        return $this->redirectToRoute('expert_irrigation');
    }
    #[Route('/produits', name: 'expert_produits')]
    public function produits(ProduitsPhytosanitaireRepository $repo): Response
    {
        return $this->render('expert/produits.html.twig', [
            'produits' => $repo->findAll(),
        ]);
    }

    #[Route('/produits/nouveau', name: 'expert_produit_new')]
    public function nouveauProduit(Request $request, EntityManagerInterface $em): Response
    {
        if ($request->isMethod('POST')) {
            $produit = new ProduitsPhytosanitaire();
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
    public function supprimerProduit(int $id, ProduitsPhytosanitaireRepository $repo, EntityManagerInterface $em): Response
    {
        $produit = $repo->find($id);
        if ($produit) {
            $em->remove($produit);
            $em->flush();
        }
        return $this->redirectToRoute('expert_produits');
    }
}