<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Service IA multi-provider pour la modération et l'amélioration de contenu.
 *
 * Architecture hybride supportant 3 fournisseurs d'IA :
 *   - OpenAI (gpt-4o-mini) — recommandé, fiable
 *   - Google Gemini (gemini-2.0-flash) — tier gratuit
 *   - Groq (llama-3.1-8b-instant) — tier gratuit, très rapide
 *
 * Fallback local automatique si aucun provider n'est disponible.
 */
class GeminiAIService
{
    /** Endpoints API */
    private const GEMINI_URL = 'https://generativelanguage.googleapis.com/v1beta/models/%s:generateContent';
    private const OPENAI_URL = 'https://api.openai.com/v1/chat/completions';
    private const GROQ_URL   = 'https://api.groq.com/openai/v1/chat/completions';

    /** Mots-clés hors-sujet (non agricoles) */
    private const OFF_TOPIC_PATTERNS = [
        'crypto', 'cryptomonnaie', 'cryptomonnaies', 'bitcoin', 'ethereum',
        'iphone', 'samsung', 'console', 'playstation', 'xbox',
        'forex', 'casino', 'paris sportif', 'pari en ligne',
        'téléphone', 'ordinateur', 'laptop', 'tablette',
    ];

    /** Mots-clés de spam / contenu inapproprié */
    private const SPAM_INAPPROPRIATE_PATTERNS = [
        'arnaque', 'escroquerie', 'fraude', 'spam',
        'gagnez', 'argent facile', 'revenu rapide', 'devenir riche',
        'violence', 'haine', 'vulgaire', 'porn', 'sexe',
    ];

    /** Templates d'amélioration de description (fallback local) */
    private const IMPROVEMENT_TEMPLATES = [
        "🌱 **%s** — %s\n\nNous recherchons des collaborateurs motivés et expérimentés pour cette mission agricole. Conditions de travail optimales garanties. Rejoignez notre équipe AgriFlow !",
        "📋 **Offre : %s**\n\n%s\n\n✅ Environnement professionnel\n✅ Rémunération compétitive\n✅ Équipe dynamique\n\nPostulez dès maintenant sur AgriFlow.",
        "🚜 **%s**\n\n%s\n\nCette opportunité s'adresse aux professionnels du secteur agricole souhaitant mettre à profit leur expertise. N'hésitez pas à postuler !",
    ];

    private string $provider;    // 'openai', 'gemini', 'groq', ou 'local'
    private string $apiKey;
    private string $model;

    public function __construct(
        private HttpClientInterface $httpClient,
        private LoggerInterface $logger,
        private string $aiProvider,
        private string $geminiApiKey,
        private string $geminiModel,
        private string $openaiApiKey,
        private string $openaiModel,
        private string $groqApiKey,
        private string $groqModel
    ) {
        // Déterminer le provider actif en fonction de la configuration
        $this->resolveProvider();
    }

    /**
     * Sélectionne automatiquement le meilleur provider disponible.
     * Ordre de priorité : aiProvider configuré > OpenAI > Groq > Gemini > local
     */
    private function resolveProvider(): void
    {
        $preferred = strtolower(trim($this->aiProvider));

        // Essayer le provider préféré d'abord
        if ($preferred === 'openai' && $this->isValidKey($this->openaiApiKey, 'sk-')) {
            $this->provider = 'openai';
            $this->apiKey = $this->openaiApiKey;
            $this->model = !empty($this->openaiModel) ? $this->openaiModel : 'gpt-4o-mini';
            return;
        }

        if ($preferred === 'groq' && $this->isValidKey($this->groqApiKey, 'gsk_')) {
            $this->provider = 'groq';
            $this->apiKey = $this->groqApiKey;
            $this->model = !empty($this->groqModel) ? $this->groqModel : 'llama-3.1-8b-instant';
            return;
        }

        if ($preferred === 'gemini' && $this->isValidKey($this->geminiApiKey, 'AIza')) {
            $this->provider = 'gemini';
            $this->apiKey = $this->geminiApiKey;
            $this->model = !empty($this->geminiModel) ? $this->geminiModel : 'gemini-2.0-flash';
            return;
        }

        // Fallback : essayer tous les providers dans l'ordre
        if ($this->isValidKey($this->openaiApiKey, 'sk-')) {
            $this->provider = 'openai';
            $this->apiKey = $this->openaiApiKey;
            $this->model = !empty($this->openaiModel) ? $this->openaiModel : 'gpt-4o-mini';
            return;
        }

        if ($this->isValidKey($this->groqApiKey, 'gsk_')) {
            $this->provider = 'groq';
            $this->apiKey = $this->groqApiKey;
            $this->model = !empty($this->groqModel) ? $this->groqModel : 'llama-3.1-8b-instant';
            return;
        }

        if ($this->isValidKey($this->geminiApiKey, 'AIza')) {
            $this->provider = 'gemini';
            $this->apiKey = $this->geminiApiKey;
            $this->model = !empty($this->geminiModel) ? $this->geminiModel : 'gemini-2.0-flash';
            return;
        }

        // Aucun provider cloud disponible : mode local
        $this->provider = 'local';
        $this->apiKey = '';
        $this->model = 'local-nlp';
        $this->logger->info('GeminiAIService : aucun provider cloud configuré, mode local activé.');
    }

