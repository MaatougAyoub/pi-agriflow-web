<?php

namespace App\Model;

use App\Entity\CollabApplication;

/**
 * DTO représentant le score de compatibilité candidat ↔ demande.
 * Port de MatchScore.java du projet JavaFX.
 */
class MatchScore
{
    private float $totalScore = 0;
    private float $experienceScore = 0;
    private float $salaryScore = 0;
    private float $locationScore = 0;
    private float $availabilityScore = 0;
    private string $recommendation = '';
    private int $stars = 0;

    public function __construct(
        private CollabApplication $application,
    ) {}

    /**
     * Calcule le score total et détermine la recommandation.
     */
    public function calculateTotalScore(): void
    {
        $this->totalScore = ($this->experienceScore + $this->salaryScore + $this->locationScore + $this->availabilityScore) / 4.0;

        if ($this->totalScore >= 80) {
            $this->recommendation = 'Excellent candidat';
            $this->stars = 5;
        } elseif ($this->totalScore >= 60) {
            $this->recommendation = 'Bon candidat';
            $this->stars = 4;
        } elseif ($this->totalScore >= 40) {
            $this->recommendation = 'Candidat moyen';
            $this->stars = 3;
        } elseif ($this->totalScore >= 20) {
            $this->recommendation = 'Candidat faible';
            $this->stars = 2;
        } else {
            $this->recommendation = 'Non recommandé';
            $this->stars = 1;
        }
    }

    public function getStarsDisplay(): string
    {
        return str_repeat('⭐', max(0, $this->stars));
    }

    public function getScoreEmoji(): string
    {
        if ($this->totalScore >= 80) return '🌟';
        if ($this->totalScore >= 60) return '✅';
        if ($this->totalScore >= 40) return '⚠️';
        return '❌';
    }

    public function getScoreColor(): string
    {
        if ($this->totalScore >= 80) return '#28a745';
        if ($this->totalScore >= 60) return '#17a2b8';
        if ($this->totalScore >= 40) return '#ffc107';
        return '#dc3545';
    }

    // Getters & Setters
    public function getApplication(): CollabApplication { return $this->application; }
    public function getTotalScore(): float { return $this->totalScore; }
    public function getExperienceScore(): float { return $this->experienceScore; }
    public function getSalaryScore(): float { return $this->salaryScore; }
    public function getLocationScore(): float { return $this->locationScore; }
    public function getAvailabilityScore(): float { return $this->availabilityScore; }
    public function getRecommendation(): string { return $this->recommendation; }
    public function getStars(): int { return $this->stars; }

    public function setExperienceScore(float $s): self { $this->experienceScore = $s; return $this; }
    public function setSalaryScore(float $s): self { $this->salaryScore = $s; return $this; }
    public function setLocationScore(float $s): self { $this->locationScore = $s; return $this; }
    public function setAvailabilityScore(float $s): self { $this->availabilityScore = $s; return $this; }
}
