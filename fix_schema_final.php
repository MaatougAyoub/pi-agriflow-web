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
        
        echo "Fixing foreign key constraints...\n";
        
        // Fix cultures table
        $sql = "ALTER TABLE cultures DROP FOREIGN KEY IF EXISTS fk_cultures_acheteur";
        try {
            $pdo->exec($sql);
            echo "Dropped fk_cultures_acheteur foreign key\n";
        } catch (PDOException $e) {
            echo "Could not drop fk_cultures_acheteur: " . $e->getMessage() . "\n";
        }
        
        // Fix diagnosti table
        $sql = "ALTER TABLE diagnosti DROP FOREIGN KEY IF EXISTS diag";
        try {
            $pdo->exec($sql);
            echo "Dropped diag foreign key\n";
        } catch (PDOException $e) {
            echo "Could not drop diag: " . $e->getMessage() . "\n";
        }
        
        // Drop problematic indexes
        $indexes = [
            'fk_cultures_acheteur' => 'cultures',
            'diag' => 'diagnosti',
            'idx_expediteur' => 'messages',
            'idx_destinataire' => 'messages',
            'fk_message_annonce' => 'messages',
            'idx_lu' => 'messages',
            'fk_message_reservation' => 'messages',
            'id_culture' => 'plans_irrigation',
            'unique_plan_jour_date' => 'plans_irrigation_jour',
            'fk_reclamations_utilisateur' => 'reclamations',
            'fk_reservation_annonce' => 'reservations'
        ];
        
        foreach ($indexes as $index => $table) {
            $sql = "DROP INDEX IF EXISTS $index ON $table";
            try {
                $pdo->exec($sql);
                echo "Dropped index $index from $table\n";
            } catch (PDOException $e) {
                echo "Could not drop index $index: " . $e->getMessage() . "\n";
            }
        }
        
        echo "Cleaning orphaned reclamations...\n";
        
        // Clean orphaned reclamations
        $sql = "DELETE r FROM reclamations r 
                LEFT JOIN utilisateurs u ON r.utilisateur_id = u.id 
                WHERE u.id IS NULL";
        $result = $pdo->exec($sql);
        echo "Cleaned $result orphaned reclamation records\n";
        
        echo "Running schema update...\n";
        
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
} else {
    echo "Could not parse DATABASE_URL\n";
}
