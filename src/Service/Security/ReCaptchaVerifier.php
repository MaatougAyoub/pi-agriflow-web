<?php

declare(strict_types=1);

namespace App\Service\Security;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class ReCaptchaVerifier
{
    private const VERIFY_ENDPOINT = 'https://www.google.com/recaptcha/api/siteverify';

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        #[Autowire('%env(string:RECAPTCHA_SECRET_KEY)%')]
        private readonly string $secretKey,
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

        if (null !== $clientIp && trim($clientIp) !== '') {
            $body['remoteip'] = $clientIp;
        }

        try {
            $response = $this->httpClient->request('POST', self::VERIFY_ENDPOINT, [
                'body' => $body,
            ]);
            $payload = $response->toArray(false);
        } catch (\Throwable) {
            return false;
        }

        if (($payload['success'] ?? false) !== true) {
            return false;
        }

        return true;
    }
}
