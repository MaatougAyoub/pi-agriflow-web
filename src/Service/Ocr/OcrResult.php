<?php

declare(strict_types=1);

namespace App\Service\Ocr;

final class OcrResult
{
    public function __construct(
        private readonly string $fullText,
    ) {
    }

    public function getFullText(): string
    {
        return $this->fullText;
    }
}
