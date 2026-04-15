<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Service IA utilisant l'API Google Gemini (modèle gemini-1.5-flash).
 * Port de GeminiAIService.java pour la modération et l'amélioration de contenu.
 */
class GeminiAIService
{
    private const BASE_URL = 'https://generativelanguage.googleapis.com/v1beta/models/%s:generateContent';
    private const OFF_TOPIC_PATTERNS = [
        'crypto', 'cryptomonnaie', 'cryptomonnaies', 'bitcoin', 'ethereum',
        'iphone', 'samsung', 'console', 'playstation',
        'forex', 'casino', 'paris sportif',
    ];
    private const SPAM_INAPPROPRIATE_PATTERNS = [
        'arnaque', 'escroquerie', 'fraude', 'spam',
        'gagnez', 'argent facile', 'revenu rapide',
        'violence', 'haine', 'vulgaire', 'porn',
    ];

    public function __construct(
        private HttpClientInterface $httpClient,
        private LoggerInterface $logger,
        private string $geminiApiKey,
        private string $geminiModel
    ) {
        // Sécurité : Si la clé injectée est celle par défaut ou vide, on regarde dans les variables d'environnement réelles
        if (empty($this->geminiApiKey) || $this->geminiApiKey === 'votre_cle_ici') {
            $this->geminiApiKey = $_ENV['GEMINI_API_KEY'] ?? $_SERVER['GEMINI_API_KEY'] ?? '';
        }

        if (empty($this->geminiModel)) {
            $this->geminiModel = $_ENV['GEMINI_MODEL'] ?? $_SERVER['GEMINI_MODEL'] ?? 'gemini-2.5-flash';
        }
    }

    /**
     * Améliore la description d'une demande pour la rendre plus professionnelle.
     */
    public function improveDescription(string $title, string $description): string
    {
        $prompt = sprintf(
            "Tu es un expert en rédaction pour AgriFlow, un marketplace agricole en Tunisie. " .
            "Réécris et améliore cette description pour la rendre plus professionnelle et attractive. " .
            "Garde le même sens. Réponds UNIQUEMENT avec la nouvelle description.\n\n" .
            "Titre : %s\nDescription originale : %s",
            $title,
            $description
        );

        return $this->generateContent($prompt, $description);
    }

    /**
     * Suggère un salaire réaliste pour une demande.
     */
    public function suggestSalary(string $title, string $description, string $location): float
    {
        $prompt = sprintf(
            "En tant qu'expert du marché agricole en Tunisie, suggère un salaire journalier réaliste en DT pour cette offre.\n" .
            "Réponds UNIQUEMENT par le chiffre (ex: 60.5).\n\n" .
            "Titre : %s\nDescription : %s\nLocalisation : %s",
            $title,
            $description,
            $location
        );

        $response = $this->generateContent($prompt);
        return (float) preg_replace('/[^0-9.]/', '', $response);
    }

    /**
     * Analyse si un candidat est un bon fit pour une demande.
     */
    public function analyzeCandidateFit(string $requestDesc, string $motivation): string
    {
        $prompt = sprintf(
            "En tant qu'expert RH agricole, analyse la motivation d'un candidat par rapport à une offre.\n" .
            "Offre : %s\nMotivation du candidat : %s\n\n" .
            "Donne un résumé très court (2 lignes max) expliquant pourquoi ce candidat est pertinent ou non.",
            $requestDesc,
            $motivation
        );

        return $this->generateContent($prompt, "Analyse indisponible pour le moment.");
    }

    /**
     * Modère le contenu d'une demande. 
     * Retourne null si OK, sinon retourne le motif de rejet.
     */
    public function moderateContent(string $title, string $description): ?string
    {
        $localReason = $this->fallbackModerationReason($title, $description);
        if ($localReason !== null) {
            return $localReason;
        }

        $prompt = sprintf(
            "Analyse cette annonce agricole. REJETER si : produit illégal, arnaque, vulgaire, ou sans rapport avec l'agriculture.\n" .
            "Si acceptable, réponds exactement : OK. Sinon, réponds : REJET: [motif court].\n\n" .
            "Titre : %s\nDescription : %s",
            $title,
            $description
        );

        // En modération, on ne considère jamais un fallback implicite "OK".
        $response = trim($this->generateContent($prompt, ''));
        if ($response === '') {
            // Si l'API est indisponible, on garde la décision locale.
            return null;
        }

        if (preg_match('/^\s*OK\b/iu', $response) === 1) {
            return null;
        }

        if (preg_match('/REJET\s*:?\s*(.*)$/iu', $response, $matches) === 1) {
            $reason = trim($matches[1] ?? '');
            return $reason !== '' ? $reason : 'Contenu non conforme à la politique de publication.';
        }

        return 'Contenu potentiellement non conforme détecté par la modération IA.';
    }

    /**
     * Envoie la requête à l'API Gemini.
     */
    private function generateContent(string $prompt, string $default = 'OK'): string
    {
        if (empty($this->geminiApiKey) || $this->geminiApiKey === 'votre_cle_ici') {
            return $default;
        }

        try {
            $response = $this->httpClient->request('POST', sprintf(
                self::BASE_URL,
                rawurlencode($this->geminiModel)
            ) . '?key=' . rawurlencode($this->geminiApiKey), [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'contents' => [[
                        'parts' => [[
                            'text' => $prompt,
                        ]],
                    ]],
                    'generationConfig' => [
                        'temperature' => 0.4,
                    ],
                ],
                'timeout' => 15,
            ]);

            $statusCode = $response->getStatusCode();
            $data = $response->toArray(false);

            if ($statusCode !== 200) {
                $this->logger->warning('Gemini API a retourne un statut inattendu.', [
                    'status_code' => $statusCode,
                    'model' => $this->geminiModel,
                    'response' => $data,
                ]);

                return $default;
            }

            $parts = $data['candidates'][0]['content']['parts'] ?? null;
            if (!is_array($parts)) {
                $this->logger->warning('Gemini API n a retourne aucun contenu exploitable.', [
                    'model' => $this->geminiModel,
                    'response' => $data,
                ]);

                return $default;
            }

            $texts = [];
            foreach ($parts as $part) {
                if (is_array($part) && isset($part['text']) && is_string($part['text'])) {
                    $texts[] = trim($part['text']);
                }
            }

            $text = trim(implode("\n", array_filter($texts)));
            if ($text === '') {
                $this->logger->warning('Gemini API a retourne une reponse vide.', [
                    'model' => $this->geminiModel,
                    'response' => $data,
                ]);

                return $default;
            }

            return $text;
        } catch (\Throwable $e) {
            $this->logger->warning('Appel Gemini indisponible.', [
                'model' => $this->geminiModel,
                'error' => $e->getMessage(),
            ]);

            return $default;
        }
    }

    private function fallbackModerationReason(string $title, string $description): ?string
    {
        $text = mb_strtolower(trim($title.' '.$description));

        foreach (self::OFF_TOPIC_PATTERNS as $pattern) {
            if (str_contains($text, $pattern)) {
                return sprintf('Hors sujet pour AgriFlow : contenu détecté "%s".', $pattern);
            }
        }

        foreach (self::SPAM_INAPPROPRIATE_PATTERNS as $pattern) {
            if (str_contains($text, $pattern)) {
                return sprintf('Contenu suspect ou inapproprié détecté : "%s".', $pattern);
            }
        }

        return null;
    }
}
