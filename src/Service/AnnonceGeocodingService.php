<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Annonce;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class AnnonceGeocodingService
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly LoggerInterface $logger,
        #[Autowire('%env(string:NOMINATIM_BASE_URL)%')]
        private readonly string $baseUrl,
    ) {
    }

    /**
     * @return array{status: 'matched'|'fallback'|'skipped', message: ?string}
     */
    public function enrichAnnonce(Annonce $annonce): array
    {
        $annonce->clearGeocoding();

        $localisation = trim((string) $annonce->getLocalisation());

        if ('' === $localisation) {
            return ['status' => 'skipped', 'message' => null];
        }

        try {
            $response = $this->httpClient->request('GET', rtrim($this->baseUrl, '/').'/search', [
                'query' => [
                    'q' => $localisation,
                    'format' => 'jsonv2',
                    'limit' => 1,
                    'addressdetails' => 1,
                    'accept-language' => 'fr',
                ],
                'headers' => [
                    'Accept' => 'application/json',
                    'Accept-Language' => 'fr',
                    'User-Agent' => 'AgriFlowMarketplace/1.0 (education-demo)',
                ],
                'timeout' => 8,
            ]);

            $payload = $response->toArray(false);
            $result = is_array($payload) && isset($payload[0]) && is_array($payload[0]) ? $payload[0] : null;

            if (null === $result || !isset($result['lat'], $result['lon'])) {
                return [
                    'status' => 'fallback',
                    'message' => 'Annonce enregistree. La localisation reste manuelle car aucun point precis n a ete trouve.',
                ];
            }

            $annonce
                ->setLatitude((float) $result['lat'])
                ->setLongitude((float) $result['lon'])
                ->setLocalisationNormalisee(substr((string) ($result['display_name'] ?? $localisation), 0, 255));

            return [
                'status' => 'matched',
                'message' => 'Localisation enrichie automatiquement via OpenStreetMap.',
            ];
        } catch (\Throwable $exception) {
            $this->logger->warning('Geocodage marketplace indisponible.', [
                'localisation' => $localisation,
                'error' => $exception->getMessage(),
            ]);

            return [
                'status' => 'fallback',
                'message' => 'Annonce enregistree. Le geocodage OpenStreetMap est temporairement indisponible.',
            ];
        }
    }
}
