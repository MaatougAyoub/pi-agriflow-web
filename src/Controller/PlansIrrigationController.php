<?php

namespace App\Controller;

use App\Entity\PlansIrrigation;
use App\Repository\PlansIrrigationJourRepository;
use App\Repository\PlansIrrigationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/plan-irrigation')]
class PlansIrrigationController extends AbstractController
{
    #[Route('/liste', name: 'plan_index')]
    public function index(PlansIrrigationRepository $repo): Response
    {
        if ($this->isGranted('ROLE_EXPERT')) {
            $plans = $repo->findAll();
        } else {
            $user = $this->getUser();
            $plans = $repo->findBy(['id_culture' => null]);
            // On récupère tous les plans pour l'agriculteur connecté
            $plans = $repo->findAll(); // À adapter selon votre logique utilisateur
        }

        return $this->render('plan_irrigation/index.html.twig', [
            'plans'     => $plans,
            'is_expert' => $this->isGranted('ROLE_EXPERT'),
        ]);
    }

    #[Route('/{id}/detail', name: 'plan_detail')]
    public function detail(
        int $id,
        PlansIrrigationRepository $planRepo,
        PlansIrrigationJourRepository $jourRepo
    ): Response {
        $plan = $planRepo->find($id);
        if (!$plan) {
            throw $this->createNotFoundException('Plan non trouvé');
        }
        $jours = $jourRepo->findBy(['plan' => $plan]);
        $jourData = [];
        foreach ($jours as $j) {
            $jourKey = $j->getJourKey();
            if ($jourKey !== null) {
                $jourData[$jourKey] = $j;
            }
        }
        return $this->render('plan_irrigation/detail.html.twig', [
            'plan'     => $plan,
            'jourData' => $jourData,
        ]);
    }

    #[Route('/nouveau', name: 'plan_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        if ($request->isMethod('POST')) {
            $plan = new PlansIrrigation();
            $nomCulture = $request->request->get('nom_culture');
            $nomCulture = is_string($nomCulture) ? $nomCulture : null;
            $plan->setNomCulture($nomCulture);
            $plan->setVolumeEauPropose((float)$request->request->get('volume_eau_propose'));
            $plan->setStatut('en_attente');
            $plan->setDateDemande(new \DateTime());
            $em->persist($plan);
            $em->flush();
            return $this->redirectToRoute('plan_index');
        }
        return $this->render('plan_irrigation/new.html.twig');
    }

    #[Route('/{id}/approuver', name: 'plan_approuver')]
    public function approuver(int $id, PlansIrrigationRepository $repo, EntityManagerInterface $em): Response
    {
        $plan = $repo->find($id);
        if ($plan) {
            $plan->setStatut('approuve');
            $em->flush();
        }
        return $this->redirectToRoute('plan_index');
    }

    #[Route('/{id}/supprimer', name: 'plan_delete')]
    public function delete(int $id, PlansIrrigationRepository $repo, EntityManagerInterface $em): Response
    {
        $plan = $repo->find($id);
        if ($plan) {
            $em->remove($plan);
            $em->flush();
        }
        return $this->redirectToRoute('plan_index');
    }
}