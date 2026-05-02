<?php

namespace App\Controller;

use App\Entity\PlansIrrigationJour;
use App\Entity\ProduitsPhytosanitaire;
use App\Repository\DiagnostiRepository;
use App\Repository\ParcelleRepository;
use App\Service\ProduitPhytosanitaireAIService;
use App\Repository\PlansIrrigationJourRepository;
use App\Repository\PlansIrrigationRepository;
use App\Repository\ProduitsPhytosanitaireRepository;
use App\Repository\CultureRepository;
use App\Service\IrrigationSmartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/expert')]
class ExpertController extends AbstractController
{
    // ==================== DASHBOARD EXPERT ====================

    #[Route('', name: 'expert_index')]
    #[Route('/', name: 'expert_home')]
    public function index(
        DiagnostiRepository $diagRepo,
        ProduitsPhytosanitaireRepository $produitRepo,
        PlansIrrigationRepository $planRepo
    ): Response {
        $diagnostics = $diagRepo->findAll();
        $plans = $planRepo->findAll();
        $produits = $produitRepo->findAll();

        $diagEnAttente = 0;
        $diagTraites = 0;
        foreach ($diagnostics as $d) {
            if ($d->getStatut() === 'traite') {
                $diagTraites++;
            } else {
                $diagEnAttente++;
            }
        }

        $plansEnAttente = 0;
        $plansRemplis = 0;
        $plansApprouves = 0;
        foreach ($plans as $p) {
            $statut = $p->getStatut();
            if ($statut === 'rempli') {
                $plansRemplis++;
            } elseif ($statut === 'approuve') {
                $plansApprouves++;
            } else {
                $plansEnAttente++;
            }
        }

        $plansStatutData = [];
        if ($plansEnAttente > 0) $plansStatutData['En attente'] = $plansEnAttente;
        if ($plansRemplis > 0) $plansStatutData['Rempli'] = $plansRemplis;
        if ($plansApprouves > 0) $plansStatutData['Approuvé'] = $plansApprouves;

        return $this->render('expert/dashboard.html.twig', [
            'diagnostics'       => $diagnostics,
            'total_produits'    => count($produits),
            'diag_en_attente'   => $diagEnAttente,
            'diag_traites'      => $diagTraites,
            'total_plans'       => count($plans),
            'plans_en_attente'  => $plansEnAttente,
            'plans_remplis'     => $plansRemplis,
            'plans_approuves'   => $plansApprouves,
            'plans_statut_data' => $plansStatutData,
            'diags_recents'     => array_slice($diagnostics, 0, 5),
            'plans_recents'     => array_slice($plans, 0, 5),
        ]);
    }


    // ==================== DIAGNOSTICS ====================

    #[Route('/diagnostics', name: 'expert_diagnostics')]
    public function diagnostics(DiagnostiRepository $repo): Response
    {
        return $this->render('expert/diagnostics.html.twig', [
            'diagnostics' => $repo->findAll(),
        ]);
    }

    #[Route('/diagnostics/{id}', name: 'expert_diagnostic_detail')]
    public function diagnosticDetail(
        int $id,
        DiagnostiRepository $repo,
        ProduitsPhytosanitaireRepository $produitRepo
    ): Response {
        $diagnostic = $repo->find($id);
        if (!$diagnostic) {
            throw $this->createNotFoundException('Diagnostic introuvable.');
        }
        return $this->render('expert/diagnostic_detail.html.twig', [
            'diagnostic' => $diagnostic,
            'produits'   => $produitRepo->findAll(),
        ]);
    }

    #[Route('/diagnostics/{id}/repondre', name: 'expert_diagnostic_repondre', methods: ['POST'])]
    public function repondre(
        int $id,
        Request $request,
        DiagnostiRepository $repo,
        EntityManagerInterface $em,
    ): Response {
        $diagnostic = $repo->find($id);
        if ($diagnostic) {
            $reponse = (string) $request->request->get('reponse', '');
            $produitTexte = (string) $request->request->get('produit_texte', '');
            if ($produitTexte) {
                $reponse .= "\n\n" . $produitTexte;
            }
            $diagnostic->setReponseExpert($reponse);
            $diagnostic->setStatut('traite');
            $diagnostic->setDateReponse(new \DateTime());
            $em->flush();

            
            $this->addFlash('success', 'Réponse envoyée avec succès.');
        }
        return $this->redirectToRoute('expert_diagnostics');
    }


