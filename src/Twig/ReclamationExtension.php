<?php

declare(strict_types=1);

namespace App\Twig;

use App\Repository\ReclamationRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ReclamationExtension extends AbstractExtension
{
    public function __construct(
        private readonly ReclamationRepository $reclamationRepository,
        private readonly Security $security,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('pending_reclamations_count', [$this, 'getPendingReclamationsCount']),
        ];
    }

    public function getPendingReclamationsCount(): int
    {
        if (!$this->security->isGranted('ROLE_ADMIN')) {
            return 0;
        }

        return $this->reclamationRepository->countPending();
    }
}
