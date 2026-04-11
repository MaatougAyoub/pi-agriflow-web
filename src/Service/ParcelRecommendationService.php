<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ParcelRecommendationService
{
    private const GEMINI_ENDPOINT = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent';

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly LoggerInterface $logger,
        #[Autowire(env: 'GEMINI_API_KEY')]
        private readonly ?string $apiKey = null,
    ) {
    }

    /**
     * @return array<int, array{nom: string, surface_m2: float|int, justification: string}>
     */
    public function getRecommendations(string $typeTerre, float $superficie, string $localisation): array
    {
        $apiKey = trim((string) $this->apiKey);

        if ('' === $apiKey) {
            throw new \RuntimeException('La cle API Gemini n est pas configuree.');
        }

        if ('' === trim($typeTerre) || $superficie <= 0) {
            throw new \RuntimeException('Les informations de la parcelle sont insuffisantes pour generer des recommandations.');
        }

        $prompt = sprintf(
            "En tant qu'expert agronome, propose un decoupage optimal pour une parcelle :\n".
            "- Type de sol : %s\n".
            "- Superficie : %.2f m2\n".
            "- Localisation : %s\n\n".
            "Instructions :\n".
            "- proposer 2 ou 3 cultures\n".
            "- donner surface_m2 pour chacune\n".
            "- donner justification courte\n".
            "- la somme des surface_m2 doit etre proche de la superficie totale\n".
            "- repondre uniquement en JSON strict sous forme de tableau\n".
            "- format attendu : [{\"nom\":\"...\",\"surface_m2\":123,\"justification\":\"...\"}]",
            $typeTerre,
            $superficie,
            trim($localisation) !== '' ? $localisation : 'Non renseignee'
        );

        $response = $this->httpClient->request('POST', self::GEMINI_ENDPOINT, [
            'headers' => [
                'Content-Type' => 'application/json',
                'x-goog-api-key' => $apiKey,
            ],
            'json' => [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt],
                        ],
                    ],
                ],
                'generationConfig' => [
                    'temperature' => 0.2,
                    'response_mime_type' => 'application/json',
                    'response_schema' => [
                        'type' => 'ARRAY',
                        'items' => [
                            'type' => 'OBJECT',
                            'properties' => [
                                'nom' => ['type' => 'STRING'],
                                'surface_m2' => ['type' => 'NUMBER'],
                                'justification' => ['type' => 'STRING'],
                            ],
                            'required' => ['nom', 'surface_m2', 'justification'],
                        ],
                    ],
                ],
            ],
            'timeout' => 20,
        ]);

        $statusCode = $response->getStatusCode();
        $rawBody = $response->getContent(false);

        $this->logger->info('Parcel recommendations response received.', [
            'status' => $statusCode,
            'body' => $rawBody,
        ]);

        if ($statusCode < 200 || $statusCode >= 300) {
            $this->logger->error('Parcel recommendations Gemini HTTP error.', [
                'status' => $statusCode,
                'body' => $rawBody,
            ]);

            throw new \RuntimeException(sprintf(
                'Gemini a retourne une erreur HTTP %d. Body brut: %s',
                $statusCode,
                $rawBody
            ));
        }

        $payload = json_decode($rawBody, true);

        if (!is_array($payload)) {
            throw new \RuntimeException(sprintf(
                'La reponse HTTP Gemini n est pas un JSON exploitable (%s). Body brut: %s',
                json_last_error_msg(),
                $rawBody
            ));
        }

        $rawText = $this->extractRawText($payload);

        $this->logger->info('Parcel recommendations extracted text.', [
            'text' => $rawText,
        ]);

        if ('' === $rawText) {
            throw new \RuntimeException('La reponse du service IA est vide.');
        }

        $recommendations = $this->decodeRecommendations($rawText);

        if ([] === $recommendations) {
            throw new \RuntimeException('Aucune recommandation exploitable n a ete retournee.');
        }

        return $recommendations;
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function extractRawText(array $payload): string
    {
        $parts = $payload['candidates'][0]['content']['parts'] ?? null;

        if (!is_array($parts)) {
            $this->logger->warning('Parcel recommendations response has no content parts.', [
                'payload' => $payload,
            ]);

            return '';
        }

        $texts = [];

        foreach ($parts as $part) {
            $text = trim((string) ($part['text'] ?? ''));

            if ('' !== $text) {
                $texts[] = $text;
            }
        }

        return trim(implode(' ', $texts));
    }

    /**
     * @return array<int, array{nom: string, surface_m2: float|int, justification: string}>
     */
    private function decodeRecommendations(string $rawText): array
    {
        $decoded = json_decode($rawText, true);

        if (!is_array($decoded)) {
            $jsonError = json_last_error_msg();

            $this->logger->warning('Parcel recommendation JSON parsing failed.', [
                'json_error' => $jsonError,
                'raw_text' => $rawText,
            ]);

            throw new \RuntimeException(sprintf(
                'Echec du parsing JSON Gemini (%s). Texte brut: %s',
                $jsonError,
                $rawText
            ));
        }

        if (isset($decoded['recommendations']) && is_array($decoded['recommendations'])) {
            $decoded = $decoded['recommendations'];
        }

        if (!array_is_list($decoded)) {
            throw new \RuntimeException(sprintf(
                'La reponse Gemini ne contient pas un tableau de recommandations exploitable. Texte brut: %s',
                $rawText
            ));
        }

        $recommendations = [];

        foreach ($decoded as $item) {
            if (!is_array($item)) {
                continue;
            }

            $nom = trim((string) ($item['nom'] ?? ''));
            $surface = $item['surface_m2'] ?? null;
            $justification = trim((string) ($item['justification'] ?? ''));

            if ('' === $nom || !is_numeric($surface) || '' === $justification) {
                continue;
            }

            $recommendations[] = [
                'nom' => $nom,
                'surface_m2' => round((float) $surface, 2),
                'justification' => $justification,
            ];
        }

        return $recommendations;
    }
}
