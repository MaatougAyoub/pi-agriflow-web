<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Annonce;
use App\Entity\Reservation;
use App\Enum\AnnonceStatut;
use App\Enum\AnnonceType;
use PHPUnit\Framework\TestCase;

final class AnnonceEntityTest extends TestCase
{
    public function testAnnonceNormalizesValuesAndTypeFlags(): void
    {
        $annonce = (new Annonce())
            ->setTitre('  Tracteur agricole  ')
            ->setDescription('  Location tracteur pour travaux saisonniers  ')
            ->setType(AnnonceType::VENTE)
            ->setStatut(AnnonceStatut::DISPONIBLE)
            ->setPrix(1250)
            ->setCategorie('  Materiel  ')
            ->setImageUrl('  https://example.com/tracteur.jpg  ')
            ->setLocalisation('  Sfax  ')
            ->setProprietaireId(15)
            ->setQuantiteDisponible(4)
            ->setUnitePrix('  unite  ');

        self::assertSame('Tracteur agricole', $annonce->getTitre());
        self::assertSame('Location tracteur pour travaux saisonniers', $annonce->getDescription());
        self::assertSame(AnnonceType::VENTE, $annonce->getType());
        self::assertSame(AnnonceStatut::DISPONIBLE, $annonce->getStatut());
        self::assertSame('1250.00', $annonce->getPrix());
        self::assertSame(1250.0, $annonce->getPrixAsFloat());
        self::assertSame('Materiel', $annonce->getCategorie());
        self::assertSame('https://example.com/tracteur.jpg', $annonce->getImageUrl());
        self::assertSame('Sfax', $annonce->getLocalisation());
        self::assertSame(15, $annonce->getProprietaireId());
        self::assertSame(4, $annonce->getQuantiteDisponible());
        self::assertSame('unite', $annonce->getUnitePrix());
        self::assertTrue($annonce->isVente());
        self::assertFalse($annonce->isLocation());
    }

    public function testGeocodingCanBeCleared(): void
    {
        $annonce = (new Annonce())
            ->setLatitude(34.7406)
            ->setLongitude(10.7603)
            ->setLocalisationNormalisee('  Sfax, Tunisie  ');

        self::assertSame(34.7406, $annonce->getLatitude());
        self::assertSame(10.7603, $annonce->getLongitude());
        self::assertSame('Sfax, Tunisie', $annonce->getLocalisationNormalisee());

        $annonce->clearGeocoding();

        self::assertNull($annonce->getLatitude());
        self::assertNull($annonce->getLongitude());
        self::assertNull($annonce->getLocalisationNormalisee());
    }

    public function testReservationRelationIsSynchronized(): void
    {
        $annonce = new Annonce();
        $reservation = new Reservation();

        $annonce->addReservation($reservation);

        self::assertCount(1, $annonce->getReservations());
        self::assertSame($annonce, $reservation->getAnnonce());

        $annonce->removeReservation($reservation);

        self::assertCount(0, $annonce->getReservations());
        self::assertNull($reservation->getAnnonce());
    }
}