    private function isValidKey(string $key, string $expectedPrefix): bool
    {
        return !empty($key) && str_starts_with($key, $expectedPrefix);
    }

    // ──────────────────────────────────────────────────────────────
    //  FONCTIONNALITÉS IA PUBLIQUES
    // ──────────────────────────────────────────────────────────────

    /**
     * IA 1 : Améliore la description d'une demande pour la rendre plus professionnelle.
     */
    public function improveDescription(string $title, string $description): string
    {
        $prompt = sprintf(
            "Tu es un expert en rédaction pour AgriFlow, un marketplace agricole en Tunisie. " .
            "Réécris et améliore cette description pour la rendre plus professionnelle et attractive. " .
            "Garde le même sens. Réponds UNIQUEMENT avec la nouvelle description.\n\n" .
            "Titre : %s\nDescription originale : %s",
            $title,
            $description
        );

        $result = $this->callAI($prompt, '');
        if ($result !== '') {
            return $result;
        }

        // Fallback local
        $this->logger->info('IA Amélioration : fallback local activé.', ['title' => $title]);
        return $this->localImproveDescription($title, $description);
    }

    /**
     * IA 2 : Analyse si un candidat est un bon fit pour une demande.
     */
    public function analyzeCandidateFit(string $requestDesc, string $motivation): string
    {
        $prompt = sprintf(
            "En tant qu'expert RH agricole, analyse la motivation d'un candidat par rapport à une offre.\n" .
            "Offre : %s\nMotivation du candidat : %s\n\n" .
            "Donne un résumé très court (2 lignes max) expliquant pourquoi ce candidat est pertinent ou non.",
            $requestDesc,
            $motivation
        );

        $result = $this->callAI($prompt, '');
        if ($result !== '') {
            return $result;
        }

        // Fallback local
        $this->logger->info('IA Analyse : fallback local activé.');
        return $this->localAnalyzeCandidateFit($requestDesc, $motivation);
    }

    /**
     * Suggère un salaire réaliste pour une demande.
     */
    public function suggestSalary(string $title, string $description, string $location): float
    {
        $prompt = sprintf(
            "En tant qu'expert du marché agricole en Tunisie, suggère un salaire journalier réaliste en DT pour cette offre.\n" .
            "Réponds UNIQUEMENT par le chiffre (ex: 60.5).\n\n" .
            "Titre : %s\nDescription : %s\nLocalisation : %s",
            $title,
            $description,
            $location
        );

        $response = $this->callAI($prompt, '');
        if ($response !== '') {
            return (float) preg_replace('/[^0-9.]/', '', $response);
        }

        return 45.0;
    }

