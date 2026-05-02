<?php

declare(strict_types=1);

namespace App\Service\Ocr;

use Symfony\Component\Process\Process;

final class TesseractOcrService implements OcrService
{
    public function extractText(string $imagePath, string $language = 'ara+eng'): OcrResult
    {
        if (trim($imagePath) === '') {
            throw new \InvalidArgumentException('imagePath est vide.');
        }

        if (!is_file($imagePath)) {
            throw new \InvalidArgumentException('Image introuvable: ' . $imagePath);
        }

        $preprocessedPath = $this->preprocessToTempPng($imagePath);

        try {
            $outputs = [];
            $errors = [];

            // First pass: preprocessed image, mixed languages.
            try {
                $outputs[] = $this->runTesseract($preprocessedPath, trim($language) !== '' ? $language : 'ara+eng', 6);
            } catch (\Throwable $exception) {
                $errors[] = $exception->getMessage();
            }

            // Second pass: original image, Arabic-focused mode to improve name extraction.
            try {
                $outputs[] = $this->runTesseract($imagePath, 'ara', 11);
            } catch (\Throwable $exception) {
                $errors[] = $exception->getMessage();
            }

            $outputs = array_values(array_filter(array_map('trim', $outputs), static fn (string $line): bool => $line !== ''));

            if ($outputs === []) {
                throw new \RuntimeException('Echec OCR Tesseract: ' . implode(' | ', $errors));
            }

            $combinedText = trim(implode("\n", array_unique($outputs)));

            return new OcrResult($combinedText);
        } finally {
            if ($preprocessedPath !== $imagePath) {
                @unlink($preprocessedPath);
            }
        }
    }

    private function runTesseract(string $imagePath, string $language, int $psm): string
    {
        $process = new Process([
            $this->resolveTesseractBin(),
            $imagePath,
            'stdout',
            '-l',
            $language,
            '--tessdata-dir',
            $this->resolveTessDataDir(),
            '--oem',
            '1',
            '--psm',
            (string) $psm,
        ]);

        $process->setTimeout(45);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new \RuntimeException(trim($process->getErrorOutput()));
        }

        return trim($process->getOutput());
    }

    private function resolveTessDataDir(): string
    {
        $env = $this->readConfig('TESSDATA_PREFIX');

        if ($env !== '') {
            $candidate = rtrim($env, "\\/");

            if (strtolower((string) basename($candidate)) === 'tessdata' && is_dir($candidate)) {
                return $candidate;
            }

            $candidateWithTessdata = $candidate . DIRECTORY_SEPARATOR . 'tessdata';
            if (is_dir($candidateWithTessdata)) {
                return $candidateWithTessdata;
            }
        }

        $candidates = [
            'C:\\Program Files\\Tesseract-OCR\\tessdata',
            'C:\\Program Files (x86)\\Tesseract-OCR\\tessdata',
            'C:\\tesseract\\tessdata',
            '/usr/share/tesseract-ocr/5/tessdata',
            '/usr/share/tesseract-ocr/4.00/tessdata',
            '/usr/share/tessdata',
        ];

        foreach ($candidates as $candidate) {
            if (is_dir($candidate)) {
                return $candidate;
            }
        }

        throw new \RuntimeException(
            'Tesseract introuvable. Installez Tesseract OCR et configurez TESSDATA_PREFIX (ex: C:/Program Files/Tesseract-OCR ou C:/Program Files/Tesseract-OCR/tessdata).'
        );
    }

    private function resolveTesseractBin(): string
    {
        $envBin = $this->readConfig('TESSERACT_BIN');
        if ($envBin !== '' && is_file($envBin)) {
            return $envBin;
        }

        $candidates = [
            'C:\\Program Files\\Tesseract-OCR\\tesseract.exe',
            'C:\\Program Files (x86)\\Tesseract-OCR\\tesseract.exe',
            'C:\\tesseract\\tesseract.exe',
        ];

        foreach ($candidates as $candidate) {
            if (is_file($candidate)) {
                return $candidate;
            }
        }

        return 'tesseract';
    }

    private function preprocessToTempPng(string $inputPath): string
    {
        if (!function_exists('imagecreatefromstring')) {
            return $inputPath;
        }

        $raw = @file_get_contents($inputPath);
        if ($raw === false) {
            return $inputPath;
        }

        $original = @imagecreatefromstring($raw);
        if ($original === false) {
            return $inputPath;
        }

        $origW = imagesx($original);
        $origH = imagesy($original);

        $targetW = max($origW, 1400);
        $scale = $targetW / $origW;
        $targetH = max(1, (int) round($origH * $scale));

        $scaled = imagecreatetruecolor($targetW, $targetH);
        imagecopyresampled($scaled, $original, 0, 0, 0, 0, $targetW, $targetH, $origW, $origH);

        $gray = imagecreatetruecolor($targetW, $targetH);
        imagecopy($gray, $scaled, 0, 0, 0, 0, $targetW, $targetH);
        imagefilter($gray, IMG_FILTER_GRAYSCALE);

        $tmpBase = tempnam(sys_get_temp_dir(), 'ocr_');
        if ($tmpBase === false) {
            imagedestroy($original);
            imagedestroy($scaled);
            imagedestroy($gray);

            return $inputPath;
        }

        $tmpPng = $tmpBase . '.png';
        @unlink($tmpBase);
        imagepng($gray, $tmpPng);

        imagedestroy($original);
        imagedestroy($scaled);
        imagedestroy($gray);

        return $tmpPng;
    }

    private function readConfig(string $key): string
    {
        $value = $_SERVER[$key] ?? $_ENV[$key] ?? getenv($key) ?: null;

        return is_string($value) ? trim($value) : '';
    }
}
