<?php

namespace App\Service;

use App\Entity\CollabApplication;
use App\Entity\CollabRequest;
use App\Model\MatchScore;

/**
 * 🤖 Service d'IA pour le Matching automatique Candidat ↔ Demande.
 * Port exact de CandidateMatchingService.java du projet JavaFX.
 *
 * Analyse la compatibilité entre un candidat et une demande via 4 critères :
 * - Expérience (0-100)
 * - Salaire (0-100)
 * - Localisation (0-100)
 * - Disponibilité (0-100)
 */
class CandidateMatchingService
{
    /** Villes tunisiennes principales (pour le score de localisation) */
    private const TUNISIAN_CITIES = [
        'tunis', 'ariana', 'ben arous', 'manouba',
        'nabeul', 'zaghouan', 'bizerte', 'béja',
        'jendouba', 'kef', 'siliana', 'sousse',
        'monastir', 'mahdia', 'sfax', 'kairouan',
        'kasserine', 'sidi bouzid', 'gabès', 'médenine',
        'tataouine', 'gafsa', 'tozeur', 'kébili',
    ];

    /**
     * Calcule le score de compatibilité pour une candidature.
     */
    public function calculateMatchScore(CollabApplication $application, CollabRequest $request): MatchScore
    {
        $score = new MatchScore($application);

        // 1️⃣ Score d'expérience (0-100)
        $score->setExperienceScore($this->calculateExperienceScore((int) $application->getYearsOfExperience()));

        // 2️⃣ Score de salaire (0-100)
        $score->setSalaryScore($this->calculateSalaryScore(
            (float) $application->getExpectedSalary(),
            (float) $request->getSalary()
        ));

        // 3️⃣ Score de localisation (0-100)
        $score->setLocationScore($this->calculateLocationScore($request->getLocation()));

        // 4️⃣ Score de disponibilité (0-100)
        $score->setAvailabilityScore(100.0); // A postulé = disponible

        // Calculer le score total
        $score->calculateTotalScore();

        return $score;
    }

    /**
     * Classe toutes les candidatures par score décroissant.
     *
     * @param CollabApplication[] $applications
     * @return MatchScore[]
     */
    public function rankApplications(array $applications, CollabRequest $request): array
    {
        $scores = [];
        foreach ($applications as $app) {
            $scores[] = $this->calculateMatchScore($app, $request);
        }

        // Trier par score décroissant (meilleur candidat en premier)
        usort($scores, fn(MatchScore $a, MatchScore $b) => $b->getTotalScore() <=> $a->getTotalScore());

        return $scores;
    }

    /**
     * Score basé sur l'expérience.
     */
    private function calculateExperienceScore(int $years): float
    {
        return match (true) {
            $years >= 10 => 100.0,
            $years >= 7  => 95.0,
            $years >= 5  => 90.0,
            $years >= 3  => 75.0,
            $years >= 1  => 55.0,
            default      => 30.0,
        };
    }

    /**
     * Score basé sur le salaire.
     */
    private function calculateSalaryScore(float $expectedSalary, float $offeredSalary): float
    {
        if ($expectedSalary <= $offeredSalary) {
            return 100.0;
        }

        if ($offeredSalary <= 0) {
            return 50.0;
        }

        $difference = (($expectedSalary - $offeredSalary) / $offeredSalary) * 100;

        return match (true) {
            $difference <= 5  => 95.0,
            $difference <= 10 => 85.0,
            $difference <= 20 => 65.0,
            $difference <= 30 => 45.0,
            $difference <= 50 => 25.0,
            default           => 10.0,
        };
    }

    /**
     * Score basé sur la localisation.
     */
    private function calculateLocationScore(?string $location): float
    {
        if ($location === null || $location === '') {
            return 50.0;
        }

        $lowerLocation = mb_strtolower(trim($location));

        foreach (self::TUNISIAN_CITIES as $city) {
            if (str_contains($lowerLocation, $city)) {
                return 85.0;
            }
        }

        return 70.0;
    }
}
