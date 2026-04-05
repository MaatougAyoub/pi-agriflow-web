<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Entity\CollabApplication;
use App\Entity\CollabRequest;
use App\Service\CandidateMatchingService;
use PHPUnit\Framework\TestCase;

final class CandidateMatchingServiceTest extends TestCase
{
    private CandidateMatchingService $service;

    protected function setUp(): void
    {
        $this->service = new CandidateMatchingService();
    }

    // ── Score calculation ─────────────────────────────────────────────────────

    public function testScoreTotalIsBetween0And100(): void
    {
        $app     = $this->makeApplication(yearsOfExperience: 3, expectedSalary: 80.0);
        $request = $this->makeRequest(salaryPerDay: 100.0, open: true);

        $score = $this->service->calculateScore($app, $request);

        self::assertGreaterThanOrEqual(0, $score['total']);
        self::assertLessThanOrEqual(100, $score['total']);
    }

    public function testPerfectCandidateGetsHighScore(): void
    {
        // 10+ years experience, salary expected <= offered
        $app     = $this->makeApplication(yearsOfExperience: 12, expectedSalary: 50.0);
        $request = $this->makeRequest(salaryPerDay: 100.0, open: true);

        $score = $this->service->calculateScore($app, $request);

        self::assertGreaterThanOrEqual(CandidateMatchingService::GOOD_MATCH_THRESHOLD, $score['total']);
    }

    public function testBeginnerWithHighSalaryGetsLowScore(): void
    {
        $app     = $this->makeApplication(yearsOfExperience: 0, expectedSalary: 500.0);
        $request = $this->makeRequest(salaryPerDay: 50.0, open: true);

        $score = $this->service->calculateScore($app, $request);

        self::assertLessThan(CandidateMatchingService::GOOD_MATCH_THRESHOLD, $score['total']);
    }

    public function testRankApplicationsReturnsSortedDescending(): void
    {
        $request = $this->makeRequest(salaryPerDay: 100.0, open: true);

        $appA = $this->makeApplication(yearsOfExperience: 12, expectedSalary: 50.0);  // should rank #1
        $appB = $this->makeApplication(yearsOfExperience: 0,  expectedSalary: 500.0); // should rank last

        $ranking = $this->service->rankApplications([$appB, $appA], $request);

        self::assertCount(2, $ranking);
        // First entry should have a higher or equal score than the second
        self::assertGreaterThanOrEqual(
            $ranking[1]['score']['total'],
            $ranking[0]['score']['total'],
        );
    }

    public function testRankApplicationsWithEmptyListReturnsEmpty(): void
    {
        $request = $this->makeRequest(salaryPerDay: 100.0, open: true);
        $ranking = $this->service->rankApplications([], $request);

        self::assertEmpty($ranking);
    }

    public function testIsKnownTunisianCityReturnsTrueForKnownCity(): void
    {
        self::assertTrue($this->service->isKnownTunisianCity('Tunis centre'));
        self::assertTrue($this->service->isKnownTunisianCity('sfax-sud'));
    }

    public function testIsKnownTunisianCityReturnsFalseForUnknown(): void
    {
        self::assertFalse($this->service->isKnownTunisianCity('Paris'));
        self::assertFalse($this->service->isKnownTunisianCity(''));
    }

    // ── Private helpers ──────────────────────────────────────────────────────

    private function makeApplication(int $yearsOfExperience, float $expectedSalary): CollabApplication
    {
        $app = new CollabApplication();
        $app->setYearsOfExperience($yearsOfExperience);
        $app->setExpectedSalary($expectedSalary);
        $app->setFullName('Test');
        $app->setPhone('+21699000000');
        $app->setEmail('t@t.com');
        $app->setMotivation(str_repeat('a', 60));

        return $app;
    }

    private function makeRequest(float $salaryPerDay, bool $open, bool $expired = false): CollabRequest
    {
        $req = $this->createMock(CollabRequest::class);
        $req->method('getSalaryPerDayAsFloat')->willReturn($salaryPerDay);
        $req->method('isOpen')->willReturn($open);
        $req->method('isExpired')->willReturn($expired);

        return $req;
    }
}
