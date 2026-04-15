<?php

require_once 'vendor/autoload.php';
use Symfony\Component\Dotenv\Dotenv;

$dotenv = new Dotenv();
$dotenv->loadEnv('.env');

// Simuler une soumission de formulaire
echo "=== Test de soumission de formulaire ===\n";

// Vérifier si le formulaire a les bons champs
echo "Champs attendus dans le formulaire:\n";
echo "- title (TextType)\n";
echo "- description (TextareaType)\n";
echo "- location (TextType)\n";
echo "- startDate (DateType)\n";
echo "- endDate (DateType)\n";
echo "- neededPeople (IntegerType)\n";
echo "- salary (NumberType)\n";

echo "\n=== Test de validation ===\n";

// Test de validation des types
$testData = [
    'title' => 'Test demande',
    'description' => 'Ceci est une description de test pour valider le formulaire.',
    'location' => 'Tunis',
    'startDate' => '2026-04-15',
    'endDate' => '2026-04-20',
    'neededPeople' => 2,
    'salary' => 50.00
];

echo "Données de test:\n";
foreach ($testData as $key => $value) {
    echo "  $key: $value (type: " . gettype($value) . ")\n";
}

echo "\n=== Vérification des contraintes ===\n";
echo "- title: " . (strlen($testData['title']) >= 3 ? '✓' : '✗') . " (min 3 chars)\n";
echo "- description: " . (strlen($testData['description']) >= 20 ? '✓' : '✗') . " (min 20 chars)\n";
echo "- location: " . (strlen($testData['location']) >= 2 ? '✓' : '✗') . " (min 2 chars)\n";
echo "- neededPeople: " . ($testData['neededPeople'] >= 1 && $testData['neededPeople'] <= 50 ? '✓' : '✗') . " (1-50)\n";
echo "- salary: " . ($testData['salary'] >= 0 && $testData['salary'] <= 99999.99 ? '✓' : '✗') . " (0-99999.99)\n";

echo "\n=== Test de conversion des dates ===\n";
try {
    $startDate = new \DateTime($testData['startDate']);
    $endDate = new \DateTime($testData['endDate']);
    echo "startDate: " . $startDate->format('Y-m-d') . " ✓\n";
    echo "endDate: " . $endDate->format('Y-m-d') . " ✓\n";
    echo "endDate > startDate: " . ($endDate > $startDate ? '✓' : '✗') . "\n";
} catch (Exception $e) {
    echo "Erreur de conversion des dates: " . $e->getMessage() . "\n";
}

echo "\n=== Analyse du problème ===\n";
echo "Si le formulaire ne se soumet pas, vérifier:\n";
echo "1. Le bouton submit a bien type='submit'\n";
echo "2. Le formulaire est dans les balises <form> correctes\n";
echo "3. Il n'y a pas d'erreur JavaScript qui bloque la soumission\n";
echo "4. Les validations Symfony ne bloquent pas\n";
echo "5. L'utilisateur est bien connecté\n";
echo "6. Le CSRF token est valide\n";
