<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Service\ContentModerationService;
use PHPUnit\Framework\TestCase;

final class ContentModerationServiceTest extends TestCase
{
    private ContentModerationService $service;

    protected function setUp(): void
    {
        $this->service = new ContentModerationService();
    }

    public function testCleanContentIsNotFlagged(): void
    {
        self::assertFalse($this->service->isFlagged('Je cherche un aide agricole pour récolte olive'));
    }

    public function testSpamKeywordIsFlagged(): void
    {
        self::assertTrue($this->service->isFlagged('Ceci est du spam pur'));
    }

    public function testFraudKeywordIsFlagged(): void
    {
        self::assertTrue($this->service->isFlagged('Arnaque détectée ici'));
    }

    public function testProfanityIsFlagged(): void
    {
        self::assertTrue($this->service->isFlagged('contenu avec merde dedans'));
    }

    public function testModerationReasonReturnsNullForCleanContent(): void
    {
        self::assertNull($this->service->moderationReason('texte propre sans problème'));
    }

    public function testModerationReasonReturnsStringForFlaggedContent(): void
    {
        $reason = $this->service->moderationReason('ceci est spam et cliquez ici');
        self::assertNotNull($reason);
        self::assertStringContainsString('spam', strtolower($reason));
    }

    public function testCaseSensitivity(): void
    {
        self::assertTrue($this->service->isFlagged('SPAM DETECTED'));
        self::assertTrue($this->service->isFlagged('Arnaque'));
    }
}
