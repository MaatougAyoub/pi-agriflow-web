<?php

declare(strict_types=1);

namespace App\Service\Security;

use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class ReCaptchaVerifier
{
    private const VERIFY_ENDPOINT = 'https://www.google.com/recaptcha/api/siteverify';

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        #[Autowire('%env(string:RECAPTCHA_SECRET_KEY)%')]
        private readonly string $secretKey,
        private readonly ?LoggerInterface $logger = null,
    ) {
    }

    public function verify(?string $token, ?string $clientIp = null): bool
    {
        $trimmedToken = trim((string) $token);

        if ($trimmedToken === '' || trim($this->secretKey) === '') {
            return false;
        }

        $body = [
            'secret' => $this->secretKey,
            'response' => $trimmedToken,
        ];
        // remoteip is optional and can cause false negatives in local proxy setups.

        try {
            $response = $this->httpClient->request('POST', self::VERIFY_ENDPOINT, [
                'body' => $body,
            ]);
            $payload = $response->toArray(false);
        } catch (\Throwable) {
            return false;
        }

        if (($payload['success'] ?? false) !== true) {
            $this->logger?->warning('reCAPTCHA verification failed.', [
                'error_codes' => (array) ($payload['error-codes'] ?? []),
                'hostname' => (string) ($payload['hostname'] ?? ''),
                'client_ip' => (string) ($clientIp ?? ''),
            ]);

            return false;
        }

        return true;
    }
}
