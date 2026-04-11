<?php

declare(strict_types=1);

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class BrevoEmailService
{
    private const BREVO_ENDPOINT = 'https://api.brevo.com/v3/smtp/email';

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function sendVerificationCode(string $toEmail, string $code, string $context): void
    {
        $apiKey = $this->requireConfig('MAILERSEND_API_KEY');
        $fromEmail = $this->requireConfig('MAILERSEND_FROM_EMAIL');
        $fromName = $this->getConfigOrDefault('MAILERSEND_FROM_NAME', 'AgriFlow');

        $subject = sprintf('Code de verification AgriFlow - %s', $context);
        $htmlContent = sprintf(
            '<p>Bonjour,</p><p>Voici votre code de verification AgriFlow:</p><p style="font-size:28px;font-weight:bold;letter-spacing:3px;">%s</p><p>Ce code est necessaire pour valider: <strong>%s</strong>.</p><p>Si vous n\'etes pas a l\'origine de cette action, ignorez cet email.</p>',
            htmlspecialchars($code, ENT_QUOTES),
            htmlspecialchars($context, ENT_QUOTES)
        );

        $textContent = sprintf(
            "Bonjour,\n\nVotre code de verification AgriFlow: %s\nAction: %s\n\nSi vous n'etes pas a l'origine de cette action, ignorez cet email.",
            $code,
            $context
        );

        try {
            $response = $this->httpClient->request('POST', self::BREVO_ENDPOINT, [
                'headers' => [
                    'accept' => 'application/json',
                    'api-key' => $apiKey,
                    'content-type' => 'application/json',
                ],
                'json' => [
                    'sender' => [
                        'name' => $fromName,
                        'email' => $fromEmail,
                    ],
                    'to' => [[
                        'email' => $toEmail,
                    ]],
                    'subject' => $subject,
                    'htmlContent' => $htmlContent,
                    'textContent' => $textContent,
                ],
                'timeout' => 20,
            ]);

            $statusCode = $response->getStatusCode();
            $body = $response->getContent(false);

            if ($statusCode < 200 || $statusCode >= 300) {
                throw new \RuntimeException(sprintf('Brevo API error (%d): %s', $statusCode, $body));
            }

            $this->logger->info('Verification email sent successfully', [
                'email' => $toEmail,
                'context' => $context,
            ]);
        } catch (\Throwable $exception) {
            $this->logger->error('Unable to send verification email via Brevo', [
                'email' => $toEmail,
                'context' => $context,
                'error' => $exception->getMessage(),
            ]);

            throw new \RuntimeException('Impossible d\'envoyer le code de verification par email. Veuillez reessayer.');
        }
    }

    private function requireConfig(string $key): string
    {
        $value = $_SERVER[$key] ?? $_ENV[$key] ?? getenv($key) ?: null;
        $value = is_string($value) ? trim($value) : '';

        if ($value === '') {
            throw new \RuntimeException(sprintf(
                'Configuration manquante: %s. Definissez-la en variable d\'environnement.',
                $key
            ));
        }

        return $value;
    }

    private function getConfigOrDefault(string $key, string $defaultValue): string
    {
        $value = $_SERVER[$key] ?? $_ENV[$key] ?? getenv($key) ?: null;
        $value = is_string($value) ? trim($value) : '';

        return $value !== '' ? $value : $defaultValue;
    }
}
