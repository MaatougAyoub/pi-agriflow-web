<?php

declare(strict_types=1);

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class AnnonceAiAssistantService
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly LoggerInterface $logger,
        #[Autowire('%env(string:AI_PROVIDER)%')]
        private readonly string $provider,
        #[Autowire('%env(string:GEMINI_API_KEY)%')]
        private readonly string $geminiApiKey,
        #[Autowire('%env(string:GEMINI_MODEL)%')]
        private readonly string $geminiModel,
        #[Autowire('%env(string:OPENAI_API_KEY)%')]
        private readonly string $openAiApiKey,
        #[Autowire('%env(string:OPENAI_MODEL)%')]
        private readonly string $openAiModel,
        #[Autowire('%env(string:GROQ_API_KEY)%')]
        private readonly string $groqApiKey,
        #[Autowire('%env(string:GROQ_MODEL)%')]
        private readonly string $groqModel,
    ) {
    }

    /**
     * @param array{titre?: mixed, description?: mixed, categorie?: mixed, unitePrix?: mixed, type?: mixed, localisation?: mixed} $context
     *
     * @return array{titre: string, description: string, categorie: string, unitePrix: string, qualityScore: int, qualityAdvice: string, provider: string}
     */
    public function generateSuggestions(array $context): array
    {
        $provider = $this->resolveProvider();
        $prompt = $this->buildPrompt($context);

        // ia: provider yetbadel mel .env.local, ama formulaire yeb9a nafsou
        $rawText = match ($provider) {
            'openai' => $this->requestOpenAi($prompt),
            'groq' => $this->requestGroq($prompt),
            default => $this->requestGemini($prompt),
        };

        $decoded = $this->decodeJsonPayload($rawText);

        return [
            'titre' => $this->sanitizeSuggestion($decoded['titre'] ?? $context['titre'] ?? '', 150),
            'description' => $this->sanitizeSuggestion($decoded['description'] ?? $context['description'] ?? '', 2000),
            'categorie' => $this->sanitizeSuggestion($decoded['categorie'] ?? $context['categorie'] ?? '', 120),
            'unitePrix' => $this->sanitizeSuggestion($decoded['unitePrix'] ?? $context['unitePrix'] ?? '', 20),
            // ia: score hedha y3awen vendeur ychouf qualite annonce 9bal validation
            'qualityScore' => $this->sanitizeScore($decoded['qualityScore'] ?? 0),
            'qualityAdvice' => $this->sanitizeSuggestion($decoded['qualityAdvice'] ?? '', 220),
            'provider' => $provider,
        ];
    }

    private function resolveProvider(): string
    {
        $provider = strtolower(trim($this->provider));

        return in_array($provider, ['gemini', 'openai', 'groq'], true) ? $provider : 'gemini';
    }

    private function requestGemini(string $prompt): string
    {
        if ('' === trim($this->geminiApiKey)) {
            throw new \DomainException('Assistant indisponible : la clé Gemini est absente.');
        }

        try {
            $response = $this->httpClient->request('POST', sprintf(
                'https://generativelanguage.googleapis.com/v1beta/models/%s:generateContent?key=%s',
                rawurlencode($this->geminiModel),
                rawurlencode($this->geminiApiKey)
            ), [
                'headers' => ['Content-Type' => 'application/json'],
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
                'timeout' => 30,
            ]);

            $payload = $response->toArray(false);
            $parts = $payload['candidates'][0]['content']['parts'] ?? null;

            if (!is_array($parts)) {
                throw new \RuntimeException('Réponse Gemini vide.');
            }

            $texts = [];

            foreach ($parts as $part) {
                if (is_array($part) && isset($part['text']) && is_string($part['text'])) {
                    $texts[] = $part['text'];
                }
            }

            $text = trim(implode("\n", $texts));

            if ('' === $text) {
                throw new \RuntimeException('Gemini n\'a retourné aucun texte.');
            }

            return $text;
        } catch (\DomainException $exception) {
            throw $exception;
        } catch (\Throwable $exception) {
            $this->logger->warning('Assistant IA Gemini indisponible.', ['error' => $exception->getMessage()]);
            throw new \DomainException('Assistant indisponible pour le moment.');
        }
    }

    private function requestOpenAi(string $prompt): string
    {
        if ('' === trim($this->openAiApiKey)) {
            throw new \DomainException('Assistant indisponible : la clé OpenAI est absente.');
        }

        try {
            $response = $this->httpClient->request('POST', 'https://api.openai.com/v1/responses', [
                'headers' => [
                    'Authorization' => 'Bearer '.$this->openAiApiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => $this->openAiModel,
                    'input' => $prompt,
                ],
                'timeout' => 30,
            ]);

            $payload = $response->toArray(false);
            $text = $this->extractTextFromOpenAi($payload);

            if ('' === $text) {
                throw new \RuntimeException('OpenAI n\'a retourné aucun texte.');
            }

            return $text;
        } catch (\DomainException $exception) {
            throw $exception;
        } catch (\Throwable $exception) {
            $this->logger->warning('Assistant IA OpenAI indisponible.', ['error' => $exception->getMessage()]);
            throw new \DomainException('Assistant indisponible pour le moment.');
        }
    }

    private function requestGroq(string $prompt): string
    {
        // groq: cle tab9a local fi .env.local, ma tetpushach lel GitHub
        if ('' === trim($this->groqApiKey)) {
            throw new \DomainException('Assistant indisponible: la cle Groq est absente.');
        }

        try {
            $response = $this->httpClient->request('POST', 'https://api.groq.com/openai/v1/chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer '.$this->groqApiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => $this->groqModel,
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'Tu retournes uniquement un objet JSON valide, sans introduction, sans markdown, sans commentaire.',
                        ],
                        [
                            'role' => 'user',
                            'content' => $prompt,
                        ],
                    ],
                    'response_format' => ['type' => 'json_object'],
                    'temperature' => 0.4,
                ],
                'timeout' => 30,
            ]);

            $payload = $response->toArray(false);
            // groq: API chat completions traja3 texte fi choices[0].message.content
            $text = $this->extractTextFromChatCompletion($payload);

            if ('' === $text) {
                throw new \RuntimeException('Groq n a retourne aucun texte.');
            }

            return $text;
        } catch (\DomainException $exception) {
            throw $exception;
        } catch (\Throwable $exception) {
            $this->logger->warning('Assistant IA Groq indisponible.', ['error' => $exception->getMessage()]);
            throw new \DomainException('Assistant indisponible pour le moment.');
        }
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function extractTextFromOpenAi(array $payload): string
    {
        if (isset($payload['output_text']) && is_string($payload['output_text'])) {
            return trim($payload['output_text']);
        }

        $texts = [];
        array_walk_recursive($payload, static function (mixed $value, string|int $key) use (&$texts): void {
            if ('text' === $key && is_string($value)) {
                $texts[] = $value;
            }
        });

        return trim(implode("\n", $texts));
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function extractTextFromChatCompletion(array $payload): string
    {
        $content = $payload['choices'][0]['message']['content'] ?? null;

        return is_string($content) ? trim($content) : '';
    }

    /**
     * @param array{titre?: mixed, description?: mixed, categorie?: mixed, unitePrix?: mixed, type?: mixed, localisation?: mixed} $context
     */
    private function buildPrompt(array $context): string
    {
        $payload = [
            'titre' => trim((string) ($context['titre'] ?? '')),
            'description' => trim((string) ($context['description'] ?? '')),
            'categorie' => trim((string) ($context['categorie'] ?? '')),
            'unitePrix' => trim((string) ($context['unitePrix'] ?? '')),
            'type' => trim((string) ($context['type'] ?? '')),
            'localisation' => trim((string) ($context['localisation'] ?? '')),
        ];
        $encodedPayload = json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        return sprintf(<<<PROMPT
Tu assistes un formulaire Marketplace agricole en francais.
Retourne uniquement un objet JSON valide avec exactement ces cles:
- titre
- description
- categorie
- unitePrix
- qualityScore
- qualityAdvice

Contraintes:
- style professionnel et clair
- ne pas inventer de caracteristiques techniques absentes
- titre court (max 150 caracteres)
- description exploitable en quelques phrases
- categorie courte
- unitePrix adaptee au type de l annonce
- qualityScore entier entre 0 et 100 selon clarte, completude et pertinence
- qualityAdvice conseil court et concret pour ameliorer l annonce
- pour une vente, privilegier une unite liee a la quantite
- pour une location, privilegier une unite liee a la duree

Contexte actuel:
%s
PROMPT, $encodedPayload ?: '{}');
    }

    /**
     * @return array<string, mixed>
     */
    private function decodeJsonPayload(string $rawText): array
    {
        $normalized = trim($rawText);
        $normalized = preg_replace('/^```(?:json)?|```$/m', '', $normalized) ?? $normalized;
        $normalized = trim($normalized);

        $decoded = json_decode($normalized, true);

        if (!is_array($decoded)) {
            // ia: ken model zed phrase 9bal JSON, nesta5rjou JSON men west texte
            $firstBrace = strpos($normalized, '{');
            $lastBrace = strrpos($normalized, '}');

            if (false !== $firstBrace && false !== $lastBrace && $lastBrace > $firstBrace) {
                $decoded = json_decode(substr($normalized, $firstBrace, $lastBrace - $firstBrace + 1), true);
            }
        }

        if (!is_array($decoded)) {
            throw new \DomainException('Assistant indisponible pour le moment.');
        }

        return $decoded;
    }

    private function sanitizeSuggestion(mixed $value, int $maxLength): string
    {
        $text = trim((string) $value);

        if ('' === $text) {
            return '';
        }

        return substr($text, 0, $maxLength);
    }

    private function sanitizeScore(mixed $value): int
    {
        if (!is_numeric($value)) {
            return 0;
        }

        return max(0, min(100, (int) round((float) $value)));
    }
}
