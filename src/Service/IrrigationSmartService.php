<?php

namespace App\Service;

class IrrigationSmartService
{
    private const DEBIT_L_MIN = 12.0;

    // Besoins en eau quotidiens (mm/jour) par type de culture
    private const BESOINS_CULTURE = [
        'fraise'     => ['besoin_mm' => 5.0,  'kc' => 1.0,  'sensibilite_chaleur' => 0.3],
        'tomate'     => ['besoin_mm' => 6.5,  'kc' => 1.15, 'sensibilite_chaleur' => 0.25],
        'pomme_de_terre' => ['besoin_mm' => 5.5, 'kc' => 1.1, 'sensibilite_chaleur' => 0.2],
        'ble'        => ['besoin_mm' => 4.5,  'kc' => 1.05, 'sensibilite_chaleur' => 0.15],
        'mais'       => ['besoin_mm' => 7.0,  'kc' => 1.2,  'sensibilite_chaleur' => 0.2],
        'olivier'    => ['besoin_mm' => 3.5,  'kc' => 0.7,  'sensibilite_chaleur' => 0.1],
        'vigne'      => ['besoin_mm' => 4.0,  'kc' => 0.8,  'sensibilite_chaleur' => 0.15],
        'laitue'     => ['besoin_mm' => 4.0,  'kc' => 0.95, 'sensibilite_chaleur' => 0.35],
        'concombre'  => ['besoin_mm' => 6.0,  'kc' => 1.0,  'sensibilite_chaleur' => 0.3],
        'poivron'    => ['besoin_mm' => 5.5,  'kc' => 1.05, 'sensibilite_chaleur' => 0.25],
        'oignon'     => ['besoin_mm' => 4.5,  'kc' => 0.95, 'sensibilite_chaleur' => 0.15],
        'carotte'    => ['besoin_mm' => 4.0,  'kc' => 0.9,  'sensibilite_chaleur' => 0.2],
        'haricot'    => ['besoin_mm' => 5.0,  'kc' => 1.0,  'sensibilite_chaleur' => 0.2],
        'piment'     => ['besoin_mm' => 5.5,  'kc' => 1.05, 'sensibilite_chaleur' => 0.2],
        'melon'      => ['besoin_mm' => 6.0,  'kc' => 1.05, 'sensibilite_chaleur' => 0.15],
        'pasteque'   => ['besoin_mm' => 6.5,  'kc' => 1.1,  'sensibilite_chaleur' => 0.15],
        'default'    => ['besoin_mm' => 5.0,  'kc' => 1.0,  'sensibilite_chaleur' => 0.2],
    ];

    public function __construct(private WeatherService $weatherService) {}

    /**
     * Récupère les paramètres de besoin en eau pour une culture donnée
     */
    private function getParamsCulture(string $nomCulture): array
    {
        $key = mb_strtolower(trim($nomCulture));
        // Normaliser : remplacer espaces par underscore
        $key = str_replace(' ', '_', $key);
        // Enlever les accents
        $key = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $key);

        return self::BESOINS_CULTURE[$key] ?? self::BESOINS_CULTURE['default'];
    }

    /**
     * Génère un plan d'irrigation optimisé basé sur :
     * - Les données météo (température, humidité, pluie)
     * - Le type de culture et ses besoins spécifiques (Kc)
     * - La superficie de la parcelle
     * - Le volume d'eau proposé par l'agriculteur
     */
    public function genererPlanIA(
        float $besoinHebdomadaire,
        float $lat,
        float $lon,
        string $nomCulture = 'default',
        float $superficie = 1.0
    ): array {
        $joursKeys = ['LUN', 'MAR', 'MER', 'JEU', 'VEN', 'SAM', 'DIM'];
        $plan = [];

        // Récupérer les données météo
        $data   = $this->weatherService->getForecast($lat, $lon);
        $daily  = $data['daily'];

        $pluies    = $daily['precipitation_sum'];
        $tempsMax  = $daily['temperature_2m_max'];
        $humidites = $daily['relative_humidity_2m_max'];

        // Paramètres spécifiques à la culture
        $params = $this->getParamsCulture($nomCulture);
        $besoinBase = $params['besoin_mm'];      // Besoin de base en mm/jour
        $kc = $params['kc'];                      // Coefficient cultural
        $sensibiliteChaleur = $params['sensibilite_chaleur'];

        // ETo de référence (Evapotranspiration simplifiée)
        // Formule simplifiée de Hargreaves
        $etoMoyenne = 5.0; // mm/jour par défaut pour zone méditerranéenne

        foreach ($joursKeys as $i => $key) {
            $pluie   = (float)($pluies[$i]    ?? 0);
            $tMax    = (float)($tempsMax[$i]   ?? 25);
            $humMax  = (float)($humidites[$i]  ?? 50);

            // Calcul de l'ETo simplifié basé sur la température
            $eto = 0.0023 * ($tMax + 17.8) * sqrt(max(0, $tMax - 10)) * 0.408;
            if ($eto <= 0) {
                $eto = $etoMoyenne;
            }

            // ETc = ETo × Kc (Evapotranspiration de la culture)
            $etc = $eto * $kc;

            // Ajustement selon la température
            $ratioTemp = 1.0;
            if ($tMax > 35) {
                $ratioTemp += $sensibiliteChaleur * 1.5;
            } elseif ($tMax > 30) {
                $ratioTemp += $sensibiliteChaleur;
            } elseif ($tMax < 15) {
                $ratioTemp -= 0.15;
            }

            // Ajustement selon l'humidité
            $ratioHum = 1.0;
            if ($humMax > 85) {
                $ratioHum -= 0.15; // Moins d'irrigation si très humide
            } elseif ($humMax > 70) {
                $ratioHum -= 0.05;
            } elseif ($humMax < 30) {
                $ratioHum += 0.1; // Plus d'irrigation si très sec
            }

            // Besoin ajusté = ETc × ratios - pluie
            $besoinAjuste = $etc * $ratioTemp * $ratioHum * $superficie;
            $eauAFournir  = max(0, round($besoinAjuste - $pluie, 2));

            // Durée d'irrigation en minutes
            $dureeMin = (int)ceil(($eauAFournir / self::DEBIT_L_MIN) * 60);

            $plan[$key] = [
                'eau_mm'   => $eauAFournir,
                'duree'    => $dureeMin,
                'temp'     => round($tMax, 1),
                'humidite' => round($humMax, 1),
                'pluie'    => round($pluie, 1),
            ];
        }

        return $plan;
    }

    /**
     * Retourne les informations de besoin pour une culture (pour affichage)
     */
    public function getInfoCulture(string $nomCulture): array
    {
        $params = $this->getParamsCulture($nomCulture);
        return [
            'besoin_quotidien_mm' => $params['besoin_mm'],
            'coefficient_cultural' => $params['kc'],
            'sensibilite_chaleur' => $params['sensibilite_chaleur'],
            'besoin_hebdomadaire_mm' => round($params['besoin_mm'] * 7, 1),
        ];
    }
}
