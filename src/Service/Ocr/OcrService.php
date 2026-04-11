<?php

declare(strict_types=1);

namespace App\Service\Ocr;

interface OcrService
{
    public function extractText(string $imagePath, string $language = 'ara+eng'): OcrResult;
}
