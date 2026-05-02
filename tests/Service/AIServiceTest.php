<?php

namespace App\Tests\Service;

use App\Service\AIService;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class AIServiceTest extends TestCase
{
    /**
     * Crée un mock HTTP qui retourne une réponse simulée de Groq.
     */
    private function createHttpMock(
        int    $statusCode = 200,
        string $content    = ''
    ): HttpClientInterface {
        if ($content === '') {
            $content = json_encode([
                'choices' => [[
                    'message' => [
                        'content' => 'Analyse IA : feuilles jaunissantes détectées sur la culture.'
                    ]
                ]]
            ]);
        }

        $responseMock = $this->createMock(ResponseInterface::class);
        $responseMock->method('getStatusCode')->willReturn($statusCode);
        $responseMock->method('getContent')->willReturn($content);

        $httpMock = $this->createMock(HttpClientInterface::class);
        $httpMock->method('request')->willReturn($responseMock);

        return $httpMock;
    }

    // ----------------------------------------------------------------
    // Tests sur analyserImage()
    // ----------------------------------------------------------------

    /**
     * Règle 1 : Une réponse 200 valide retourne le texte de l'IA.
     */
    public function testReponseValideRetourneLeTexteIA(): void
    {
        $_ENV['GROQ_API_KEY1'] = 'gsk_fake_key_for_test';

        $service  = new AIService($this->createHttpMock(200));
        $resultat = $service->analyserImage(base64_encode('fake_image_data'), 'image/jpeg');

        $this->assertIsString($resultat);
        $this->assertNotEmpty($resultat);
        $this->assertStringContainsString('feuilles jaunissantes', $resultat);
    }

    /**
     * Règle 2 : Sans clé API, une RuntimeException doit être levée.
     */
    public function testSansCleAPILeveUneException(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Clé API GROQ manquante');

        // Supprimer la clé API
        unset($_ENV['GROQ_API_KEY1']);
        putenv('GROQ_API_KEY1');

        $service = new AIService($this->createHttpMock());
        $service->analyserImage(base64_encode('fake_image'), 'image/jpeg');
    }

    /**
     * Règle 3 : Une réponse HTTP 401 (non autorisé) lève une RuntimeException.
     */
    public function testErreur401LeveUneException(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Erreur API Groq (401)');

        $_ENV['GROQ_API_KEY1'] = 'gsk_invalid_key';

        $contenuErreur = json_encode([
            'error' => ['message' => 'Invalid API key provided.']
        ]);

        $service = new AIService($this->createHttpMock(401, $contenuErreur));
        $service->analyserImage(base64_encode('fake_image'), 'image/jpeg');
    }

    /**
     * Règle 4 : Une réponse HTTP 500 (erreur serveur) lève une RuntimeException.
     */
    public function testErreur500LeveUneException(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Erreur API Groq (500)');

        $_ENV['GROQ_API_KEY1'] = 'gsk_fake_key';

        $contenuErreur = json_encode([
            'error' => ['message' => 'Internal server error.']
        ]);

        $service = new AIService($this->createHttpMock(500, $contenuErreur));
        $service->analyserImage(base64_encode('fake_image'), 'image/png');
    }

    /**
     * Règle 5 : Une réponse JSON invalide lève une RuntimeException.
     */
    public function testJsonInvalideLeveUneException(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Réponse API invalide');

        $_ENV['GROQ_API_KEY1'] = 'gsk_fake_key';

        $service = new AIService($this->createHttpMock(200, 'NOT_VALID_JSON{{'));
        $service->analyserImage(base64_encode('fake_image'), 'image/jpeg');
    }

    /**
     * Règle 6 : Le type MIME est correctement inclus dans la requête.
     */
    public function testDifferentsMimeTypesSontAcceptes(): void
    {
        $_ENV['GROQ_API_KEY1'] = 'gsk_fake_key';

        foreach (['image/jpeg', 'image/png', 'image/webp'] as $mime) {
            $service  = new AIService($this->createHttpMock(200));
            $resultat = $service->analyserImage(base64_encode('fake_image'), $mime);

            $this->assertIsString($resultat, "Échec pour MIME type : $mime");
            $this->assertNotEmpty($resultat);
        }
    }

    /**
     * Règle 7 : Si l'IA retourne un contenu vide, retourner le message par défaut.
     */
    public function testContenusVideRetourneMessageParDefaut(): void
    {
        $_ENV['GROQ_API_KEY1'] = 'gsk_fake_key';

        $contenuVide = json_encode(['choices' => [[
            'message' => ['content' => null]
        ]]]);

        $service  = new AIService($this->createHttpMock(200, $contenuVide));
        $resultat = $service->analyserImage(base64_encode('fake_image'), 'image/jpeg');

        $this->assertEquals("Aucune réponse de l'IA.", $resultat);
    }
}