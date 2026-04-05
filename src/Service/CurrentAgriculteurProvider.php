<?php

namespace App\Service;

class CurrentAgriculteurProvider
{
    public function __construct(
        private readonly int $currentTestAgriculteurId,
    ) {
    }

    public function getCurrentTestUserId(): int
    {
        return $this->currentTestAgriculteurId;
    }
}
