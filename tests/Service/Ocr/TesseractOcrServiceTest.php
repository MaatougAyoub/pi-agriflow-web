<?php

declare(strict_types=1);

namespace App\Tests\Service\Ocr;

use App\Service\Ocr\TesseractOcrService;
use PHPUnit\Framework\TestCase;

final class TesseractOcrServiceTest extends TestCase
{
    private const PNG_BASE64 = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMBA8pK2LEAAAAASUVORK5CYII=';

    /** @var string[] */
    private array $tempPaths = [];

    protected function tearDown(): void
    {
        foreach ([
            'TESSERACT_BIN',
            'TESSDATA_PREFIX',
            'OCR_PSM6_OUT',
            'OCR_PSM6_ERR',
            'OCR_PSM6_EXIT',
            'OCR_PSM11_OUT',
            'OCR_PSM11_ERR',
            'OCR_PSM11_EXIT',
            'OCR_LOG_FILE',
        ] as $key) {
            $this->setEnv($key, null);
        }

        $this->cleanupTempPaths();
    }

    public function testExtractTextThrowsWhenImagePathEmpty(): void
    {
        $service = new TesseractOcrService();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('imagePath est vide.');

        $service->extractText('');
    }

    public function testExtractTextThrowsWhenImageMissing(): void
    {
        $service = new TesseractOcrService();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Image introuvable: missing.png');

        $service->extractText('missing.png');
    }

    public function testExtractTextReturnsCombinedTextFromTwoPasses(): void
    {
        $imagePath = $this->createTempImage();
        $tessdataDir = $this->createTessdataDir();
        $tesseractBin = $this->createFakeTesseractScript();

        $this->setEnv('TESSERACT_BIN', $tesseractBin);
        $this->setEnv('TESSDATA_PREFIX', $tessdataDir);
        $this->setEnv('OCR_PSM6_OUT', 'FIRST_PASS');
        $this->setEnv('OCR_PSM11_OUT', 'SECOND_PASS');
        $this->setEnv('OCR_PSM6_EXIT', '0');
        $this->setEnv('OCR_PSM11_EXIT', '0');

        $service = new TesseractOcrService();
        $result = $service->extractText($imagePath);

        self::assertSame("FIRST_PASS\nSECOND_PASS", $result->getFullText());
    }

    public function testExtractTextReturnsSecondPassWhenFirstFails(): void
    {
        $imagePath = $this->createTempImage();
        $tessdataDir = $this->createTessdataDir();
        $tesseractBin = $this->createFakeTesseractScript();

        $this->setEnv('TESSERACT_BIN', $tesseractBin);
        $this->setEnv('TESSDATA_PREFIX', $tessdataDir);
        $this->setEnv('OCR_PSM6_ERR', 'first pass failed');
        $this->setEnv('OCR_PSM6_EXIT', '1');
        $this->setEnv('OCR_PSM11_OUT', 'SECOND_PASS');
        $this->setEnv('OCR_PSM11_EXIT', '0');

        $service = new TesseractOcrService();
        $result = $service->extractText($imagePath);

        self::assertSame('SECOND_PASS', $result->getFullText());
    }

    public function testExtractTextThrowsWhenBothPassesFail(): void
    {
        $imagePath = $this->createTempImage();
        $tessdataDir = $this->createTessdataDir();
        $tesseractBin = $this->createFakeTesseractScript();

        $this->setEnv('TESSERACT_BIN', $tesseractBin);
        $this->setEnv('TESSDATA_PREFIX', $tessdataDir);
        $this->setEnv('OCR_PSM6_ERR', 'first pass failed');
        $this->setEnv('OCR_PSM6_EXIT', '1');
        $this->setEnv('OCR_PSM11_ERR', 'second pass failed');
        $this->setEnv('OCR_PSM11_EXIT', '1');

        $service = new TesseractOcrService();

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Echec OCR Tesseract: first pass failed | second pass failed');

        $service->extractText($imagePath);
    }

    public function testExtractTextUsesFallbackLanguageWhenEmpty(): void
    {
        $imagePath = $this->createTempImage();
        $tessdataDir = $this->createTessdataDir();
        $tesseractBin = $this->createFakeTesseractScript();
        $logFile = $this->createTempFile('log');

        $this->setEnv('TESSERACT_BIN', $tesseractBin);
        $this->setEnv('TESSDATA_PREFIX', $tessdataDir);
        $this->setEnv('OCR_PSM6_OUT', 'FIRST_PASS');
        $this->setEnv('OCR_PSM11_OUT', 'SECOND_PASS');
        $this->setEnv('OCR_PSM6_EXIT', '0');
        $this->setEnv('OCR_PSM11_EXIT', '0');
        $this->setEnv('OCR_LOG_FILE', $logFile);

        $service = new TesseractOcrService();
        $service->extractText($imagePath, '  ');

        $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        self::assertIsArray($lines);
        self::assertCount(2, $lines);
        self::assertSame('psm=6;lang=ara+eng', $lines[0]);
        self::assertSame('psm=11;lang=ara', $lines[1]);
    }

