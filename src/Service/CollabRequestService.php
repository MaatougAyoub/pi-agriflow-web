<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\CollabRequest;
use App\Entity\Utilisateur;
use App\Repository\CollabRequestRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Business logic for CollabRequest lifecycle.
 */
class CollabRequestService
{
    public function __construct(
        private readonly EntityManagerInterface  $em,
        private readonly CollabRequestRepository $repository,
        private readonly ContentModerationService $moderationService,
    ) {
    }

    /**
     * Publishes a new collaboration request after content moderation.
     *
     * @throws \DomainException if the content is flagged as inappropriate
     */
    public function publish(CollabRequest $request, Utilisateur $requester): void
    {
        $flagged = $this->moderationService->isFlagged($request->getTitle().' '.$request->getDescription());
        if ($flagged) {
            throw new \DomainException('Votre demande contient un contenu inapproprié et ne peut pas être publiée.');
        }

        $request->setRequester($requester);

        if ($request->getPublisher() === null) {
            $request->setPublisher($requester->getPrenom().' '.$requester->getNom());
        }

        $this->em->persist($request);
        $this->em->flush();
    }

    /**
     * Updates an existing request.
     *
     * @throws \DomainException if the content is flagged
     */
    public function update(CollabRequest $request): void
    {
        $flagged = $this->moderationService->isFlagged($request->getTitle().' '.$request->getDescription());
        if ($flagged) {
            throw new \DomainException('Votre demande contient un contenu inapproprié.');
        }

        $this->em->flush();
    }

    /**
     * Deletes a request and all its applications (cascade).
     */
    public function delete(CollabRequest $request): void
    {
        $this->em->remove($request);
        $this->em->flush();
    }

    /**
     * Changes just the status of a request.
     */
    public function changeStatus(CollabRequest $request, string $status): void
    {
        $request->setStatus($status);
        $this->em->flush();
    }
}
