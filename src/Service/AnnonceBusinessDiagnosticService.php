<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Annonce;
use App\Enum\AnnonceStatut;

final class AnnonceBusinessDiagnosticService
{
    /**
     * @param array<string, mixed>|null $environmentInsights
     *
     * @return array{
     *     score: int,
     *     level: string,
     *     summary: string,
     *     commissionRateLabel: string,
     *     badges: list<array{label: string, state: string, points: int}>,
     *     advice: list<string>,
     *     weatherAvailable: bool,
     *     airQualityAvailable: bool
     * }
     */
    public function buildForAnnonce(Annonce $annonce, ?array $environmentInsights = null): array
    {
        $score = 0;
        $badges = [];
        $advice = [];

        // diagnostic: houni n7ot score metier wa7ed yjma3 stock statut prix image w APIs
        $this->addCheck(
            $badges,
            $advice,
            $score,
            'Reservation possible',
            $annonce->getStatut() === AnnonceStatut::DISPONIBLE,
            20,
            'Rendre l annonce disponible si elle doit encore accepter des demandes.'
        );

        // stock: stock lazmou ykoun positif bech acceptation reservation tab9a logique
        $this->addCheck(
            $badges,
            $advice,
            $score,
            'Stock OK',
            $annonce->getQuantiteDisponible() > 0,
            15,
            'Augmenter la quantite disponible avant de publier l offre.'
        );

        $this->addCheck(
            $badges,
            $advice,
            $score,
            'Prix et unite OK',
            $annonce->getPrixAsFloat() > 0.0 && '' !== trim($annonce->getUnitePrix()),
            15,
            'Verifier le prix et l unite pour rendre le calcul reservation clair.'
        );

        $this->addCheck(
            $badges,
            $advice,
            $score,
            $annonce->isLocation() ? 'Logique location' : 'Logique vente',
            true,
            10,
            null
        );

        $hasCoordinates = null !== $annonce->getLatitude() && null !== $annonce->getLongitude();
        $this->addCheck(
            $badges,
            $advice,
            $score,
            'Localisation verifiee',
            $hasCoordinates,
            15,
            'Modifier l annonce avec une localisation claire pour activer carte, meteo et qualite de l air.'
        );

        $weatherAvailable = (bool) ($environmentInsights['weather']['available'] ?? false);
        // api: meteo tjibha Open-Meteo Forecast ken 3andna coordonnees
        $this->addCheck(
            $badges,
            $advice,
            $score,
            'Meteo disponible',
            $weatherAvailable,
            10,
            'La meteo s affiche apres geocodage valide de la localisation.'
        );

        $airQualityAvailable = (bool) ($environmentInsights['airQuality']['available'] ?? false);
        // api: qualite air tjibha Open-Meteo Air Quality bech nwarri API externe thenya
        $this->addCheck(
            $badges,
            $advice,
            $score,
            'Qualite air disponible',
            $airQualityAvailable,
            10,
            'La qualite de l air s affiche apres geocodage valide de la localisation.'
        );

        $imageUrl = trim((string) ($annonce->getImageUrl() ?? ''));
        $hasRealImage = '' !== $imageUrl
            && false !== filter_var($imageUrl, FILTER_VALIDATE_URL)
            && !str_contains(strtolower($imageUrl), 'example.com');
        $this->addCheck(
            $badges,
            $advice,
            $score,
            'Image vendeur OK',
            $hasRealImage,
            5,
            'Ajouter une vraie image URL pour rendre la fiche plus professionnelle.'
        );

        $score = min(100, $score);

        return [
            'score' => $score,
            'level' => $this->levelForScore($score),
            'summary' => $this->summaryForScore($score),
            // commission: meme logique affichable fil fiche w fil devis PDF
            'commissionRateLabel' => sprintf('%d%%', (int) round(ReservationPricingService::COMMISSION_RATE * 100)),
            'badges' => $badges,
            'advice' => $advice,
            'weatherAvailable' => $weatherAvailable,
            'airQualityAvailable' => $airQualityAvailable,
        ];
    }

    /**
     * @param list<array{label: string, state: string, points: int}> $badges
     * @param list<string>                                          $advice
     */
    private function addCheck(
        array &$badges,
        array &$advice,
        int &$score,
        string $label,
        bool $isOk,
        int $points,
        ?string $adviceMessage
    ): void {
        if ($isOk) {
            $score += $points;
        } elseif (null !== $adviceMessage) {
            $advice[] = $adviceMessage;
        }

        $badges[] = [
            'label' => $label,
            'state' => $isOk ? 'success' : 'warning',
            'points' => $isOk ? $points : 0,
        ];
    }

    private function levelForScore(int $score): string
    {
        return match (true) {
            $score >= 85 => 'Tres solide',
            $score >= 70 => 'Solide',
            $score >= 50 => 'A ameliorer',
            default => 'Incomplet',
        };
    }

    private function summaryForScore(int $score): string
    {
        return match (true) {
            $score >= 85 => 'Annonce prete pour une presentation propre et une reservation claire.',
            $score >= 70 => 'Annonce exploitable avec quelques points a surveiller.',
            $score >= 50 => 'Annonce utilisable mais certaines donnees metier peuvent etre renforcees.',
            default => 'Annonce a completer avant de la presenter comme offre fiable.',
        };
    }
}
