<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class ProduitPhytosanitaireAIService
{
    private array $baseProduits;

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly string $groqApiKey = ''
    ) {
        // Base de connaissances locale des produits phytosanitaires courants
        $this->baseProduits = [
            // ===== FONGICIDES =====
            'bouillie bordelaise' => [
                'dosage' => '10 à 20 g/L d\'eau (soit 1 à 2 kg/ha)',
                'frequence' => 'Tous les 10 à 14 jours, renouveler après la pluie',
                'remarques' => 'À base de sulfate de cuivre. Délai avant récolte : 21 jours. Éviter l\'application par temps chaud (>25°C). Port de gants et lunettes recommandé. Ne pas contaminer les cours d\'eau. Produit utilisable en agriculture biologique.',
            ],
            'oidium-fix' => [
                'dosage' => '2,5 à 5 mL/L d\'eau (soit 250 à 500 mL/ha)',
                'frequence' => 'Tous les 10 à 14 jours dès les premiers symptômes',
                'remarques' => 'Fongicide anti-oïdium à base de soufre mouillable. Délai avant récolte : 5 jours. Ne pas appliquer par temps chaud (>28°C) pour éviter les brûlures. Port de masque respiratoire conseillé.',
            ],
            'soufre mouillable' => [
                'dosage' => '3 à 7,5 g/L d\'eau (soit 300 à 750 g/ha)',
                'frequence' => 'Tous les 10 à 14 jours en préventif',
                'remarques' => 'Efficace contre l\'oïdium et certains acariens. Délai avant récolte : 5 jours. Ne pas mélanger avec des huiles ou produits à base de cuivre. Éviter les traitements au-dessus de 28°C. Utilisable en agriculture biologique.',
            ],
            'mancozèbe' => [
                'dosage' => '2 à 3 kg/ha (soit 2 à 3 g/L)',
                'frequence' => 'Tous les 7 à 10 jours en période à risque',
                'remarques' => 'Fongicide de contact à large spectre (mildiou, alternariose). Délai avant récolte : 21 à 28 jours selon la culture. Port d\'EPI complet obligatoire (gants, masque, combinaison). Maximum 6 applications par saison.',
            ],
            'fosetyl-aluminium' => [
                'dosage' => '2,5 à 5 kg/ha selon la culture',
                'frequence' => 'Tous les 14 à 21 jours',
                'remarques' => 'Fongicide systémique contre le mildiou et les Phytophthora. Délai avant récolte : 14 à 28 jours. Stimule les défenses naturelles de la plante. Compatible avec la plupart des autres produits.',
            ],
            'trichoderma' => [
                'dosage' => '1 à 2 kg/ha (ou 1 à 2 g/L en traitement du sol)',
                'frequence' => 'Application unique ou tous les 30 jours',
                'remarques' => 'Agent de biocontrôle (champignon antagoniste). Pas de délai avant récolte. Appliquer au sol ou sur les semences. Stocker au frais (4-8°C). Compatible agriculture biologique. Ne pas mélanger avec des fongicides chimiques.',
            ],
            'cuivre' => [
                'dosage' => '1 à 2 kg/ha de cuivre métal',
                'frequence' => 'Tous les 7 à 14 jours selon la pression maladie',
                'remarques' => 'Fongicide de contact contre mildiou, bactérioses. Délai avant récolte : 14 à 21 jours. Limite annuelle de 4 kg/ha de cuivre métal en bio. Risque de phytotoxicité sur jeunes feuilles. Port de gants obligatoire.',
            ],
            'fongicide bio-x' => [
                'dosage' => '3 à 5 mL/L d\'eau (soit 300 à 500 mL/ha)',
                'frequence' => 'Tous les 7 à 14 jours en préventif',
                'remarques' => 'Fongicide biologique à base d\'extraits végétaux. Pas de délai avant récolte. Appliquer de préférence le matin ou en fin de journée. Compatible avec l\'agriculture biologique. Stocker à l\'abri de la lumière.',
            ],

            // ===== INSECTICIDES =====
            'bacillus thuringiensis' => [
                'dosage' => '0,5 à 1 kg/ha (ou 0,5 à 1 g/L)',
                'frequence' => 'Tous les 7 à 10 jours tant que les larves sont présentes',
                'remarques' => 'Insecticide biologique (Bt) contre les chenilles (piéride, pyrale, tordeuse). Pas de délai avant récolte. Appliquer sur jeunes larves pour une efficacité maximale. Sensible aux UV, traiter en soirée. Utilisable en bio.',
            ],
            'huile de neem' => [
                'dosage' => '3 à 5 mL/L d\'eau (soit 300 à 500 mL/ha)',
                'frequence' => 'Tous les 7 à 14 jours',
                'remarques' => 'Insecticide naturel à base d\'azadirachtine. Efficace contre pucerons, aleurodes, acariens. Délai avant récolte : 3 jours. Agit par ingestion et contact. Ne pas appliquer en plein soleil. Toxique pour les organismes aquatiques.',
            ],
            'pyrèthre naturel' => [
                'dosage' => '1 à 3 mL/L d\'eau',
                'frequence' => 'Tous les 5 à 7 jours si nécessaire',
                'remarques' => 'Insecticide de contact à large spectre. Délai avant récolte : 1 à 3 jours. Action rapide mais non rémanente. Toxique pour les abeilles, ne pas traiter pendant la floraison. Appliquer le soir. Utilisable en bio.',
            ],
            'deltaméthrine' => [
                'dosage' => '0,5 à 1 mL/L d\'eau (soit 50 à 100 mL/ha)',
                'frequence' => 'Maximum 2 à 3 applications par saison, espacées de 14 jours',
                'remarques' => 'Insecticide pyréthrinoïde à large spectre. Délai avant récolte : 3 à 7 jours selon la culture. Très toxique pour les abeilles et les organismes aquatiques. Port d\'EPI complet obligatoire. Ne pas traiter pendant la floraison.',
            ],
            'spinosad' => [
                'dosage' => '0,2 à 0,4 mL/L d\'eau (soit 100 à 200 mL/ha)',
                'frequence' => 'Tous les 7 à 10 jours, maximum 3 applications consécutives',
                'remarques' => 'Insecticide d\'origine naturelle (fermentation). Efficace contre thrips, mouches mineuses, chenilles. Délai avant récolte : 1 à 3 jours. Moins toxique pour les auxiliaires. Utilisable en agriculture biologique.',
            ],
            'savon noir' => [
                'dosage' => '30 à 50 mL/L d\'eau (soit 3 à 5%)',
                'frequence' => 'Tous les 3 à 5 jours jusqu\'à disparition des ravageurs',
                'remarques' => 'Insecticide de contact naturel contre pucerons, cochenilles, aleurodes. Pas de délai avant récolte. Agit par asphyxie. Bien mouiller toute la plante y compris le dessous des feuilles. Compatible agriculture biologique.',
            ],

            // ===== HERBICIDES =====
            'glyphosate' => [
                'dosage' => '3 à 6 L/ha selon la concentration du produit',
                'frequence' => '1 à 2 applications par an maximum',
                'remarques' => 'Herbicide systémique non sélectif. Délai de rentrée : 6 heures. Ne pas appliquer par vent >19 km/h. Interdit à proximité des points d\'eau (5 m minimum). Port d\'EPI obligatoire. Usage réglementé, vérifier les restrictions locales.',
            ],
            'désherbant bio' => [
                'dosage' => '50 à 100 mL/L d\'eau selon la concentration',
                'frequence' => 'Tous les 15 à 30 jours selon la repousse',
                'remarques' => 'Herbicide à base d\'acide pélargonique ou d\'acide acétique. Action de contact uniquement (ne détruit pas les racines). Pas de délai avant récolte. Efficace sur jeunes adventices. Appliquer par temps sec et ensoleillé.',
            ],

            // ===== ENGRAIS / STIMULANTS =====
            'purin d\'ortie' => [
                'dosage' => '100 à 200 mL/L d\'eau en pulvérisation foliaire, pur en arrosage au pied',
                'frequence' => 'Tous les 15 jours en période de croissance',
                'remarques' => 'Stimulant naturel riche en azote et fer. Renforce les défenses de la plante. Effet répulsif contre pucerons. Pas de délai avant récolte. Préparer avec de l\'eau de pluie si possible. Odeur forte lors de la préparation.',
            ],
            'engrais npk' => [
                'dosage' => '200 à 500 kg/ha selon la formulation et la culture',
                'frequence' => '2 à 3 applications par saison (base, croissance, fructification)',
                'remarques' => 'Engrais minéral complet (Azote-Phosphore-Potassium). Adapter la formulation selon le stade de la culture. Ne pas appliquer sur sol sec. Risque de brûlure racinaire en cas de surdosage. Stocker à l\'abri de l\'humidité.',
            ],
        ];
    }

    public function suggereProduit(string $nomProduit): array
    {
        // 1. Chercher dans la base locale (recherche insensible à la casse)
        $nomNormalise = mb_strtolower(trim($nomProduit));

        // Recherche exacte
        if (isset($this->baseProduits[$nomNormalise])) {
            return $this->baseProduits[$nomNormalise];
        }

        // Recherche partielle (le nom saisi contient ou est contenu dans un produit connu)
        foreach ($this->baseProduits as $key => $data) {
            if (str_contains($nomNormalise, $key) || str_contains($key, $nomNormalise)) {
                return $data;
            }
        }

        // Recherche par mots-clés
        $motsCles = explode(' ', $nomNormalise);
        foreach ($this->baseProduits as $key => $data) {
            foreach ($motsCles as $mot) {
                if (strlen($mot) >= 3 && str_contains($key, $mot)) {
                    return $data;
                }
            }
        }

        // 2. Si le produit contient des mots-clés de catégorie, donner une réponse générique
        $categoriesGeneriques = [
            'fongicide' => [
                'dosage' => '2 à 5 mL/L d\'eau (consulter l\'étiquette du produit)',
                'frequence' => 'Tous les 10 à 14 jours en préventif, 7 jours en curatif',
                'remarques' => 'Fongicide - consulter l\'étiquette pour le dosage exact. Délai avant récolte variable selon la substance active. Porter des gants et un masque lors de l\'application. Alterner les familles chimiques pour éviter les résistances.',
            ],
            'insecticide' => [
                'dosage' => '1 à 3 mL/L d\'eau (consulter l\'étiquette du produit)',
                'frequence' => 'Tous les 7 à 14 jours selon la pression des ravageurs',
                'remarques' => 'Insecticide - consulter l\'étiquette pour le dosage exact. Respecter le délai avant récolte indiqué. Ne pas traiter pendant la floraison (protection des pollinisateurs). Port d\'EPI recommandé.',
            ],
            'herbicide' => [
                'dosage' => '3 à 6 L/ha (consulter l\'étiquette du produit)',
                'frequence' => '1 à 2 applications par saison maximum',
                'remarques' => 'Herbicide - consulter l\'étiquette pour le dosage exact. Respecter les zones non traitées (ZNT) près des cours d\'eau. Ne pas appliquer par vent fort. Port d\'EPI obligatoire.',
            ],
            'bio' => [
                'dosage' => '3 à 5 mL/L d\'eau (consulter l\'étiquette du produit)',
                'frequence' => 'Tous les 7 à 14 jours',
                'remarques' => 'Produit compatible agriculture biologique. Généralement pas de délai avant récolte ou délai court. Appliquer de préférence le matin ou en fin de journée. Stocker à l\'abri de la chaleur et de la lumière.',
            ],
            'engrais' => [
                'dosage' => '200 à 500 kg/ha ou 2 à 5 g/L en fertigation',
                'frequence' => 'Tous les 15 à 30 jours en période de croissance',
                'remarques' => 'Adapter la formulation NPK selon le stade de la culture et l\'analyse de sol. Ne pas appliquer sur sol sec. Arroser après application au sol. Risque de brûlure en cas de surdosage.',
            ],
            'acaricide' => [
                'dosage' => '1 à 2 mL/L d\'eau (consulter l\'étiquette)',
                'frequence' => 'Tous les 14 à 21 jours, maximum 2 applications par saison',
                'remarques' => 'Acaricide spécifique contre les acariens (araignées rouges, etc.). Bien couvrir le dessous des feuilles. Alterner les matières actives. Port de gants et masque recommandé.',
            ],
            'nématicide' => [
                'dosage' => '20 à 40 L/ha en traitement du sol',
                'frequence' => '1 application avant plantation',
                'remarques' => 'Nématicide pour le traitement du sol avant culture. Respecter un délai de 2 à 4 semaines avant plantation. Incorporer au sol par irrigation ou travail du sol. Port d\'EPI complet obligatoire.',
            ],
        ];

        foreach ($categoriesGeneriques as $categorie => $data) {
            if (str_contains($nomNormalise, $categorie)) {
                $data['remarques'] = 'Produit "' . $nomProduit . '" : ' . $data['remarques'];
                return $data;
            }
        }

        // 3. Tenter l'API Groq si la clé est configurée
        if (!empty($this->groqApiKey)) {
            try {
                return $this->appelApiGroq($nomProduit);
            } catch (\Throwable $e) {
                // Si l'API échoue, on continue vers la réponse par défaut
            }
        }

        // 4. Réponse par défaut si rien ne correspond
        return [
            'dosage' => 'Consulter l\'étiquette du produit "' . $nomProduit . '" pour le dosage exact',
            'frequence' => 'Selon les recommandations du fabricant (généralement tous les 7 à 14 jours)',
            'remarques' => 'Produit "' . $nomProduit . '" : veuillez consulter l\'étiquette officielle pour les dosages précis, le délai avant récolte et les précautions d\'emploi. Porter toujours des équipements de protection individuelle (gants, masque, lunettes) lors de la manipulation et l\'application. Respecter les zones non traitées à proximité des points d\'eau.',
        ];
    }

    private function appelApiGroq(string $nomProduit): array
    {
        $prompt = "Tu es un expert agronome spécialisé en produits phytosanitaires. "
            . "Pour le produit phytosanitaire nommé \"{$nomProduit}\", génère des informations techniques précises. "
            . "Réponds UNIQUEMENT en JSON valide avec exactement ces clés : "
            . "{\"dosage\": \"dosage recommandé avec unité (ex: 500 ml/ha)\", "
            . "\"frequence\": \"fréquence d'application (ex: Tous les 14 jours)\", "
            . "\"remarques\": \"précautions d emploi, délai avant récolte, équipement de protection (2-3 phrases)\"}";

        $response = $this->httpClient->request('POST', 'https://api.groq.com/openai/v1/chat/completions', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->groqApiKey,
                'Content-Type'  => 'application/json',
            ],
            'json' => [
                'model'       => 'llama3-8b-8192',
                'messages'    => [
                    [
                        'role'    => 'system',
                        'content' => 'Tu es un expert agronome. Tu réponds toujours uniquement en JSON valide, sans texte avant ou après.'
                    ],
                    [
                        'role'    => 'user',
                        'content' => $prompt
                    ],
                ],
                'temperature' => 0.3,
                'max_tokens'  => 400,
            ],
            'timeout' => 15,
        ]);

        $statusCode = $response->getStatusCode();
        $content    = $response->getContent(false);

        if ($statusCode !== 200) {
            throw new \RuntimeException('Erreur API Groq (code ' . $statusCode . ')');
        }

        $data = json_decode($content, true);
        if (!isset($data['choices'][0]['message']['content'])) {
            throw new \RuntimeException('Réponse Groq invalide.');
        }

        $text = trim($data['choices'][0]['message']['content']);
        $text = preg_replace('/```json\s*/i', '', $text);
        $text = preg_replace('/```\s*/i', '', $text);
        $text = trim($text);

        if (preg_match('/\{[^{}]*\}/', $text, $matches)) {
            $text = $matches[0];
        }

        $decoded = json_decode($text, true);
        if (!is_array($decoded)) {
            throw new \RuntimeException('JSON invalide.');
        }

        return [
            'dosage'    => $decoded['dosage']    ?? '',
            'frequence' => $decoded['frequence'] ?? '',
            'remarques' => $decoded['remarques'] ?? '',
        ];
    }
}
