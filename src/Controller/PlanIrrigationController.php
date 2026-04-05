<?php

namespace App\Controller;

use App\Entity\PlanIrrigation;
use App\Repository\CultureRepository;
use App\Repository\PlanIrrigationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/plan-irrigation')]
class PlanIrrigationController extends AbstractController
{
    #[Route('/', name: 'plan_index')]
    public function index(PlanIrrigationRepository $repo): Response
    {
        return $this->render('plan_irrigation/index.html.twig', [
            'plans' => $repo->findAllWithCulture(),
        ]);
    }

    #[Route('/nouveau', name: 'plan_new')]
    public function new(Request $request, EntityManagerInterface $em, CultureRepository $cultureRepo): Response
    {
        if ($request->isMethod('POST')) {
            $culture = $cultureRepo->find($request->request->get('culture_id'));
            if ($culture) {
                $plan = new PlanIrrigation();
                $plan->setCulture($culture);
                $plan->setBesoinEau($culture->calculerBesoinEau());
                $plan->setStatut($request->request->get('statut', 'brouillon'));
                $em->persist($plan);
                $em->flush();
            }
            return $this->redirectToRoute('plan_index');
        }
        return $this->render('plan_irrigation/new.html.twig', [
            'cultures' => $cultureRepo->findAll(),
        ]);
    }

    #[Route('/{id}/approuver', name: 'plan_approuver')]
    public function approuver(PlanIrrigation $plan, EntityManagerInterface $em): Response
    {
        $plan->setStatut('approuvé');
        $em->flush();
        return $this->redirectToRoute('plan_index');
    }

    #[Route('/{id}/supprimer', name: 'plan_delete')]
    public function delete(PlanIrrigation $plan, EntityManagerInterface $em): Response
    {
        $em->remove($plan);
        $em->flush();
        return $this->redirectToRoute('plan_index');
    }
}