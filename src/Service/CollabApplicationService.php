<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\CollabApplication;
use App\Entity\CollabRequest;
use App\Entity\Utilisateur;
use App\Repository\CollabApplicationRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Business logic for CollabApplication lifecycle.
 */
class CollabApplicationService
{
    public function __construct(
        private readonly EntityManagerInterface       $em,
        private readonly CollabApplicationRepository  $repository,
    ) {
    }

    /**
     * Submits a new application, enforcing business rules.
     *
     * @throws \DomainException on rule violation
     */
    public function apply(CollabApplication $application, CollabRequest $request, Utilisateur $candidate): void
    {
        // Rule: cannot apply to own request
        if ($request->getRequester()?->getId() === $candidate->getId()) {
            throw new \DomainException('Vous ne pouvez pas postuler à votre propre demande.');
        }

        // Rule: request must still be open
        if (!$request->isOpen()) {
            throw new \DomainException('Cette demande n\'est plus ouverte aux candidatures.');
        }

        // Rule: end date must not be passed
        if ($request->isExpired()) {
            throw new \DomainException('La date limite de cette demande est dépassée.');
        }

        // Rule: no duplicate application
        if ($this->repository->hasApplied($candidate->getId(), $request->getId())) {
            throw new \DomainException('Vous avez déjà postulé à cette demande.');
        }

        $application->setRequest($request);
        $application->setCandidate($candidate);

        $this->em->persist($application);
        $this->em->flush();
    }

    /**
     * Updates the status of an application (admin action).
     *
     * @throws \InvalidArgumentException on unknown status
     */
    public function updateStatus(CollabApplication $application, string $status): void
    {
        if (!in_array($status, array_values(CollabApplication::STATUSES), true)) {
            throw new \InvalidArgumentException(sprintf('Statut "%s" inconnu.', $status));
        }

        $application->setStatus($status);
        $this->em->flush();
    }

    /**
     * Deletes an application.
     */
    public function delete(CollabApplication $application): void
    {
        $this->em->remove($application);
        $this->em->flush();
    }
}
