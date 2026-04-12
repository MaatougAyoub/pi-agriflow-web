<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\CollabApplication;
use App\Entity\CollabRequest;

/**
 * Calculates a compatibility match score between a candidate application
 * and a collaboration request — mirrors CandidateMatchingService.java.
 */
class CandidateMatchingService
{
    /** Minimum score (0–100) to be considered a good match. */
    public const GOOD_MATCH_THRESHOLD = 70.0;

    /** Weights for each criterion (must sum to 1.0). */
    private const WEIGHT_EXPERIENCE   = 0.35;
    private const WEIGHT_SALARY       = 0.40;
    private const WEIGHT_AVAILABILITY = 0.25;

    private const TUNISIAN_CITIES = [
        'tunis', 'sfax', 'sousse', 'nabeul', 'bizerte', 'kairouan', 'monastir',
        'mahdia', 'medenine', 'beja', 'jendouba', 'siliana', 'kef', 'kasserine',
        'sidi bouzid', 'gafsa', 'tozeur', 'kebili', 'gabes', 'tataouine', 'zaghouan',
        'ariana', 'ben arous', 'manouba',
    ];

    /**
     * Returns a match score (0–100) for a single application.
     *
     * @return array{total: float, experience: float, salary: float, availability: float}
     */
    public function calculateScore(CollabApplication $application, CollabRequest $request): array
    {
        $experience   = $this->experienceScore($application->getYearsOfExperience() ?? 0);
        $salary       = $this->salaryScore(
            $application->getExpectedSalaryAsFloat(),
            $request->getSalaryPerDayAsFloat(),
        );
        $availability = $this->availabilityScore($request);

        $total = round(
            $experience   * self::WEIGHT_EXPERIENCE
            + $salary     * self::WEIGHT_SALARY
            + $availability * self::WEIGHT_AVAILABILITY,
            2,
        );

        return [
            'total'        => $total,
            'experience'   => $experience,
            'salary'       => $salary,
            'availability' => $availability,
        ];
    }

    /**
     * Ranks applications by descending total match score.
     *
     * @param  CollabApplication[] $applications
     * @return array<array{application: CollabApplication, score: array}>
     */
    public function rankApplications(array $applications, CollabRequest $request): array
    {
        $ranked = array_map(fn (CollabApplication $app) => [
            'application' => $app,
            'score'       => $this->calculateScore($app, $request),
        ], $applications);

        usort($ranked, fn ($a, $b) => $b['score']['total'] <=> $a['score']['total']);

        return $ranked;
    }

    // ── Private helpers ─────────────────────────────────────────────────────

    private function experienceScore(int $years): float
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

    private function salaryScore(float $expected, float $offered): float
    {
        if ($offered <= 0) {
            return 50.0; // neutral when no salary data
        }

        if ($expected <= $offered) {
            return 100.0;
        }

        $diff = (($expected - $offered) / $offered) * 100;

        return match (true) {
            $diff <= 5  => 95.0,
            $diff <= 10 => 85.0,
            $diff <= 20 => 65.0,
            $diff <= 30 => 45.0,
            $diff <= 50 => 25.0,
            default     => 10.0,
        };
    }

    private function availabilityScore(CollabRequest $request): float
    {
        // If the request is still open and not expired the candidate is considered available.
        if ($request->isOpen() && !$request->isExpired()) {
            return 100.0;
        }

        return 50.0;
    }

    /**
     * Whether a location string mentions a known Tunisian city (used for display only).
     */
    public function isKnownTunisianCity(string $location): bool
    {
        $lower = mb_strtolower(trim($location));
        foreach (self::TUNISIAN_CITIES as $city) {
            if (str_contains($lower, $city)) {
                return true;
            }
        }

        return false;
    }
}