    /**
     * Modération Préventive du contenu.
     * Retourne null si OK, sinon retourne le motif de rejet.
     */
    public function moderateContent(string $title, string $description): ?string
    {
        // Étape 1 : Filtre local (toujours actif)
        $localReason = $this->fallbackModerationReason($title, $description);
        if ($localReason !== null) {
            return $localReason;
        }

        // Étape 2 : Analyse sémantique via IA (si disponible)
        if ($this->provider !== 'local') {
            $prompt = sprintf(
                "Analyse cette annonce agricole. REJETER si : produit illégal, arnaque, vulgaire, ou sans rapport avec l'agriculture.\n" .
                "Si acceptable, réponds exactement : OK. Sinon, réponds : REJET: [motif court].\n\n" .
                "Titre : %s\nDescription : %s",
                $title,
                $description
            );

            $response = trim($this->callAI($prompt, ''));
            if ($response !== '') {
                if (preg_match('/^\s*OK\b/iu', $response) === 1) {
                    return null;
                }
                if (preg_match('/REJET\s*:?\s*(.*)$/iu', $response, $matches) === 1) {
                    $reason = trim($matches[1] ?? '');
                    return $reason !== '' ? $reason : 'Contenu non conforme à la politique de publication.';
                }
                return 'Contenu potentiellement non conforme détecté par la modération IA.';
            }
        }

        return null;
    }

    /**
     * Retourne le nom du provider actif.
     */
    public function getActiveProvider(): string
    {
        return $this->provider;
    }

    /**
     * Retourne le modèle actif.
     */
    public function getActiveModel(): string
    {
        return $this->model;
    }

    /**
     * Vérifie si un provider cloud est actif.
     */
    public function isApiAvailable(): bool
    {
        return $this->provider !== 'local';
    }

    // ──────────────────────────────────────────────────────────────
    //  APPEL API UNIFIÉ (OpenAI / Groq / Gemini)
    // ──────────────────────────────────────────────────────────────

    private function callAI(string $prompt, string $default = 'OK'): string
    {
        if ($this->provider === 'local') {
            return $default;
        }

        try {
            if ($this->provider === 'gemini') {
                return $this->callGemini($prompt, $default);
            }

            // OpenAI et Groq utilisent le même format d'API (OpenAI-compatible)
            return $this->callOpenAICompatible($prompt, $default);
        } catch (\Throwable $e) {
            $this->logger->warning('Appel IA indisponible.', [
                'provider' => $this->provider,
                'model' => $this->model,
                'error' => $e->getMessage(),
            ]);
            return $default;
        }
    }

    /**
     * Appel API format OpenAI (fonctionne pour OpenAI et Groq).
     */
    private function callOpenAICompatible(string $prompt, string $default): string
    {
        $url = ($this->provider === 'groq') ? self::GROQ_URL : self::OPENAI_URL;

        $response = $this->httpClient->request('POST', $url, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->apiKey,
            ],
            'json' => [
                'model' => $this->model,
                'messages' => [
                    ['role' => 'system', 'content' => 'Tu es un assistant expert en agriculture tunisienne pour la plateforme AgriFlow.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'temperature' => 0.4,
                'max_tokens' => 500,
            ],
            'timeout' => 15,
        ]);

        $statusCode = $response->getStatusCode();
        $data = $response->toArray(false);

        if ($statusCode !== 200) {
            $this->logger->warning('API IA a retourné un statut inattendu.', [
                'provider' => $this->provider,
                'status_code' => $statusCode,
                'model' => $this->model,
                'error' => $data['error']['message'] ?? 'unknown',
            ]);
            return $default;
        }

        $text = trim($data['choices'][0]['message']['content'] ?? '');
        if ($text === '') {
            $this->logger->warning('API IA a retourné une réponse vide.', [
                'provider' => $this->provider,
                'model' => $this->model,
            ]);
            return $default;
        }

        return $text;
    }

    /**
     * Appel API Google Gemini.
     */
    private function callGemini(string $prompt, string $default): string
    {
        $response = $this->httpClient->request('POST', sprintf(
            self::GEMINI_URL,
            rawurlencode($this->model)
        ) . '?key=' . rawurlencode($this->apiKey), [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'contents' => [['parts' => [['text' => $prompt]]]],
                'generationConfig' => ['temperature' => 0.4],
            ],
            'timeout' => 15,
        ]);

        $statusCode = $response->getStatusCode();
        $data = $response->toArray(false);

        if ($statusCode !== 200) {
            $this->logger->warning('Gemini API a retourné un statut inattendu.', [
                'status_code' => $statusCode,
                'model' => $this->model,
                'error' => $data['error']['message'] ?? 'unknown',
            ]);
            return $default;
        }

        $parts = $data['candidates'][0]['content']['parts'] ?? null;
        if (!is_array($parts)) {
            return $default;
        }

