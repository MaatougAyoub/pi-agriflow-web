<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Service\AnnonceAiAssistantService;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

final class AnnonceAiAssistantServiceTest extends TestCase
{
    public function testGenerateSuggestionsReturnsGroqPayload(): void
    {
        $service = $this->createService('groq', [
            new MockResponse(json_encode([
                'choices' => [[
                    'message' => [
                        'content' => json_encode([
                            'titre' => 'Tracteur agricole compact',
                            'description' => 'Tracteur disponible pour travaux saisonniers avec entretien recent.',
                            'categorie' => 'Materiel agricole',
                            'unitePrix' => 'jour',
                            'qualityScore' => 82,
                            'qualityAdvice' => 'Ajouter la puissance et l etat du materiel.',
                        ]),
                    ],
                ]],
            ]) ?: '{}'),
        ]);

        $result = $service->generateSuggestions([
            'titre' => 'tracteur',
            'description' => 'desc',
            'type' => 'location',
            'localisation' => 'Sousse',
        ]);

        self::assertSame('groq', $result['provider']);
        self::assertSame('Tracteur agricole compact', $result['titre']);
        self::assertSame('Materiel agricole', $result['categorie']);
        self::assertSame(82, $result['qualityScore']);
    }

    public function testGenerateSuggestionsExtractsEmbeddedJsonFromModelOutput(): void
    {
        $service = $this->createService('groq', [
            new MockResponse(json_encode([
                'choices' => [[
                    'message' => [
                        'content' => "Voici la proposition:\n```json\n{\"titre\":\"Pulverisateur pro\",\"description\":\"Pulverisateur disponible pour traitement localise avec cuve propre.\",\"categorie\":\"Materiel\",\"unitePrix\":\"jour\",\"qualityScore\":71,\"qualityAdvice\":\"Ajouter capacite et autonomie.\"}\n```",
                    ],
                ]],
            ]) ?: '{}'),
        ]);

        $result = $service->generateSuggestions([
            'titre' => 'pulve',
            'description' => 'desc',
        ]);

        self::assertSame('Pulverisateur pro', $result['titre']);
        self::assertSame('jour', $result['unitePrix']);
        self::assertSame(71, $result['qualityScore']);
    }

    public function testGenerateSuggestionsBoundsQualityScore(): void
    {
        $service = $this->createService('groq', [
            new MockResponse(json_encode([
                'choices' => [[
                    'message' => [
                        'content' => json_encode([
                            'titre' => 'Moissonneuse',
                            'description' => 'Moissonneuse disponible avec chauffeur.',
                            'categorie' => 'Materiel',
                            'unitePrix' => 'jour',
                            'qualityScore' => 170,
                            'qualityAdvice' => 'Ajouter modele exact.',
                        ]),
                    ],
                ]],
            ]) ?: '{}'),
        ]);

        $result = $service->generateSuggestions([]);

        self::assertSame(100, $result['qualityScore']);
    }

    public function testGenerateSuggestionsFallsBackToGeminiWhenProviderIsUnknown(): void
    {
        $service = $this->createService('unknown-provider', [
            new MockResponse(json_encode([
                'candidates' => [[
                    'content' => [
                        'parts' => [[
                            'text' => json_encode([
                                'titre' => 'Semoir pneumatique',
                                'description' => 'Semoir bien entretenu pret pour la saison.',
                                'categorie' => 'Materiel',
                                'unitePrix' => 'jour',
                                'qualityScore' => 64,
                                'qualityAdvice' => 'Ajouter largeur de travail.',
                            ]),
                        ]],
                    ],
                ]],
            ]) ?: '{}'),
        ], geminiApiKey: 'gemini-key');

        $result = $service->generateSuggestions([]);

        self::assertSame('gemini', $result['provider']);
        self::assertSame('Semoir pneumatique', $result['titre']);
    }

    public function testGenerateSuggestionsThrowsWhenGroqKeyIsMissing(): void
    {
        $service = $this->createService('groq', [], groqApiKey: '');

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Assistant indisponible: la cle Groq est absente.');

        $service->generateSuggestions([]);
    }

    public function testGenerateSuggestionsThrowsWhenPayloadCannotBeDecoded(): void
    {
        $service = $this->createService('groq', [
            new MockResponse(json_encode([
                'choices' => [[
                    'message' => [
                        'content' => 'ceci nest pas du json',
                    ],
                ]],
            ]) ?: '{}'),
        ]);

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Assistant indisponible pour le moment.');

        $service->generateSuggestions([]);
    }

    /**
     * @param list<MockResponse> $responses
     */
    private function createService(
        string $provider,
        array $responses,
        string $geminiApiKey = 'gemini-key',
        string $groqApiKey = 'groq-key'
    ): AnnonceAiAssistantService {
        return new AnnonceAiAssistantService(
            new MockHttpClient($responses),
            new NullLogger(),
            $provider,
            $geminiApiKey,
            'gemini-1.5-flash',
            '',
            'gpt-4.1-mini',
            $groqApiKey,
            'llama-3.1-8b-instant',
        );
    }
}