    // ==================== IRRIGATION ====================

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
        Request $request,
        PlansIrrigationRepository $planRepo,
        PlansIrrigationJourRepository $jourRepo,
        IrrigationSmartService $smartService
    ): Response {
        $plan = $planRepo->find($id);
        if (!$plan) {
            throw $this->createNotFoundException('Plan non trouvé');
        }

        $semaineOffset = (int)$request->query->get('semaine', 0);
        $semaineDebut  = (new \DateTime('monday this week'))
            ->modify(($semaineOffset >= 0 ? '+' : '') . $semaineOffset . ' weeks');

        $jours = $jourRepo->findBy(['plan' => $plan]);
        $jourData = [];
        foreach ($jours as $j) {
            $jourData[$j->getJour()] = $j;
        }

        // Récupérer les infos de besoin de la culture
        $nomCulture = $plan->getNomCulture() ?? 'default';
        $infoCulture = $smartService->getInfoCulture($nomCulture);

        // Calculer les totaux de la semaine
        $totalEau = 0;
        $totalDuree = 0;
        foreach ($jourData as $j) {
            $totalEau += $j->getEauMm() ?? 0;
            $totalDuree += $j->getTempsMin() ?? 0;
        }

        return $this->render('expert/irrigation_detail.html.twig', [
            'plan'           => $plan,
            'jourData'       => $jourData,
            'semaineDebut'   => $semaineDebut,
            'semaineOffset'  => $semaineOffset,
            'infoCulture'    => $infoCulture,
            'totalEau'       => round($totalEau, 2),
            'totalDuree'     => $totalDuree,
        ]);
    }

    #[Route('/irrigation/{id}/save', name: 'expert_irrigation_save', methods: ['POST'])]
    public function irrigationSave(
        int $id,
        Request $request,
        PlansIrrigationRepository $planRepo,
        PlansIrrigationJourRepository $jourRepo,
        EntityManagerInterface $em,
    ): Response {
        $plan = $planRepo->find($id);
        if (!$plan) {
            throw $this->createNotFoundException('Plan non trouvé');
        }

        $joursKeys    = ['LUN', 'MAR', 'MER', 'JEU', 'VEN', 'SAM', 'DIM'];
        $semaineDebut = new \DateTime('monday this week');

        foreach ($joursKeys as $k) {
            $jour = $jourRepo->findOneBy(['plan' => $plan, 'jour' => $k]);
            if (!$jour) {
                $jour = new PlansIrrigationJour();
                $jour->setPlan($plan);
                $jour->setJour($k);
                $jour->setSemaineDebut($semaineDebut);
                $em->persist($jour);
            }
            $jour->setEauMm((float)$request->request->get('eau_' . $k, 0));
            $jour->setTempsMin((int)$request->request->get('duree_' . $k, 0));
            $jour->setTempC((float)$request->request->get('temp_' . $k, 0));
            $jour->setHumidite((float)$request->request->get('hum_' . $k, 0));
            $jour->setPluie((float)$request->request->get('pluie_' . $k, 0));
        }

        $plan->setStatut('rempli');
        $em->flush();

        
        $this->addFlash('success', 'Plan enregistré avec succès !');
        return $this->redirectToRoute('expert_irrigation_detail', ['id' => $id]);
    }

#[Route('/irrigation/{id}/ia', name: 'expert_irrigation_ia', methods: ['POST'])]
public function irrigationIA(
    int $id,
    PlansIrrigationRepository $planRepo,
    PlansIrrigationJourRepository $jourRepo,
    IrrigationSmartService $smartService,
    CultureRepository $cultureRepo,
    EntityManagerInterface $em
): Response {
    $plan = $planRepo->find($id);
    if (!$plan) {
        throw $this->createNotFoundException('Plan non trouvé');
    }

    try {
        $lat = 36.8;
        $lon = 10.18;

        // Récupérer le nom et la superficie de la culture
        $nomCulture = $plan->getNomCulture() ?? 'default';
        $superficie = 1.0;

        // Si le plan est lié à une culture, récupérer la superficie
        $culture = $plan->getCulture();
        if ($culture) {
            $superficie = $culture->getSuperficie() ?? 1.0;
            $nomCulture = $culture->getNom() ?? $nomCulture;
        }

        $besoin = $plan->getVolumeEauPropose() ?? 100;
        $planIA = $smartService->genererPlanIA($besoin, $lat, $lon, $nomCulture, $superficie);

        $semaineDebut = new \DateTime('monday this week');
        foreach ($planIA as $key => $valeurs) {
            $jour = $jourRepo->findOneBy(['plan' => $plan, 'jour' => $key]);
            if (!$jour) {
                $jour = new PlansIrrigationJour();
                $jour->setPlan($plan);
                $jour->setJour($key);
                $jour->setSemaineDebut($semaineDebut);
                $em->persist($jour);
            }
            $jour->setEauMm($valeurs['eau_mm']);
            $jour->setTempsMin($valeurs['duree']);
            $jour->setTempC($valeurs['temp']);
            $jour->setHumidite($valeurs['humidite']);
            $jour->setPluie($valeurs['pluie']);
        }

        $plan->setStatut('rempli');
        $em->flush();
        $this->addFlash('success', 'Plan optimisé avec l\'IA météo pour "' . $nomCulture . '" !');
    } catch (\Exception $e) {
        $this->addFlash('danger', 'Erreur IA : ' . $e->getMessage());
    }

    return $this->redirectToRoute('expert_irrigation_detail', ['id' => $id]);
}

    // ==================== PRODUITS ====================

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
            $produit->setNomProduit((string) $request->request->get('nomProduit', ''));
            $produit->setDosage((string) $request->request->get('dosage', ''));
            $produit->setFrequenceApplication((string) $request->request->get('frequence', ''));
            $produit->setRemarques((string) $request->request->get('remarques', ''));
            $em->persist($produit);
            $em->flush();
            return $this->redirectToRoute('expert_produits');
        }
        return $this->render('expert/nouveau_produit.html.twig');
    }
    #[Route('/produits/suggerer', name: 'expert_produit_suggest', methods: ['POST'])]
    public function suggererProduit(
        Request $request,
        ProduitPhytosanitaireAIService $aiService
    ): Response {
        $nomProduit = trim((string) $request->request->get('nomProduit', ''));

        if (strlen($nomProduit) < 3) {
            return $this->json(['error' => 'Nom du produit trop court.'], 400);
        }

        try {
            $suggestion = $aiService->suggereProduit($nomProduit);
            return $this->json($suggestion);
        } catch (\Throwable $e) {
            return $this->json(['error' => 'Erreur IA : ' . $e->getMessage()], 500);
        }
    }



    #[Route('/produits/{id}/supprimer', name: 'expert_produit_delete')]
    public function supprimerProduit(
        int $id,
        ProduitsPhytosanitaireRepository $repo,
        EntityManagerInterface $em
    ): Response {
        $produit = $repo->find($id);
        if ($produit) {
            $em->remove($produit);
            $em->flush();
        }
        return $this->redirectToRoute('expert_produits');
    }
}
