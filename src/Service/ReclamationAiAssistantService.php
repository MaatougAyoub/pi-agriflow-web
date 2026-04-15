<?php

declare(strict_types=1);

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class ReclamationAiAssistantService
{
    private const GROQ_ENDPOINT = 'https://api.groq.com/openai/v1/chat/completions';
    private const GROQ_MODEL = 'llama-3.1-8b-instant';

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly LoggerInterface $logger,
        #[Autowire('%env(string:GROQ_API_KEY)%')]
        private readonly string $groqApiKey,
    ) {
    }

    public function generateDescriptionFromTitle(string $title): string
    {
        $normalizedTitle = trim($title);

        if ($normalizedTitle === '') {
            throw new \DomainException('Veuillez entrer un titre avant de generer une description.');
        }

        if (trim($this->groqApiKey) === '') {
            throw new \DomainException('Assistant indisponible: la cle Groq est absente.');
        }

        $prompt = sprintf(
            "Titre de reclamation: %s\n\n".
            "Genere une description claire et utile pour un formulaire de reclamation client.\n".
            "Contraintes:\n".
            "- reponds uniquement en francais\n".
            "- entre 70 et 180 mots\n".
            "- style professionnel et concret\n".
            "- propose des faits plausibles sans exageration\n".
            "- pas de liste a puces\n".
            "- ne mentionne pas que le texte vient dune IA",
            $normalizedTitle
        );

        try {
            $response = $this->httpClient->request('POST', self::GROQ_ENDPOINT, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->groqApiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => self::GROQ_MODEL,
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'Tu aides un utilisateur a rediger une reclamation pour une plateforme agricole.',
                        ],
                        [
                            'role' => 'user',
                            'content' => $prompt,
                        ],
                    ],
                    'temperature' => 0.4,
                    'max_tokens' => 320,
                ],
                'timeout' => 30,
            ]);

            $payload = $response->toArray(false);
            $rawDescription = (string) ($payload['choices'][0]['message']['content'] ?? '');
            $description = trim($rawDescription);

            if ($description === '') {
                throw new \RuntimeException('Groq n a retourne aucun texte.');
            }

            return mb_substr($description, 0, 2000);
        } catch (\DomainException $exception) {
            throw $exception;
        } catch (\Throwable $exception) {
            $this->logger->warning('Generation IA reclamation indisponible.', [
                'error' => $exception->getMessage(),
            ]);

            throw new \DomainException('Generation IA indisponible pour le moment.');
        }
    }
}
