<?php

require_once 'vendor/autoload.php';
use Symfony\Component\Dotenv\Dotenv;

$dotenv = new Dotenv();
$dotenv->loadEnv('.env');

$databaseUrl = $_ENV['DATABASE_URL'] ?? '';
if (preg_match('/mysql:\/\/([^:]*):?([^@]*)@([^:]+):(\d+)\/(.+)/', $databaseUrl, $matches)) {
    $user = $matches[1];
    $pass = $matches[2];
    $dbhost = $matches[3];
    $port = $matches[4];
    $dbname = $matches[5];
    
    try {
        $pdo = new PDO("mysql:host=$dbhost;port=$port;dbname=$dbname", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $stmt = $pdo->query('SELECT id, title, status, requester_id, created_at FROM collab_requests ORDER BY created_at DESC LIMIT 10');
        $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "Demandes dans la base de données:\n";
        foreach ($requests as $req) {
            echo sprintf("ID: %d | Titre: %s | Statut: %s | Créé: %s\n", 
                $req['id'], $req['title'], $req['status'], $req['created_at']);
        }
        
        if (empty($requests)) {
            echo "Aucune demande trouvée dans la base de données.\n";
        }
        
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
} else {
    echo "Could not parse DATABASE_URL\n";
}
