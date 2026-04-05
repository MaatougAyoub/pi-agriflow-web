<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Entity\CollabApplication;
use App\Entity\CollabRequest;
use App\Entity\Utilisateur;
use App\Repository\CollabApplicationRepository;
use App\Service\CollabApplicationService;
use App\Service\ContentModerationService;
use App\Service\CollabRequestService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class CollabApplicationServiceTest extends TestCase
{
    private EntityManagerInterface&MockObject       $em;
    private CollabApplicationRepository&MockObject  $repo;
    private CollabApplicationService                $service;

    protected function setUp(): void
    {
        $this->em   = $this->createMock(EntityManagerInterface::class);
        $this->repo = $this->createMock(CollabApplicationRepository::class);

        $this->service = new CollabApplicationService($this->em, $this->repo);
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    private function makeRequest(int $requesterId = 10, bool $open = true, bool $expired = false): CollabRequest
    {
        $req = $this->createMock(CollabRequest::class);
        $requester = $this->createMock(Utilisateur::class);
        $requester->method('getId')->willReturn($requesterId);

        $req->method('getRequester')->willReturn($requester);
        $req->method('isOpen')->willReturn($open);
        $req->method('isExpired')->willReturn($expired);
        $req->method('getId')->willReturn(1);

        return $req;
    }

    private function makeCandidate(int $id): Utilisateur
    {
        $candidate = $this->createMock(Utilisateur::class);
        $candidate->method('getId')->willReturn($id);

        return $candidate;
    }

    private function makeApplication(): CollabApplication
    {
        return (new CollabApplication())
            ->setFullName('Test User')
            ->setPhone('+21612345678')
            ->setEmail('test@test.com')
            ->setYearsOfExperience(3)
            ->setMotivation(str_repeat('a', 60))
            ->setExpectedSalary(50.00);
    }

    // ── Tests ────────────────────────────────────────────────────────────────

    public function testApplySuccessPersistsApplication(): void
    {
        $request   = $this->makeRequest(requesterId: 10);
        $candidate = $this->makeCandidate(20);
        $app       = $this->makeApplication();

        $this->repo->method('hasApplied')->willReturn(false);
        $this->em->expects(self::once())->method('persist')->with($app);
        $this->em->expects(self::once())->method('flush');

        $this->service->apply($app, $request, $candidate);
    }

    public function testApplyThrowsWhenCandidateIsRequester(): void
    {
        $request   = $this->makeRequest(requesterId: 5);
        $candidate = $this->makeCandidate(5); // same id as requester

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('propre demande');

        $this->service->apply($this->makeApplication(), $request, $candidate);
    }

    public function testApplyThrowsWhenRequestIsClosed(): void
    {
        $request   = $this->makeRequest(requesterId: 10, open: false);
        $candidate = $this->makeCandidate(20);

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('plus ouverte');

        $this->service->apply($this->makeApplication(), $request, $candidate);
    }

    public function testApplyThrowsWhenRequestIsExpired(): void
    {
        $request   = $this->makeRequest(requesterId: 10, open: true, expired: true);
        $candidate = $this->makeCandidate(20);

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('date limite');

        $this->service->apply($this->makeApplication(), $request, $candidate);
    }

    public function testApplyThrowsOnDuplicateApplication(): void
    {
        $request   = $this->makeRequest(requesterId: 10);
        $candidate = $this->makeCandidate(20);

        $this->repo->method('hasApplied')->willReturn(true); // already applied

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('déjà postulé');

        $this->service->apply($this->makeApplication(), $request, $candidate);
    }

    public function testUpdateStatusAcceptsValidStatus(): void
    {
        $app = new CollabApplication();

        $this->em->expects(self::once())->method('flush');

        $this->service->updateStatus($app, CollabApplication::STATUS_ACCEPTED);

        self::assertSame(CollabApplication::STATUS_ACCEPTED, $app->getStatus());
    }

    public function testUpdateStatusRejectsInvalidStatus(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->service->updateStatus(new CollabApplication(), 'unknown_status');
    }
}
