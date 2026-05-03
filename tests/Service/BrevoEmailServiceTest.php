<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Service\BrevoEmailService;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

final class BrevoEmailServiceTest extends TestCase
{
    private const ENDPOINT = 'https://api.brevo.com/v3/smtp/email';
    private const API_KEY = 'test_api_key';
    private const FROM_EMAIL = 'noreply@example.com';

    protected function tearDown(): void
    {
        $this->clearEnv('MAILERSEND_API_KEY');
        $this->clearEnv('MAILERSEND_FROM_EMAIL');
        $this->clearEnv('MAILERSEND_FROM_NAME');
    }

    public function testSendVerificationCodeSendsRequestAndLogsSuccess(): void
    {
        $this->setEnv('MAILERSEND_API_KEY', self::API_KEY);
        $this->setEnv('MAILERSEND_FROM_EMAIL', self::FROM_EMAIL);
        $this->setEnv('MAILERSEND_FROM_NAME', 'AgriFlow Test');

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(202);
        $response->method('getContent')->with(false)->willReturn('{"message":"ok"}');

        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient
            ->expects(self::once())
            ->method('request')
            ->with(
                'POST',
                self::ENDPOINT,
                $this->callback(function (array $options): bool {
                    self::assertSame('application/json', $options['headers']['accept']);
                    self::assertSame(self::API_KEY, $options['headers']['api-key']);
                    self::assertSame('application/json', $options['headers']['content-type']);
                    self::assertSame('AgriFlow Test', $options['json']['sender']['name']);
                    self::assertSame(self::FROM_EMAIL, $options['json']['sender']['email']);
                    self::assertSame('user@example.com', $options['json']['to'][0]['email']);
                    self::assertStringContainsString('Inscription', $options['json']['subject']);
                    self::assertStringContainsString('123456', $options['json']['htmlContent']);
                    self::assertStringContainsString('123456', $options['json']['textContent']);
                    self::assertSame(20, $options['timeout']);
                    return true;
                })
            )
            ->willReturn($response);

        $logger = $this->createMock(LoggerInterface::class);
        $logger
            ->expects(self::once())
            ->method('info')
            ->with(
                'Verification email sent successfully',
                ['email' => 'user@example.com', 'context' => 'Inscription']
            );

        $service = new BrevoEmailService($httpClient, $logger);
        $service->sendVerificationCode('user@example.com', '123456', 'Inscription');
    }

    public function testSendVerificationCodeThrowsWhenConfigMissing(): void
    {
        $this->clearEnv('MAILERSEND_API_KEY');
        $this->setEnv('MAILERSEND_FROM_EMAIL', self::FROM_EMAIL);

        $httpClient = $this->createMock(HttpClientInterface::class);
        $logger = $this->createMock(LoggerInterface::class);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Configuration manquante: MAILERSEND_API_KEY');

        $service = new BrevoEmailService($httpClient, $logger);
        $service->sendVerificationCode('user@example.com', '123456', 'Inscription');
    }

    public function testSendVerificationCodeThrowsWhenBrevoRespondsWithError(): void
    {
        $this->setEnv('MAILERSEND_API_KEY', self::API_KEY);
        $this->setEnv('MAILERSEND_FROM_EMAIL', self::FROM_EMAIL);

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(500);
        $response->method('getContent')->with(false)->willReturn('{"error":"boom"}');

        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->method('request')->willReturn($response);

        $logger = $this->createMock(LoggerInterface::class);
        $logger
            ->expects(self::once())
            ->method('error')
            ->with(
                'Unable to send verification email via Brevo',
                $this->callback(function (array $context): bool {
                    self::assertSame('user@example.com', $context['email']);
                    self::assertSame('Inscription', $context['context']);
                    self::assertIsString($context['error']);
                    self::assertNotSame('', $context['error']);
                    return true;
                })
            );

        $service = new BrevoEmailService($httpClient, $logger);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Impossible d\'envoyer le code de verification par email. Veuillez reessayer.');

        $service->sendVerificationCode('user@example.com', '123456', 'Inscription');
    }

    private function setEnv(string $key, string $value): void
    {
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
        putenv($key.'='.$value);
    }

    private function clearEnv(string $key): void
    {
        unset($_ENV[$key], $_SERVER[$key]);
        putenv($key);
    }
}
