<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Entity\CollabRequest;
use App\Entity\Utilisateur;
use App\Repository\CollabRequestRepository;
use App\Service\CollabRequestService;
use App\Service\ContentModerationService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class CollabRequestServiceTest extends TestCase
{
    private EntityManagerInterface&MockObject   $em;
    private CollabRequestRepository&MockObject  $repo;
    private ContentModerationService            $moderation;
    private CollabRequestService                $service;

    protected function setUp(): void
    {
        $this->em         = $this->createMock(EntityManagerInterface::class);
        $this->repo       = $this->createMock(CollabRequestRepository::class);
        $this->moderation = new ContentModerationService();
        $this->service    = new CollabRequestService($this->em, $this->repo, $this->moderation);
    }

    private function makeValidRequest(): CollabRequest
    {
        return (new CollabRequest())
            ->setTitle('Besoin aide pour récolte')
            ->setDescription(str_repeat('description longue valide ', 5))
            ->setLocation('Sfax')
            ->setStartDate(new \DateTime('+1 day'))
            ->setEndDate(new \DateTime('+30 days'))
            ->setNeededPeople(3)
            ->setSalary(500.00);
    }

    private function makeRequester(): Utilisateur
    {
        $user = $this->createMock(Utilisateur::class);
        $user->method('getId')->willReturn(1);
        $user->method('getNom')->willReturn('Maatougui');
        $user->method('getPrenom')->willReturn('Ayoub');

        return $user;
    }

    public function testPublishPersistsValidRequest(): void
    {
        $request  = $this->makeValidRequest();
        $requester = $this->makeRequester();

        $this->em->expects(self::once())->method('persist')->with($request);
        $this->em->expects(self::once())->method('flush');

        $this->service->publish($request, $requester);

        self::assertSame($requester, $request->getRequester());
    }

    public function testPublishSetsPublisherName(): void
    {
        $request   = $this->makeValidRequest();
        $requester = $this->makeRequester();

        $this->em->method('persist');
        $this->em->method('flush');

        $this->service->publish($request, $requester);

        self::assertSame('Ayoub Maatougui', $request->getPublisher());
    }

    public function testPublishThrowsOnFlaggedContent(): void
    {
        $request = $this->makeValidRequest();
        $request->setTitle('Arnaque spam gratuit argent');
        $requester = $this->makeRequester();

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('inapproprié');

        $this->service->publish($request, $requester);
    }

    public function testDeleteRemovesRequest(): void
    {
        $request = $this->makeValidRequest();

        $this->em->expects(self::once())->method('remove')->with($request);
        $this->em->expects(self::once())->method('flush');

        $this->service->delete($request);
    }

    public function testChangeStatusUpdatesStatus(): void
    {
        $request = $this->makeValidRequest();
        $request->setStatus(CollabRequest::STATUS_OPEN);

        $this->em->expects(self::once())->method('flush');

        $this->service->changeStatus($request, CollabRequest::STATUS_CLOSED);

        self::assertSame(CollabRequest::STATUS_CLOSED, $request->getStatus());
    }
}
