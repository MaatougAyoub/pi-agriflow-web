<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Entity\Culture;
use App\Entity\Parcelle;
use App\Entity\Utilisateur;
use App\Service\ParcelleCultureRulesService;
use PHPUnit\Framework\TestCase;

final class ParcelleCultureRulesServiceTest extends TestCase
{
    public function testCanDeleteParcelleWhenAllCulturesAreRecoltees(): void
    {
        $parcelle = (new Parcelle())->setId(10);
        $cultures = [
            $this->createCulture(10, 4, Culture::ETAT_RECOLTEE),
            $this->createCulture(10, 4, Culture::ETAT_RECOLTEE),
        ];

        $result = (new ParcelleCultureRulesService())->canDeleteParcelle($parcelle, $cultures);

        self::assertTrue($result);
    }

    public function testCannotDeleteParcelleWhenOneCultureIsVendue(): void
    {
        $parcelle = (new Parcelle())->setId(10);
        $cultures = [
            $this->createCulture(10, 4, Culture::ETAT_RECOLTEE),
            $this->createCulture(10, 4, Culture::ETAT_VENDUE),
        ];

        $result = (new ParcelleCultureRulesService())->canDeleteParcelle($parcelle, $cultures);

        self::assertFalse($result);
    }

    public function testCanBuyCultureWhenItIsEnVenteWithoutAcheteur(): void
    {
        $culture = $this->createCulture(12, 8, Culture::ETAT_EN_VENTE);
        $acheteur = (new Utilisateur())->setId(22);

        $result = (new ParcelleCultureRulesService())->canBuyCulture($culture, $acheteur);

        self::assertTrue($result);
    }

    public function testOwnerCannotBuyOwnCulture(): void
    {
        $culture = $this->createCulture(12, 8, Culture::ETAT_EN_VENTE);
        $proprietaire = (new Utilisateur())->setId(8);

        $result = (new ParcelleCultureRulesService())->canBuyCulture($culture, $proprietaire);

        self::assertFalse($result);
    }

    public function testCanPublishCultureWhenItIsEnCoursWithoutAcheteur(): void
    {
        $culture = $this->createCulture(18, 5, Culture::ETAT_EN_COURS);

        $result = (new ParcelleCultureRulesService())->canPublishCulture($culture);

        self::assertTrue($result);
    }

    public function testCannotPublishCultureWhenItIsVendue(): void
    {
        $culture = $this->createCulture(18, 5, Culture::ETAT_VENDUE);

        $result = (new ParcelleCultureRulesService())->canPublishCulture($culture);

        self::assertFalse($result);
    }

    private function createCulture(int $parcelleId, int $proprietaireId, string $etat): Culture
    {
        return (new Culture())
            ->setParcelleId($parcelleId)
            ->setProprietaireId($proprietaireId)
            ->setEtat($etat)
            ->setNom('Culture test');
    }
}
