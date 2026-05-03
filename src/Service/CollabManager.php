<?php

namespace App\Service;

use App\Entity\CollabRequest;
use InvalidArgumentException;

class CollabManager
{
    /**
     * Valide une demande de collaboration selon les règles métier.
     *
     * @param CollabRequest $request
     * @return bool
     * @throws InvalidArgumentException
     */
    public function validate(CollabRequest $request): bool
    {
        // Règle 1 : Le titre est obligatoire
        if (empty($request->getTitle())) {
            throw new InvalidArgumentException('Le titre est obligatoire');
        }

        // Règle 2 : Le salaire doit être positif
        if ($request->getSalary() < 0) {
            throw new InvalidArgumentException('Le salaire doit être positif');
        }

        // Règle 3 : La date de fin doit être après la date de début
        if ($request->getStartDate() && $request->getEndDate()) {
            if ($request->getEndDate() <= $request->getStartDate()) {
                throw new InvalidArgumentException('La date de fin doit être après la date de début');
            }
        }

        return true;
    }
}
