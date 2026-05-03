<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\CollabApplication;
use PHPUnit\Framework\TestCase;

final class CollabApplicationTest extends TestCase
{
    public function testDefaultsAreInitialized(): void
    {
        $application = new CollabApplication();

        self::assertSame(CollabApplication::STATUS_PENDING, $application->getStatus());
        self::assertSame(0, $application->getYearsOfExperience());
        self::assertSame(0.0, $application->getExpectedSalary());
        self::assertInstanceOf(\DateTimeInterface::class, $application->getAppliedAt());
        self::assertInstanceOf(\DateTimeInterface::class, $application->getUpdatedAt());
    }

    public function testNullStringsAreNormalizedToEmptyStrings(): void
    {
        $application = new CollabApplication();

        $application
            ->setFullName(null)
            ->setPhone(null)
            ->setEmail(null)
            ->setMotivation(null);

        self::assertSame('', $application->getFullName());
        self::assertSame('', $application->getPhone());
        self::assertSame('', $application->getEmail());
        self::assertSame('', $application->getMotivation());
    }

    public function testExpectedSalarySupportsStringAndNull(): void
    {
        $application = new CollabApplication();

        $application->setExpectedSalary('2500.50');
        self::assertSame(2500.5, $application->getExpectedSalary());

        $application->setExpectedSalary(null);
        self::assertNull($application->getExpectedSalary());
    }

    public function testSetUpdatedAtValueRefreshesTimestamp(): void
    {
        $application = new CollabApplication();
        $application->setUpdatedAt(new \DateTime('2000-01-01 00:00:00'));

        $application->setUpdatedAtValue();

        $updatedAt = $application->getUpdatedAt();
        self::assertInstanceOf(\DateTimeInterface::class, $updatedAt);
        self::assertGreaterThan(
            (new \DateTime('2000-01-01 00:00:00'))->getTimestamp(),
            $updatedAt->getTimestamp()
        );
    }
}
