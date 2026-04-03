<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

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
}