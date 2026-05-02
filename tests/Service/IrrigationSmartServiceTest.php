<?php

namespace App\Tests\Service;

use App\Service\IrrigationSmartService;
use App\Service\WeatherService2;
use PHPUnit\Framework\TestCase;

class IrrigationSmartServiceTest extends TestCase
{
    /**
     * Crée un mock de WeatherService2 avec des données météo simulées.
     */
    private function createWeatherMock(
        array $pluies    = [0,0,0,0,0,0,0],
        array $tempsMax  = [25,26,28,24,27,30,22],
        array $humidites = [50,55,60,45,50,40,65]
    ): WeatherService2 {
        $mock = $this->createMock(WeatherService2::class);
        $mock->method('getForecast')->willReturn([
            'daily' => [
                'precipitation_sum'        => $pluies,
                'temperature_2m_max'       => $tempsMax,
                'relative_humidity_2m_max' => $humidites,
            ]
        ]);
        return $mock;
    }

    // ----------------------------------------------------------------
    // Tests sur genererPlanIA()
    // ----------------------------------------------------------------

    /**
     * Règle 1 : Le plan généré doit contenir exactement 7 jours.
     */
    public function testPlanContientSeptJours(): void
    {
        $service = new IrrigationSmartService($this->createWeatherMock());

        $plan = $service->genererPlanIA(
            besoinHebdomadaire: 35.0,
            lat: 36.8,
            lon: 10.1,
            nomCulture: 'tomate',
            superficie: 1.0
        );

        $this->assertCount(7, $plan);
        $this->assertArrayHasKey('LUN', $plan);
        $this->assertArrayHasKey('DIM', $plan);
    }

    /**
     * Règle 2 : Chaque jour doit contenir les clés eau_mm, duree, temp, humidite, pluie.
     */
    public function testChaqueJourContientLesBonnesCles(): void
    {
        $service = new IrrigationSmartService($this->createWeatherMock());

        $plan = $service->genererPlanIA(35.0, 36.8, 10.1, 'blé', 1.0);

        foreach ($plan as $jour => $valeurs) {
            $this->assertArrayHasKey('eau_mm',   $valeurs, "$jour manque 'eau_mm'");
            $this->assertArrayHasKey('duree',    $valeurs, "$jour manque 'duree'");
            $this->assertArrayHasKey('temp',     $valeurs, "$jour manque 'temp'");
            $this->assertArrayHasKey('humidite', $valeurs, "$jour manque 'humidite'");
            $this->assertArrayHasKey('pluie',    $valeurs, "$jour manque 'pluie'");
        }
    }

    /**
     * Règle 3 : Le volume d'eau ne peut jamais être négatif.
     */
    public function testEauMMNePeutPasEtreNegative(): void
    {
        // Beaucoup de pluie → l'eau à fournir doit être ramenée à 0, jamais négative
        $service = new IrrigationSmartService(
            $this->createWeatherMock(
                pluies: [50, 50, 50, 50, 50, 50, 50]
            )
        );

        $plan = $service->genererPlanIA(35.0, 36.8, 10.1, 'tomate', 1.0);

        foreach ($plan as $jour => $valeurs) {
            $this->assertGreaterThanOrEqual(
                0,
                $valeurs['eau_mm'],
                "L'eau pour $jour est négative : {$valeurs['eau_mm']}"
            );
        }
    }

    /**
     * Règle 4 : La durée d'irrigation doit être un entier positif ou nul.
     */
    public function testDureeEstUnEntierPositif(): void
    {
        $service = new IrrigationSmartService($this->createWeatherMock());

        $plan = $service->genererPlanIA(35.0, 36.8, 10.1, 'mais', 2.0);

        foreach ($plan as $jour => $valeurs) {
            $this->assertIsInt($valeurs['duree'], "$jour : duree n'est pas un entier");
            $this->assertGreaterThanOrEqual(0, $valeurs['duree']);
        }
    }