        $texts = [];
        foreach ($parts as $part) {
            if (is_array($part) && isset($part['text']) && is_string($part['text'])) {
                $texts[] = trim($part['text']);
            }
        }

        $text = trim(implode("\n", array_filter($texts)));
        return $text !== '' ? $text : $default;
    }

    // ──────────────────────────────────────────────────────────────
    //  FALLBACK LOCAL : Algorithmes NLP de base
    // ──────────────────────────────────────────────────────────────

    private function localImproveDescription(string $title, string $description): string
    {
        $cleanDesc = trim($description);
        $cleanDesc = preg_replace('/\s+/', ' ', $cleanDesc);
        $cleanDesc = mb_strtoupper(mb_substr($cleanDesc, 0, 1)) . mb_substr($cleanDesc, 1);

        if (!str_ends_with($cleanDesc, '.') && !str_ends_with($cleanDesc, '!') && !str_ends_with($cleanDesc, '?')) {
            $cleanDesc .= '.';
        }

        $templateIndex = abs(crc32($title)) % count(self::IMPROVEMENT_TEMPLATES);
        return sprintf(self::IMPROVEMENT_TEMPLATES[$templateIndex], trim($title), $cleanDesc);
    }

    private function localAnalyzeCandidateFit(string $requestDesc, string $motivation): string
    {
        $requestWords = $this->extractKeywords($requestDesc);
        $motivationWords = $this->extractKeywords($motivation);

        $commonWords = array_intersect($requestWords, $motivationWords);
        $matchCount = count($commonWords);
        $motivationLength = mb_strlen(trim($motivation));

        $effortLevel = match (true) {
            $motivationLength > 200 => 'détaillée',
            $motivationLength > 80  => 'correcte',
            default                 => 'courte',
        };

        if ($matchCount >= 3) {
            $words = implode(', ', array_slice($commonWords, 0, 3));
            return sprintf(
                "✅ Bonne adéquation. Le candidat mentionne des mots-clés pertinents (%s). Motivation %s (%d caractères). Profil à considérer.",
                $words, $effortLevel, $motivationLength
            );
        } elseif ($matchCount >= 1) {
            return sprintf(
                "⚠️ Adéquation partielle. Quelques points communs trouvés, mais la motivation est %s. Le candidat pourrait convenir avec des précisions supplémentaires.",
                $effortLevel
            );
        } else {
            return sprintf(
                "❌ Faible adéquation. Peu de correspondance entre la description de l'offre et la motivation du candidat (motivation %s, %d caractères).",
                $effortLevel, $motivationLength
            );
        }
    }

    private function extractKeywords(string $text): array
    {
        $stopWords = ['le', 'la', 'les', 'de', 'du', 'des', 'un', 'une', 'et', 'en', 'est', 'je', 'suis',
            'pour', 'dans', 'que', 'qui', 'sur', 'avec', 'par', 'pas', 'plus', 'ce', 'se', 'son', 'sa',
            'au', 'aux', 'il', 'elle', 'on', 'nous', 'vous', 'ils', 'mon', 'ma', 'mes', 'ton', 'ta',
            'aussi', 'très', 'bien', 'fait', 'être', 'avoir', 'été', 'fait', 'faire', 'comme', 'mais',
            'ou', 'car', 'cette', 'ces', 'tout', 'tous', 'toute', 'toutes', 'ça', 'cela',
        ];

        $words = preg_split('/[\s\p{P}]+/u', mb_strtolower(trim($text)));
        $words = array_filter($words, fn(string $w) => mb_strlen($w) > 2 && !in_array($w, $stopWords, true));

        return array_values(array_unique($words));
    }

    private function fallbackModerationReason(string $title, string $description): ?string
    {
        $text = mb_strtolower(trim($title . ' ' . $description));

        foreach (self::OFF_TOPIC_PATTERNS as $pattern) {
            if (str_contains($text, $pattern)) {
                return sprintf('Hors sujet pour AgriFlow : contenu détecté "%s".', $pattern);
            }
        }

        foreach (self::SPAM_INAPPROPRIATE_PATTERNS as $pattern) {
            if (str_contains($text, $pattern)) {
                return sprintf('Contenu suspect ou inapproprié détecté : "%s".', $pattern);
            }
        }

        return null;
    }
}
