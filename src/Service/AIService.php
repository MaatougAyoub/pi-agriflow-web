<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class AIService
{
    private const API_URL = 'https://api.groq.com/openai/v1/chat/completions';
    
    // Modèle vision qui fonctionne sur Groq (testé dans le playground)
    private const MODEL = 'meta-llama/llama-4-scout-17b-16e-instruct';

    public function __construct(private HttpClientInterface $http) {}

    /**
     * Analyse une image via l'API Groq avec un modèle vision
     * 
     * @param string $base64Image Image encodée en base64
     * @param string $mimeType Type MIME de l'image (image/jpeg, image/png, etc.)
     * @return string Description générée par l'IA
     * @throws \RuntimeException En cas d'erreur
     */
    public function analyserImage(string $base64Image, string $mimeType = 'image/jpeg'): string
    {
        $apiKey = $_ENV['GROQ_API_KEY1'] ?? getenv('GROQ_API_KEY1');

        if (!$apiKey) {
            throw new \RuntimeException('Clé API GROQ manquante dans .env. Ajoutez GROQ_API_KEY=gsk_votre_clé');
        }

        $dataUrl = "data:{$mimeType};base64,{$base64Image}";

        try {
            $response = $this->http->request('POST', self::API_URL, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type'  => 'application/json',
                ],
                'json' => [
                    'model'    => self::MODEL,
                    'messages' => [[
                        'role'    => 'user',
                        'content' => [
                            [
                                'type' => 'text',
                                'text' => "Agis en tant que système AGRIFLOW. "
                                    . "Rédige une demande de réclamation agricole claire "
                                    . "basée sur cette image. "
                                    . "Décris le problème observé sur la culture ou le fruit, "
                                    . "destinée à un expert agricole, en français. "
                                    . "Sois précis et concis (3-4 phrases maximum).",
                            ],
                            [
                                'type'      => 'image_url',
                                'image_url' => ['url' => $dataUrl],
                            ],
                        ],
                    ]],
                    'max_tokens'  => 500,
                    'temperature' => 0.3,
                ],
                'timeout' => 60,
            ]);

            $statusCode = $response->getStatusCode();
            $content = $response->getContent(false);

            if ($statusCode !== 200) {
                $errorData = json_decode($content, true);
                $errorMsg = $errorData['error']['message'] ?? substr($content, 0, 300);
                throw new \RuntimeException("Erreur API Groq ($statusCode) : $errorMsg");
            }

            $data = json_decode($content, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \RuntimeException('Réponse API invalide (pas du JSON valide).');
            }

            return $data['choices'][0]['message']['content'] ?? 'Aucune réponse de l\'IA.';

        } catch (\Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface $e) {
            throw new \RuntimeException('Erreur de connexion à Groq : ' . $e->getMessage());
        } catch (\RuntimeException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new \RuntimeException('Erreur IA inattendue : ' . $e->getMessage());
        }
    }
}