    /**
     * Règle 5 : Une culture inconnue utilise les paramètres 'default' sans erreur.
     */
    public function testCultureInconnueUtiliseDefault(): void
    {
        $service = new IrrigationSmartService($this->createWeatherMock());

        // Ne doit pas lancer d'exception
        $plan = $service->genererPlanIA(35.0, 36.8, 10.1, 'cultureinconnue', 1.0);

        $this->assertCount(7, $plan);
    }

    /**
     * Règle 6 : Forte chaleur (>35°C) augmente les besoins en eau.
     */
    public function testForteChaleurAugmenteLesBesoins(): void
    {
        $serviceChaleur = new IrrigationSmartService(
            $this->createWeatherMock(tempsMax: [38,38,38,38,38,38,38])
        );
        $serviceNormal = new IrrigationSmartService(
            $this->createWeatherMock(tempsMax: [22,22,22,22,22,22,22])
        );

        $planChaleur = $serviceChaleur->genererPlanIA(35.0, 36.8, 10.1, 'tomate', 1.0);
        $planNormal  = $serviceNormal->genererPlanIA(35.0, 36.8, 10.1, 'tomate', 1.0);

        $totalChaleur = array_sum(array_column($planChaleur, 'eau_mm'));
        $totalNormal  = array_sum(array_column($planNormal,  'eau_mm'));

        $this->assertGreaterThan(
            $totalNormal,
            $totalChaleur,
            'La forte chaleur devrait augmenter les besoins en eau.'
        );
    }

    /**
     * Règle 7 : Grande humidité réduit les besoins en eau.
     */
    public function testGrandeHumiditeReduitLesBesoins(): void
    {
        $serviceHumide = new IrrigationSmartService(
            $this->createWeatherMock(humidites: [90,90,90,90,90,90,90])
        );
        $serviceSec = new IrrigationSmartService(
            $this->createWeatherMock(humidites: [20,20,20,20,20,20,20])
        );

        $planHumide = $serviceHumide->genererPlanIA(35.0, 36.8, 10.1, 'tomate', 1.0);
        $planSec    = $serviceSec->genererPlanIA(35.0, 36.8, 10.1, 'tomate', 1.0);

        $totalHumide = array_sum(array_column($planHumide, 'eau_mm'));
        $totalSec    = array_sum(array_column($planSec,    'eau_mm'));

        $this->assertLessThan(
            $totalSec,
            $totalHumide,
            'Grande humidité devrait réduire les besoins en eau.'
        );
    }

    // ----------------------------------------------------------------
    // Tests sur getInfoCulture()
    // ----------------------------------------------------------------

    /**
     * Règle 8 : getInfoCulture() retourne les 4 clés attendues.
     */
    public function testGetInfoCultureRetourneLesBonnesCles(): void
    {
        $service = new IrrigationSmartService($this->createWeatherMock());

        $info = $service->getInfoCulture('tomate');

        $this->assertArrayHasKey('besoin_quotidien_mm',    $info);
        $this->assertArrayHasKey('coefficient_cultural',   $info);
        $this->assertArrayHasKey('sensibilite_chaleur',    $info);
        $this->assertArrayHasKey('besoin_hebdomadaire_mm', $info);
    }

    /**
     * Règle 9 : Le besoin hebdomadaire = besoin quotidien × 7.
     */
    public function testBesoinHebdomadaireEgaleSeptFoisQuotidien(): void
    {
        $service = new IrrigationSmartService($this->createWeatherMock());

        $info = $service->getInfoCulture('olivier');

        $this->assertEquals(
            round($info['besoin_quotidien_mm'] * 7, 1),
            $info['besoin_hebdomadaire_mm']
        );
    }

    /**
     * Règle 10 : Le coefficient cultural (Kc) doit être supérieur à 0.
     */
    public function testCoefficientCulturalEstPositif(): void
    {
        $service = new IrrigationSmartService($this->createWeatherMock());

        foreach (['tomate', 'blé', 'vigne', 'mais', 'olivier'] as $culture) {
            $info = $service->getInfoCulture($culture);
            $this->assertGreaterThan(
                0,
                $info['coefficient_cultural'],
                "Kc de '$culture' doit être > 0"
            );
        }
    }
}