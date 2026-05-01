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
        
        echo "Creating collab_requests table...\n";
        
        // Create collab_requests table
        $sql = "CREATE TABLE IF NOT EXISTS collab_requests (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            description TEXT NOT NULL,
            requester_id INT NOT NULL,
            location VARCHAR(255) NULL,
            start_date DATE NULL,
            end_date DATE NULL,
            status VARCHAR(50) DEFAULT 'PENDING',
            created_at DATETIME NULL,
            updated_at DATETIME NULL,
            INDEX IDX_10CC4FA4ED442CF4 (requester_id),
            CONSTRAINT FK_10CC4FA4ED442CF4 FOREIGN KEY (requester_id) REFERENCES utilisateurs (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        
        $pdo->exec($sql);
        echo "Table collab_requests created successfully\n";
        
        echo "Creating collab_applications table...\n";
        
        // Create collab_applications table
        $sql = "CREATE TABLE IF NOT EXISTS collab_applications (
            id INT AUTO_INCREMENT PRIMARY KEY,
            request_id INT NOT NULL,
            candidate_id INT NOT NULL,
            full_name VARCHAR(255) NOT NULL,
            phone VARCHAR(20) NOT NULL,
            email VARCHAR(100) NOT NULL,
            years_of_experience INT DEFAULT 0,
            motivation TEXT NOT NULL,
            expected_salary DECIMAL(10,2) DEFAULT 0.00,
            status VARCHAR(50) DEFAULT 'PENDING',
            applied_at DATETIME NULL,
            updated_at DATETIME NULL,
            INDEX IDX_4F684F86427EB8A5 (request_id),
            INDEX IDX_4F684F8691BD8781 (candidate_id),
            CONSTRAINT FK_4F684F86427EB8A5 FOREIGN KEY (request_id) REFERENCES collab_requests (id) ON DELETE CASCADE,
            CONSTRAINT FK_4F684F8691BD8781 FOREIGN KEY (candidate_id) REFERENCES utilisateurs (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        
        $pdo->exec($sql);
        echo "Table collab_applications created successfully\n";
        
        echo "Adding image_url column to annonces table...\n";
        
        // Add image_url column to annonces table
        $sql = "ALTER TABLE annonces ADD COLUMN image_url VARCHAR(255) NULL";
        try {
            $pdo->exec($sql);
            echo "Column image_url added successfully\n";
        } catch (PDOException $e) {
            echo "Column image_url may already exist: " . $e->getMessage() . "\n";
        }
        
        echo "Cleaning orphaned reservations...\n";
        
        // Clean orphaned reservations
        $sql = "DELETE r FROM reservations r 
                LEFT JOIN annonces a ON r.annonce_id = a.id 
                WHERE a.id IS NULL";
        $result = $pdo->exec($sql);
        echo "Cleaned $result orphaned reservation records\n";
        
        echo "Cleaning all orphaned data...\n";
        
        // Clean orphaned reclamations
        $sql = "DELETE r FROM reclamations r 
                LEFT JOIN utilisateurs u ON r.utilisateur_id = u.id 
                WHERE u.id IS NULL";
        $result = $pdo->exec($sql);
        echo "Cleaned $result orphaned reclamation records\n";
        
        // Clean orphaned cultures
        $sql = "DELETE c FROM cultures c 
                LEFT JOIN utilisateurs u ON c.acheteur_id = u.id 
                WHERE c.acheteur_id IS NOT NULL AND u.id IS NULL";
        $result = $pdo->exec($sql);
        echo "Cleaned $result orphaned culture records\n";
        
        // Clean orphaned diagnosti
        $sql = "DELETE d FROM diagnosti d 
                LEFT JOIN utilisateurs u ON d.agriculteur_id = u.id 
                WHERE d.agriculteur_id IS NOT NULL AND u.id IS NULL";
        $result = $pdo->exec($sql);
        echo "Cleaned $result orphaned diagnosti records\n";
        
        echo "Fixing problematic indexes...\n";
        
        // Drop problematic indexes
        $sql = "DROP INDEX IF EXISTS idx_candidate ON collab_applications";
        try {
            $pdo->exec($sql);
            echo "Dropped idx_candidate index\n";
        } catch (PDOException $e) {
            echo "Could not drop idx_candidate: " . $e->getMessage() . "\n";
        }
        
        $sql = "DROP INDEX IF EXISTS idx_demandeur ON reservations";
        try {
            $pdo->exec($sql);
            echo "Dropped idx_demandeur index\n";
        } catch (PDOException $e) {
            echo "Could not drop idx_demandeur: " . $e->getMessage() . "\n";
        }
        
        $sql = "DROP INDEX IF EXISTS idx_proprietaire ON reservations";
        try {
            $pdo->exec($sql);
            echo "Dropped idx_proprietaire index\n";
        } catch (PDOException $e) {
            echo "Could not drop idx_proprietaire: " . $e->getMessage() . "\n";
        }
        
        $sql = "DROP INDEX IF EXISTS idx_statut ON reservations";
        try {
            $pdo->exec($sql);
            echo "Dropped idx_statut index\n";
        } catch (PDOException $e) {
            echo "Could not drop idx_statut: " . $e->getMessage() . "\n";
        }
        
        echo "Fixing problematic foreign keys...\n";
        
        // Drop problematic foreign keys
        $sql = "ALTER TABLE cultures DROP FOREIGN KEY IF EXISTS fk_cultures_acheteur";
        try {
            $pdo->exec($sql);
            echo "Dropped fk_cultures_acheteur foreign key\n";
        } catch (PDOException $e) {
            echo "Could not drop fk_cultures_acheteur: " . $e->getMessage() . "\n";
        }
        
        echo "Database fix completed!\n";
        
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
} else {
    echo "Could not parse DATABASE_URL\n";
}
