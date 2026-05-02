<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Annonce;
use App\Entity\Reservation;
use App\Entity\Utilisateur;
use App\Enum\ReservationStatut;
use App\Form\FrontReservationType;
use App\Repository\AnnonceRepository;
use App\Repository\ReservationRepository;
use App\Service\ReservationPricingService;
use App\Service\ReservationPdfService;
use App\Service\SellerMarketplaceService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class ReservationController extends AbstractController
{
    #[IsGranted('ROLE_AGRICULTEUR')]
    #[Route('/marketplace/{id}/reserve', name: 'app_reservation_create', methods: ['POST'])]
    public function create(
        Request $request,
        #[MapEntity(mapping: ['id' => 'id'])] Annonce $annonce,
        EntityManagerInterface $entityManager,
        ReservationPricingService $pricingService,
        AnnonceRepository $annonceRepository,
        ReservationRepository $reservationRepository,
        SellerMarketplaceService $sellerMarketplaceService
    ): Response {
        $user = $this->getUser();

        // security: route hedhi ma tnajjemch tet3ada ken b compte agriculteur s7i7
        if (!$user instanceof Utilisateur) {
            throw $this->createAccessDeniedException('Connexion agriculteur requise.');
        }

        $userId = $user->getId();
        if ($userId === null) {
            throw $this->createAccessDeniedException('Connexion agriculteur requise.');
        }

        try {
            // metier: houni n9ablou ken annonce disponible w moch annonce mta3 nafs user
            $sellerMarketplaceService->ensureCanReserve($user, $annonce);
        } catch (\DomainException $exception) {
            $this->addFlash('danger', $exception->getMessage());

            return $this->redirectToRoute('app_marketplace_show', ['id' => $annonce->getId()]);
        }

        // reservation: demande jdida tabda toujours EN_ATTENTE w clientId men compte connecte
        $reservation = (new Reservation())
            ->setAnnonce($annonce)
            ->setStatut(ReservationStatut::EN_ATTENTE)
            ->setClientId($userId);

        if (!$annonce->isLocation()) {
            $today = new \DateTimeImmutable('today');
            $reservation
                ->setDateDebut($today)
                ->setDateFin($today);
        }

        $form = $this->createForm(FrontReservationType::class, $reservation, [
            'action' => $this->generateUrl('app_reservation_create', ['id' => $annonce->getId()]),
            'method' => 'POST',
            'is_location' => $annonce->isLocation(),
        ]);
        $form->handleRequest($request);

        if ($annonce->isLocation() && $form->isSubmitted() && $form->isValid()) {
            $today = new \DateTimeImmutable('today');

            // date: fil front ma n5alliwch reservation tabda fi date fetet
            if (null !== $reservation->getDateDebut() && $reservation->getDateDebut() < $today) {
                $form->get('dateDebut')->addError(new FormError('La date de debut ne peut pas etre dans le passe.'));
            }

            // date: nafs logique l date fin zeda bech demande 9dima ma tet3addach
            if (null !== $reservation->getDateFin() && $reservation->getDateFin() < $today) {
                $form->get('dateFin')->addError(new FormError('La date de fin ne peut pas etre dans le passe.'));
            }
        }

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                // pricing: houni service y7sb prix total w commission w y3abbi proprietaireId
                $pricingService->hydrateReservation($reservation);

                $entityManager->persist($reservation);
                $entityManager->flush();

                $this->addFlash('success', 'Reservation enregistree avec succes.');

                return $this->redirectToRoute('app_marketplace_show', ['id' => $annonce->getId()]);
            } catch (\DomainException $exception) {
                $form->addError(new FormError($exception->getMessage()));
            }
        }

        // validation: ken fama erreur nrendrou nafs page b status 422 bech feedback yban
        $this->addFlash('danger', 'Le formulaire de reservation contient des erreurs.');
        /** @var list<Annonce> $similarAnnonces */
        $similarAnnonces = array_values($annonceRepository->findSimilarPublic($annonce, 4));

        return $this->render('marketplace/show.html.twig', [
            'annonce' => $annonce,
            'reservationForm' => $form->createView(),
            'recentReservations' => $reservationRepository->findBy(
                ['annonce' => $annonce],
                ['createdAt' => 'DESC'],
                5
            ),
            'visual' => $this->buildAnnonceVisual($annonce),
            'similarAnnonces' => $similarAnnonces,
            'similarVisuals' => $this->buildAnnonceVisuals($similarAnnonces),
            'canReserve' => true,
            'isAgriculteurViewer' => true,
            'isAdminViewer' => false,
            'isOwnerViewer' => false,
        ], new Response(status: Response::HTTP_UNPROCESSABLE_ENTITY));
    }

    #[IsGranted('ROLE_AGRICULTEUR')]
    #[Route('/mes-reservations', name: 'app_reservation_my', methods: ['GET'])]
    public function myReservations(
        Request $request,
        ReservationRepository $reservationRepository,
        PaginatorInterface $paginator
    ): Response
    {
        $user = $this->getUser();

        // security: mes reservations marboutin b agriculteur connecte bark
        if (!$user instanceof Utilisateur) {
            throw $this->createAccessDeniedException('Connexion agriculteur requise.');
        }

        $userId = $user->getId();
        if ($userId === null) {
            throw $this->createAccessDeniedException('Connexion agriculteur requise.');
        }

        // reservation: hedhi liste demandes eli user ba3athhom comme client
        return $this->render('reservation/my_reservations.html.twig', [
            'reservations' => $paginator->paginate(
                $reservationRepository->createByClientIdForMarketplaceQueryBuilder($userId),
                $request->query->getInt('page', 1),
                8
            ),
        ]);
    }

    #[IsGranted('ROLE_AGRICULTEUR')]
    #[Route('/mes-reservations/{id}/devis', name: 'app_reservation_quote', methods: ['GET'])]
    public function quote(
        #[MapEntity(mapping: ['id' => 'id'])] Reservation $reservation,
        ReservationPdfService $reservationPdfService
    ): Response {
        $user = $this->getUser();

        if (!$user instanceof Utilisateur) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas acceder a ce devis.');
        }

        $userId = $user->getId();
        if ($userId === null || $reservation->getClientId() !== $userId) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas acceder a ce devis.');
        }

        return $reservationPdfService->streamReservationQuote($reservation, 'devis-client');
    }

    /**
     * @param list<Annonce> $annonces
     *
     * @return array<int, array{image: string, alt: string, position: string, isExternal: bool}>
     */
    private function buildAnnonceVisuals(array $annonces): array
    {
        $visuals = [];

        foreach ($annonces as $annonce) {
            $visuals[$annonce->getId() ?? 0] = $this->buildAnnonceVisual($annonce);
        }

        return $visuals;
    }

    /**
     * @return array{image: string, alt: string, position: string, isExternal: bool}
     */
    private function buildAnnonceVisual(Annonce $annonce): array
    {
        $title = strtolower((string) $annonce->getTitre());
        $category = strtolower((string) $annonce->getCategorie());
        $imageUrl = trim((string) ($annonce->getImageUrl() ?? ''));

        // image: na5thou image mta3 seller ki tkoun URL s7i7a bech detail yeb9a nafes rendu
        if ('' !== $imageUrl && false !== filter_var($imageUrl, FILTER_VALIDATE_URL)) {
            $host = strtolower((string) parse_url($imageUrl, PHP_URL_HOST));

            if (!in_array($host, ['example.com', 'www.example.com'], true)) {
                return [
                    'image' => $imageUrl,
                    'alt' => (string) $annonce->getTitre(),
                    'position' => 'center center',
                    'isExternal' => true,
                ];
            }
        }

        // image: fallback local yb9a mawjoued ken ma fama hata image externe s7i7a
        if (str_contains($title, 'tracteur') || str_contains($category, 'materiel')) {
            return [
                'image' => 'uploads/marketplace/tracteur-cover.jpg',
                'alt' => 'Tracteur agricole sur terrain laboure',
                'position' => 'center center',
                'isExternal' => false,
            ];
        }

        if (str_contains($title, 'pompe') || str_contains($category, 'irrigation')) {
            return [
                'image' => 'uploads/marketplace/pompe-irrigation-cover.jpg',
                'alt' => 'Pompe d irrigation mobile',
                'position' => 'center center',
                'isExternal' => false,
            ];
        }

        if (str_contains($title, 'tomate') || str_contains($category, 'produits')) {
            return [
                'image' => 'uploads/marketplace/tomates-caisses-cover.jpg',
                'alt' => 'Caisses de tomates fraiches',
                'position' => 'center center',
                'isExternal' => false,
            ];
        }

        return [
            'image' => 'template/assets/img/hero_4.jpg',
            'alt' => (string) $annonce->getTitre(),
            'position' => 'center center',
            'isExternal' => false,
        ];
    }
}
