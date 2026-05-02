<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Annonce;
use App\Entity\Reservation;
use App\Entity\Utilisateur;
use App\Enum\AnnonceStatut;
use App\Enum\AnnonceType;
use App\Form\FrontReservationType;
use App\Repository\AnnonceRepository;
use App\Repository\ReservationRepository;
use App\Service\AnnonceBusinessDiagnosticService;
use App\Service\AnnonceEnvironmentInsightService;
use App\Service\SellerMarketplaceService;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/marketplace', name: 'app_marketplace_')]
final class MarketplaceController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(
        Request $request,
        AnnonceRepository $annonceRepository,
        PaginatorInterface $paginator
    ): Response
    {
        // filter: houni njiib texte recherche w type men query string mta3 page marketplace
        $keyword = $request->query->getString('q');
        $type = AnnonceType::tryFrom((string) $request->query->get('type'));
        // view: n5admou query builder bech KnpPaginator y9assem liste marketplace b page propre
        $annonces = $paginator->paginate(
            $annonceRepository->createPublicSearchQueryBuilder($keyword, $type),
            $request->query->getInt('page', 1),
            6
        );
        // view: searchPublic njiibou beha kol annonces filtrées bech stats yeb9aw exact
        $statsAnnonces = $annonceRepository->searchPublic($keyword, $type);
        $stats = [
            'total' => count($statsAnnonces),
            'vente' => 0,
            'location' => 0,
            'zones' => 0,
        ];
        $localisations = [];

        // stats: houni n7sbou chiffres sghar bech nwarri resume 3la catalogue
        foreach ($statsAnnonces as $annonce) {
            if ($annonce->isLocation()) {
                ++$stats['location'];
            } else {
                ++$stats['vente'];
            }

            $localisations[] = $annonce->getLocalisation();
        }

        $stats['zones'] = count(array_unique($localisations));

        return $this->render('marketplace/index.html.twig', [
            'annonces' => $annonces,
            'filters' => [
                'q' => $keyword,
                'type' => $type?->value,
            ],
            'stats' => $stats,
            'types' => AnnonceType::cases(),
            'visuals' => $this->buildAnnonceVisuals(array_values(iterator_to_array($annonces))),
        ]);
    }

    #[Route('/annonce/{id}', name: 'show', methods: ['GET'])]
    public function show(
        #[MapEntity(mapping: ['id' => 'id'])] Annonce $annonce,
        AnnonceRepository $annonceRepository,
        ReservationRepository $reservationRepository,
        AnnonceBusinessDiagnosticService $businessDiagnosticService,
        AnnonceEnvironmentInsightService $environmentInsightService,
        SellerMarketplaceService $sellerMarketplaceService
    ): Response {
        $user = $this->getUser();
        $viewer = $user instanceof Utilisateur ? $user : null;
        // role: houni nfarkou bin owner w viewer bech kol wahed ychouf el actions eli t5assou
        $isAgriculteurViewer = $viewer instanceof Utilisateur && $this->isGranted('ROLE_AGRICULTEUR');
        $isOwnerViewer = $sellerMarketplaceService->isAnnonceOwner($viewer, $annonce);
        // reservation: canReserve t7eb annonce disponible w viewer agriculteur w moch owner
        $canReserve = $isAgriculteurViewer
            && !$isOwnerViewer
            && $annonce->getStatut() === AnnonceStatut::DISPONIBLE;
        /** @var list<Annonce> $similarAnnonces */
        $similarAnnonces = array_values($annonceRepository->findSimilarPublic($annonce, 4));
        $environmentInsights = $environmentInsightService->buildForAnnonce($annonce);
        $businessDiagnostic = $businessDiagnosticService->buildForAnnonce($annonce, $environmentInsights);
        $reservationForm = null;
        if ($canReserve) {
            $reservationForm = $this->createReservationForm($annonce, $viewer)->createView();
        }

        return $this->render('marketplace/show.html.twig', [
            'annonce' => $annonce,
            'reservationForm' => $reservationForm,
            'recentReservations' => $reservationRepository->findBy(
                ['annonce' => $annonce],
                ['createdAt' => 'DESC'],
                5
            ),
            'visual' => $this->buildAnnonceVisual($annonce),
            // api: Open-Meteo yet7seb mel coordonnees eli jethom men geocoding
            'environmentInsights' => $environmentInsights,
            'businessDiagnostic' => $businessDiagnostic,
            'similarAnnonces' => $similarAnnonces,
            'similarVisuals' => $this->buildAnnonceVisuals($similarAnnonces),
            'canReserve' => $canReserve,
            'isAdminViewer' => $this->isGranted('ROLE_ADMIN'),
            'isAgriculteurViewer' => $isAgriculteurViewer,
            'isOwnerViewer' => $isOwnerViewer,
        ]);
    }

    private function createReservationForm(Annonce $annonce, Utilisateur $user): FormInterface
    {
        // reservation: clientId yji automatique men user connecte bech ma n5alliwch champ technique fil front
        $reservation = (new Reservation())->setClientId($user->getId() ?? 0);

        if (!$annonce->isLocation()) {
            $today = new \DateTimeImmutable('today');
            $reservation
                ->setDateDebut($today)
                ->setDateFin($today);
        }

        // reservation: n7adhrou form wa7dou bech detail page tab9a wadh7a w POST yimchi route m5asssa
        return $this->createForm(FrontReservationType::class, $reservation, [
            'action' => $this->generateUrl('app_reservation_create', ['id' => $annonce->getId()]),
            'method' => 'POST',
            'is_location' => $annonce->isLocation(),
        ]);
    }

    /**
     * @param list<Annonce> $annonces
     *
     * @return array<int, array{image: string, alt: string, position: string, isExternal: bool}>
     */
    private function buildAnnonceVisuals(array $annonces): array
    {
        $visuals = [];

        // view: n7adhrou visuel kol carte mara wa7da bech twig yeb9a ashel
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

        // image: ken seller 7at lien s7i7 n5admouh 9bal ay image par defaut
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

        // image: houni na3ti fallback local 3la 7sab titre/categorie bech visuel ma ykounch 3achwa2i
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
