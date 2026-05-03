<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Service\ReclamationAiAssistantService;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

final class ReclamationAiAssistantServiceTest extends TestCase
{
    private function createService(MockResponse $response): ReclamationAiAssistantService
    {
        $httpClient = new MockHttpClient($response);

        $logger = $this->createMock(LoggerInterface::class);

        return new ReclamationAiAssistantService(
            $httpClient,
            $logger,
            'fake-api-key'
        );
    }

    /** ✅ TEST PRINCIPAL */
    public function testGenerateDescriptionReturnsDescription(): void
    {
        $responseBody = json_encode([
            'choices' => [
                [
                    'message' => [
                        'content' => 'Description IA valide pour test.',
                    ],
                ],
            ],
        ]);

        $service = $this->createService(
            new MockResponse($responseBody)
        );

        $result = $service->generateDescriptionFromTitle('Probleme irrigation');

        $this->assertSame(
            'Description IA valide pour test.',
            $result
        );
    }

    /** ✅ TITRE VIDE */
    public function testThrowsWhenTitleEmpty(): void
    {
        $this->expectException(\DomainException::class);

        $service = $this->createService(new MockResponse('{}'));

        $service->generateDescriptionFromTitle('');
    }

    /** ✅ API KEY ABSENTE */
    public function testThrowsWhenApiKeyMissing(): void
    {
        $httpClient = new MockHttpClient(new MockResponse('{}'));

        $logger = $this->createMock(LoggerInterface::class);

        $service = new ReclamationAiAssistantService(
            $httpClient,
            $logger,
            ''
        );

        $this->expectException(\DomainException::class);

        $service->generateDescriptionFromTitle('test');
    }

    /** ✅ REPONSE IA VIDE */
    public function testThrowsWhenResponseEmpty(): void
    {
        $responseBody = json_encode([
            'choices' => [
                [
                    'message' => [
                        'content' => '',
                    ],
                ],
            ],
        ]);

        $service = $this->createService(
            new MockResponse($responseBody)
        );

        $this->expectException(\DomainException::class);

        $service->generateDescriptionFromTitle('test');
    }

    /** ✅ TRONCATURE TEXTE */
    public function testTruncatesLongText(): void
    {
        $longText = str_repeat('A', 3000);

        $responseBody = json_encode([
            'choices' => [
                [
                    'message' => [
                        'content' => $longText,
                    ],
                ],
            ],
        ]);

        $service = $this->createService(
            new MockResponse($responseBody)
        );

        $result = $service->generateDescriptionFromTitle('test');

        $this->assertLessThanOrEqual(2000, mb_strlen($result));
    }
}