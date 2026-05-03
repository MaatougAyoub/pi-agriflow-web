<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Entity\Culture;
use App\Service\CultureManager;
use PHPUnit\Framework\TestCase;

final class CultureManagerTest extends TestCase
{
    public function testPublishAllowed(): void
    {
        $culture = (new Culture())
            ->setParcelleId(10)
            ->setProprietaireId(5)
            ->setEtat(Culture::ETAT_EN_COURS)
            ->setNom('Culture test');

        $result = (new CultureManager())->canPublish($culture);

        self::assertTrue($result);
    }

    public function testPublishBlocked(): void
    {
        $culture = (new Culture())
            ->setParcelleId(10)
            ->setProprietaireId(5)
            ->setEtat(Culture::ETAT_VENDUE)
            ->setNom('Culture test');

        $result = (new CultureManager())->canPublish($culture);

        self::assertFalse($result);
    }
}