    private function createFakeTesseractScript(): string
    {
        $dir = $this->createTempDir();
        if (PHP_OS_FAMILY === 'Windows') {
            $path = $dir . '\\fake_tesseract.cmd';
            $content = "@echo off\r\n".
                "setlocal EnableExtensions EnableDelayedExpansion\r\n".
                "set \"psm=\"\r\n".
                "set \"lang=\"\r\n".
                "set \"prev=\"\r\n".
                ":loop\r\n".
                "if \"%~1\"==\"\" goto done\r\n".
                "if /I \"%prev%\"==\"--psm\" set \"psm=%~1\"\r\n".
                "if /I \"%prev%\"==\"-l\" set \"lang=%~1\"\r\n".
                "set \"prev=%~1\"\r\n".
                "shift\r\n".
                "goto loop\r\n".
                ":done\r\n".
                "if not \"%OCR_LOG_FILE%\"==\"\" (\r\n".
                "  >>\"%OCR_LOG_FILE%\" echo psm=%psm%;lang=%lang%\r\n".
                ")\r\n".
                "set \"exitCode=\"\r\n".
                "set \"out=\"\r\n".
                "set \"err=\"\r\n".
                "if \"%psm%\"==\"6\" (\r\n".
                "  set \"exitCode=%OCR_PSM6_EXIT%\"\r\n".
                "  set \"out=%OCR_PSM6_OUT%\"\r\n".
                "  set \"err=%OCR_PSM6_ERR%\"\r\n".
                ") else if \"%psm%\"==\"11\" (\r\n".
                "  set \"exitCode=%OCR_PSM11_EXIT%\"\r\n".
                "  set \"out=%OCR_PSM11_OUT%\"\r\n".
                "  set \"err=%OCR_PSM11_ERR%\"\r\n".
                ")\r\n".
                "if \"%exitCode%\"==\"\" set \"exitCode=0\"\r\n".
                "if not \"%err%\"==\"\" 1>&2 echo %err%\r\n".
                "if not \"%out%\"==\"\" echo %out%\r\n".
                "exit /b %exitCode%\r\n";
        } else {
            $path = $dir . '/fake_tesseract.sh';
                        $content = <<<'SH'
#!/bin/sh
psm=""
lang=""
prev=""
for arg in "$@"; do
    if [ "$prev" = "--psm" ]; then psm="$arg"; fi
    if [ "$prev" = "-l" ]; then lang="$arg"; fi
    prev="$arg"
done
if [ -n "$OCR_LOG_FILE" ]; then echo "psm=$psm;lang=$lang" >> "$OCR_LOG_FILE"; fi
exitCode="0"
out=""
err=""
if [ "$psm" = "6" ]; then
    exitCode="${OCR_PSM6_EXIT:-0}"
    out="${OCR_PSM6_OUT}"
    err="${OCR_PSM6_ERR}"
elif [ "$psm" = "11" ]; then
    exitCode="${OCR_PSM11_EXIT:-0}"
    out="${OCR_PSM11_OUT}"
    err="${OCR_PSM11_ERR}"
fi
if [ -n "$err" ]; then echo "$err" 1>&2; fi
if [ -n "$out" ]; then echo "$out"; fi
exit "$exitCode"
SH;
        }

        file_put_contents($path, $content);
        $this->tempPaths[] = $path;

        if (PHP_OS_FAMILY !== 'Windows') {
            chmod($path, 0755);
        }

        return $path;
    }

    private function createTempImage(): string
    {
        $path = $this->createTempFile('png');
        $data = base64_decode(self::PNG_BASE64, true);
        if ($data === false) {
            $this->fail('Unable to decode test image.');
        }
        file_put_contents($path, $data);

        return $path;
    }

    private function createTessdataDir(): string
    {
        $baseDir = $this->createTempDir();
        $tessdataDir = $baseDir . DIRECTORY_SEPARATOR . 'tessdata';
        if (!mkdir($tessdataDir, 0777, true) && !is_dir($tessdataDir)) {
            $this->fail('Unable to create tessdata directory.');
        }
        $this->tempPaths[] = $tessdataDir;

        return $tessdataDir;
    }

    private function createTempFile(string $extension = ''): string
    {
        $tmp = tempnam(sys_get_temp_dir(), 'ocr_');
        if ($tmp === false) {
            $this->fail('Unable to create temp file.');
        }

        $path = $tmp;
        if ($extension !== '') {
            $candidate = $tmp . '.' . $extension;
            if (@rename($tmp, $candidate)) {
                $path = $candidate;
            }
        }

        $this->tempPaths[] = $path;

        return $path;
    }

    private function createTempDir(): string
    {
        $dir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'ocr_test_' . uniqid('', true);
        if (!mkdir($dir, 0777, true) && !is_dir($dir)) {
            $this->fail('Unable to create temp directory.');
        }
        $this->tempPaths[] = $dir;

        return $dir;
    }

    private function cleanupTempPaths(): void
    {
        $paths = array_reverse($this->tempPaths);
        $this->tempPaths = [];

        foreach ($paths as $path) {
            $this->removePath($path);
        }
    }

    private function removePath(string $path): void
    {
        if (is_file($path) || is_link($path)) {
            @unlink($path);
            return;
        }

        if (!is_dir($path)) {
            return;
        }

        $entries = scandir($path);
        if ($entries === false) {
            return;
        }

        foreach ($entries as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }

            $this->removePath($path . DIRECTORY_SEPARATOR . $entry);
        }

        @rmdir($path);
    }

    private function setEnv(string $key, ?string $value): void
    {
        if ($value === null) {
            unset($_ENV[$key], $_SERVER[$key]);
            putenv($key);

            return;
        }

        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
        putenv($key . '=' . $value);
    }
}
