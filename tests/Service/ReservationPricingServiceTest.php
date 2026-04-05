<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Entity\Annonce;
use App\Entity\Reservation;
use App\Enum\AnnonceType;
use App\Service\ReservationPricingService;
use PHPUnit\Framework\TestCase;

final class ReservationPricingServiceTest extends TestCase
{
    public function testHydrateReservationForLocationAddsFivePercentCommission(): void
    {
        $annonce = (new Annonce())
            ->setTitre('Tracteur')
            ->setDescription('Description assez longue pour respecter la validation de base du module.')
            ->setType(AnnonceType::LOCATION)
            ->setPrix(200)
            ->setCategorie('Materiel')
            ->setImageUrl('https://example.com/tracteur.jpg')
            ->setLocalisation('Tunis')
            ->setProprietaireId(9)
            ->setQuantiteDisponible(2)
            ->setUnitePrix('jour');

        $reservation = (new Reservation())
            ->setAnnonce($annonce)
            ->setClientId(14)
            ->setDateDebut(new \DateTimeImmutable('2026-04-10'))
            ->setDateFin(new \DateTimeImmutable('2026-04-12'))
            ->setQuantite(1);

        (new ReservationPricingService())->hydrateReservation($reservation);

        self::assertSame('30.00', $reservation->getCommission());
        self::assertSame('630.00', $reservation->getPrixTotal());
        self::assertSame(9, $reservation->getProprietaireId());
    }

    public function testHydrateReservationForVenteUsesQuantity(): void
    {
        $annonce = (new Annonce())
            ->setTitre('Tomates')
            ->setDescription('Description assez longue pour montrer une annonce de vente de produits frais.')
            ->setType(AnnonceType::VENTE)
            ->setPrix(10)
            ->setCategorie('Produits frais')
            ->setImageUrl('https://example.com/tomates.jpg')
            ->setLocalisation('Sousse')
            ->setProprietaireId(5)
            ->setQuantiteDisponible(40)
            ->setUnitePrix('piece');

        $reservation = (new Reservation())
            ->setAnnonce($annonce)
            ->setClientId(3)
            ->setDateDebut(new \DateTimeImmutable('2026-04-10'))
            ->setDateFin(new \DateTimeImmutable('2026-04-10'))
            ->setQuantite(20);

        (new ReservationPricingService())->hydrateReservation($reservation);

        self::assertSame('10.00', $reservation->getCommission());
        self::assertSame('210.00', $reservation->getPrixTotal());
    }

    public function testHydrateReservationThrowsWhenQuantityExceedsStock(): void
    {
        $annonce = (new Annonce())
            ->setTitre('Pompe')
            ->setDescription('Description assez longue pour verifier le cas de stock insuffisant.')
            ->setType(AnnonceType::VENTE)
            ->setPrix(80)
            ->setCategorie('Irrigation')
            ->setImageUrl('https://example.com/pompe.jpg')
            ->setLocalisation('Nabeul')
            ->setProprietaireId(6)
            ->setQuantiteDisponible(2)
            ->setUnitePrix('piece');

        $reservation = (new Reservation())
            ->setAnnonce($annonce)
            ->setClientId(7)
            ->setDateDebut(new \DateTimeImmutable('2026-04-10'))
            ->setDateFin(new \DateTimeImmutable('2026-04-10'))
            ->setQuantite(5);

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('La quantite demandee depasse le stock disponible.');

        (new ReservationPricingService())->hydrateReservation($reservation);
    }
}
