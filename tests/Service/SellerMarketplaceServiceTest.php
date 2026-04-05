<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Entity\Annonce;
use App\Entity\Reservation;
use App\Entity\Utilisateur;
use App\Enum\AnnonceStatut;
use App\Enum\AnnonceType;
use App\Enum\ReservationStatut;
use App\Service\SellerMarketplaceService;
use PHPUnit\Framework\TestCase;

final class SellerMarketplaceServiceTest extends TestCase
{
    public function testEnsureCanReserveRejectsOwnerAnnonce(): void
    {
        $user = (new Utilisateur())->setId(14);
        $annonce = $this->createAnnonce(14, AnnonceType::LOCATION, 3, AnnonceStatut::DISPONIBLE);

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Vous ne pouvez pas reserver votre propre annonce.');

        (new SellerMarketplaceService())->ensureCanReserve($user, $annonce);
    }

    public function testAcceptReservationClosesLocationWhenStockReachesZero(): void
    {
        $annonce = $this->createAnnonce(7, AnnonceType::LOCATION, 2, AnnonceStatut::DISPONIBLE);
        $reservation = (new Reservation())
            ->setAnnonce($annonce)
            ->setClientId(22)
            ->setProprietaireId(7)
            ->setQuantite(2)
            ->setStatut(ReservationStatut::EN_ATTENTE);

        (new SellerMarketplaceService())->acceptReservation($reservation);

        self::assertSame(0, $annonce->getQuantiteDisponible());
        self::assertSame(AnnonceStatut::LOUEE, $annonce->getStatut());
        self::assertSame(ReservationStatut::ACCEPTEE, $reservation->getStatut());
    }

    public function testAcceptReservationThrowsWhenQuantityIsTooHigh(): void
    {
        $annonce = $this->createAnnonce(9, AnnonceType::VENTE, 1, AnnonceStatut::DISPONIBLE);
        $reservation = (new Reservation())
            ->setAnnonce($annonce)
            ->setClientId(30)
            ->setProprietaireId(9)
            ->setQuantite(4)
            ->setStatut(ReservationStatut::EN_ATTENTE);

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Le stock disponible ne permet plus d accepter cette demande.');

        (new SellerMarketplaceService())->acceptReservation($reservation);
    }

    public function testRefuseReservationKeepsStockUntouched(): void
    {
        $annonce = $this->createAnnonce(4, AnnonceType::VENTE, 8, AnnonceStatut::DISPONIBLE);
        $reservation = (new Reservation())
            ->setAnnonce($annonce)
            ->setClientId(19)
            ->setProprietaireId(4)
            ->setQuantite(2)
            ->setStatut(ReservationStatut::EN_ATTENTE);

        (new SellerMarketplaceService())->refuseReservation($reservation);

        self::assertSame(8, $annonce->getQuantiteDisponible());
        self::assertSame(AnnonceStatut::DISPONIBLE, $annonce->getStatut());
        self::assertSame(ReservationStatut::REFUSEE, $reservation->getStatut());
    }

    private function createAnnonce(
        int $ownerId,
        AnnonceType $type,
        int $stock,
        AnnonceStatut $statut
    ): Annonce {
        return (new Annonce())
            ->setTitre('Annonce test')
            ->setDescription('Description assez longue pour garder un objet annonce valide en test service.')
            ->setType($type)
            ->setStatut($statut)
            ->setPrix(120)
            ->setCategorie('Materiel')
            ->setImageUrl('https://example.com/image.jpg')
            ->setLocalisation('Sousse')
            ->setProprietaireId($ownerId)
            ->setQuantiteDisponible($stock)
            ->setUnitePrix('jour');
    }
}
