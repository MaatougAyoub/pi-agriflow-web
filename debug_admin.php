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
        
        // Test de la requête utilisée dans AdminCollaborationController
        echo "=== Test de la requête AdminCollaborationController ===\n";
        
        // Sans filtre
        $stmt = $pdo->query('SELECT id, title, status, requester_id, created_at FROM collab_requests ORDER BY created_at DESC');
        $allRequests = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "Toutes les demandes (" . count($allRequests) . "):\n";
        foreach ($allRequests as $req) {
            echo sprintf("  ID: %d | Titre: %s | Statut: %s\n", 
                $req['id'], substr($req['title'], 0, 30), $req['status']);
        }
        
        // Avec filtre PENDING
        $stmt = $pdo->prepare('SELECT id, title, status FROM collab_requests WHERE status = :status ORDER BY created_at DESC');
        $stmt->execute(['status' => 'PENDING']);
        $pendingRequests = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "\nDemandes PENDING (" . count($pendingRequests) . "):\n";
        foreach ($pendingRequests as $req) {
            echo sprintf("  ID: %d | Titre: %s\n", 
                $req['id'], substr($req['title'], 0, 30));
        }
        
        // Avec filtre APPROVED
        $stmt->execute(['status' => 'APPROVED']);
        $approvedRequests = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "\nDemandes APPROVED (" . count($approvedRequests) . "):\n";
        foreach ($approvedRequests as $req) {
            echo sprintf("  ID: %d | Titre: %s\n", 
                $req['id'], substr($req['title'], 0, 30));
        }
        
        // Test de la recherche
        echo "\n=== Test de recherche ===\n";
        $stmt = $pdo->prepare('SELECT id, title, status FROM collab_requests WHERE title LIKE :q OR publisher LIKE :q OR location LIKE :q ORDER BY created_at DESC');
        $stmt->execute(['q' => '%olive%']);
        $searchResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "Résultats recherche 'olive' (" . count($searchResults) . "):\n";
        foreach ($searchResults as $req) {
            echo sprintf("  ID: %d | Titre: %s | Statut: %s\n", 
                $req['id'], substr($req['title'], 0, 30), $req['status']);
        }
        
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
} else {
    echo "Could not parse DATABASE_URL\n";
}
