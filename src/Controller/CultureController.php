<?php

namespace App\Controller;

use App\Entity\Culture;
use App\Repository\CultureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/culture')]
class CultureController extends AbstractController
{
    #[Route('/', name: 'culture_index')]
    public function index(CultureRepository $repo): Response
    {
        return $this->render('culture/index.html.twig', [
            'cultures' => $repo->findAll(),
        ]);
    }

    #[Route('/nouveau', name: 'culture_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        if ($request->isMethod('POST')) {
            $culture = new Culture();
            $culture->setNom($request->request->get('nom'));
            $culture->setTypeCulture($request->request->get('typeCulture'));
            $culture->setSuperficie((float)$request->request->get('superficie'));
            $em->persist($culture);
            $em->flush();
            return $this->redirectToRoute('culture_index');
        }
        return $this->render('culture/new.html.twig', [
            'types' => Culture::TYPES,
        ]);
    }

    #[Route('/{id}/modifier', name: 'culture_edit')]
    public function edit(Culture $culture, Request $request, EntityManagerInterface $em): Response
    {
        if ($request->isMethod('POST')) {
            $culture->setNom($request->request->get('nom'));
            $culture->setTypeCulture($request->request->get('typeCulture'));
            $culture->setSuperficie((float)$request->request->get('superficie'));
            $em->flush();
            return $this->redirectToRoute('culture_index');
        }
        return $this->render('culture/edit.html.twig', [
            'culture' => $culture,
            'types' => Culture::TYPES,
        ]);
    }

    #[Route('/{id}/supprimer', name: 'culture_delete')]
    public function delete(Culture $culture, EntityManagerInterface $em): Response
    {
        $em->remove($culture);
        $em->flush();
        return $this->redirectToRoute('culture_index');
    }
}