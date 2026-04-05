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
use App\Service\SellerMarketplaceService;
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
    public function index(Request $request, AnnonceRepository $annonceRepository): Response
    {
        $keyword = $request->query->getString('q');
        $type = AnnonceType::tryFrom((string) $request->query->get('type'));
        $annonces = $annonceRepository->searchPublic($keyword, $type);
        $stats = [
            'total' => count($annonces),
            'vente' => 0,
            'location' => 0,
            'zones' => 0,
        ];
        $localisations = [];

        foreach ($annonces as $annonce) {
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
            'visuals' => $this->buildAnnonceVisuals($annonces),
        ]);
    }

    #[Route('/annonce/{id}', name: 'show', methods: ['GET'])]
    public function show(
        #[MapEntity(mapping: ['id' => 'id'])] Annonce $annonce,
        ReservationRepository $reservationRepository,
        SellerMarketplaceService $sellerMarketplaceService
    ): Response {
        $user = $this->getUser();
        $isAgriculteurViewer = $user instanceof Utilisateur && $this->isGranted('ROLE_AGRICULTEUR');
        $isOwnerViewer = $sellerMarketplaceService->isAnnonceOwner(
            $user instanceof Utilisateur ? $user : null,
            $annonce
        );
        $canReserve = $isAgriculteurViewer
            && !$isOwnerViewer
            && $annonce->getStatut() === AnnonceStatut::DISPONIBLE;

        return $this->render('marketplace/show.html.twig', [
            'annonce' => $annonce,
            'reservationForm' => $canReserve && $user instanceof Utilisateur
                ? $this->createReservationForm($annonce, $user)->createView()
                : null,
            'recentReservations' => $reservationRepository->findBy(
                ['annonce' => $annonce],
                ['createdAt' => 'DESC'],
                5
            ),
            'visual' => $this->buildAnnonceVisual($annonce),
            'canReserve' => $canReserve,
            'isAdminViewer' => $this->isGranted('ROLE_ADMIN'),
            'isAgriculteurViewer' => $isAgriculteurViewer,
            'isOwnerViewer' => $isOwnerViewer,
        ]);
    }

    private function createReservationForm(Annonce $annonce, Utilisateur $user): FormInterface
    {
        $reservation = (new Reservation())->setClientId($user->getId() ?? 0);

        // n5alliw action wa7adha bech page details tab9a lisible w POST yetsayyar wahdou
        return $this->createForm(FrontReservationType::class, $reservation, [
            'action' => $this->generateUrl('app_reservation_create', ['id' => $annonce->getId()]),
            'method' => 'POST',
        ]);
    }

    /**
     * @param list<Annonce> $annonces
     *
     * @return array<int, array{image: string, alt: string, position: string}>
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
     * @return array{image: string, alt: string, position: string}
     */
    private function buildAnnonceVisual(Annonce $annonce): array
    {
        $title = strtolower((string) $annonce->getTitre());
        $category = strtolower((string) $annonce->getCategorie());

        // houni na3ti kol annonce image local mefhoma bech ma ykounch visuel 3achwa2i
        if (str_contains($title, 'tracteur') || str_contains($category, 'materiel')) {
            return [
                'image' => 'uploads/marketplace/tracteur-cover.jpg',
                'alt' => 'Tracteur agricole sur terrain laboure',
                'position' => 'center center',
            ];
        }

        if (str_contains($title, 'pompe') || str_contains($category, 'irrigation')) {
            return [
                'image' => 'uploads/marketplace/pompe-irrigation-cover.jpg',
                'alt' => 'Pompe d irrigation mobile',
                'position' => 'center center',
            ];
        }

        if (str_contains($title, 'tomate') || str_contains($category, 'produits')) {
            return [
                'image' => 'uploads/marketplace/tomates-caisses-cover.jpg',
                'alt' => 'Caisses de tomates fraiches',
                'position' => 'center center',
            ];
        }

        return [
            'image' => 'template/assets/img/hero_4.jpg',
            'alt' => (string) $annonce->getTitre(),
            'position' => 'center center',
        ];
    }
}
