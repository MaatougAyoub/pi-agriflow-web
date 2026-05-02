<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260410092505 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $schemaManager = $this->connection->createSchemaManager();

        $this->addSql('CREATE TABLE IF NOT EXISTS annonces (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(150) NOT NULL, description LONGTEXT NOT NULL, type VARCHAR(20) NOT NULL, statut VARCHAR(20) NOT NULL, prix NUMERIC(10, 2) NOT NULL, categorie VARCHAR(120) NOT NULL, image_url VARCHAR(255) DEFAULT NULL, localisation VARCHAR(120) NOT NULL, latitude DOUBLE PRECISION DEFAULT NULL, longitude DOUBLE PRECISION DEFAULT NULL, localisation_normalisee VARCHAR(255) DEFAULT NULL, proprietaire_id INT NOT NULL, quantite_disponible INT NOT NULL, unite_prix VARCHAR(20) NOT NULL, date_creation DATETIME NOT NULL, date_modification DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE IF NOT EXISTS collab_applications (id INT AUTO_INCREMENT NOT NULL, full_name VARCHAR(255) NOT NULL, phone VARCHAR(20) NOT NULL, email VARCHAR(100) NOT NULL, years_of_experience INT DEFAULT 0 NOT NULL, motivation LONGTEXT NOT NULL, expected_salary NUMERIC(10, 2) DEFAULT \'0.00\' NOT NULL, status VARCHAR(50) DEFAULT \'PENDING\' NOT NULL, applied_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, request_id INT NOT NULL, candidate_id INT NOT NULL, INDEX IDX_4F684F86427EB8A5 (request_id), INDEX IDX_4F684F8691BD8781 (candidate_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE IF NOT EXISTS collab_requests (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, location VARCHAR(100) NOT NULL, latitude NUMERIC(10, 7) DEFAULT NULL, longitude NUMERIC(10, 7) DEFAULT NULL, start_date DATE NOT NULL, end_date DATE NOT NULL, needed_people INT DEFAULT 1 NOT NULL, salary NUMERIC(10, 2) DEFAULT \'0.00\' NOT NULL, status VARCHAR(50) DEFAULT \'PENDING\' NOT NULL, publisher VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, requester_id INT NOT NULL, INDEX IDX_10CC4FA4ED442CF4 (requester_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE IF NOT EXISTS reservations (id INT AUTO_INCREMENT NOT NULL, demandeur_id INT NOT NULL, proprietaire_id INT NOT NULL, date_debut DATE NOT NULL, date_fin DATE NOT NULL, quantite INT NOT NULL, prix_total NUMERIC(10, 2) NOT NULL, commission NUMERIC(10, 2) NOT NULL, statut VARCHAR(20) NOT NULL, message LONGTEXT DEFAULT NULL, date_creation DATETIME NOT NULL, annonce_id INT NOT NULL, INDEX IDX_4DA2398805AB2F (annonce_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE IF NOT EXISTS utilisateurs (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, cin INT NOT NULL, email VARCHAR(255) NOT NULL, motDePasse VARCHAR(255) NOT NULL, role VARCHAR(40) NOT NULL, dateCreation DATE NOT NULL, signature VARCHAR(500) NOT NULL, revenu DOUBLE PRECISION DEFAULT NULL, carte_pro VARCHAR(500) DEFAULT NULL, adresse VARCHAR(255) DEFAULT NULL, parcelles VARCHAR(255) DEFAULT NULL, certification VARCHAR(500) DEFAULT NULL, verification_status VARCHAR(20) DEFAULT \'APPROVED\' NOT NULL, verification_reason VARCHAR(500) DEFAULT NULL, verification_score DOUBLE PRECISION DEFAULT NULL, nom_ar VARCHAR(255) DEFAULT NULL, prenom_ar VARCHAR(255) DEFAULT NULL, UNIQUE INDEX cin (cin), UNIQUE INDEX email (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');

        $collabApplicationsExists = $schemaManager->tablesExist(['collab_applications']);
        $collabRequestsExists = $schemaManager->tablesExist(['collab_requests']);
        $utilisateursExists = $schemaManager->tablesExist(['utilisateurs']);
        $reservationsExists = $schemaManager->tablesExist(['reservations']);
        $annoncesExists = $schemaManager->tablesExist(['annonces']);

        if ($collabApplicationsExists && $collabRequestsExists) {
            $hasRequestFk = false;
            foreach ($schemaManager->listTableForeignKeys('collab_applications') as $foreignKey) {
                if (['request_id'] === $foreignKey->getLocalColumns()) {
                    $hasRequestFk = true;
                    break;
                }
            }

            $orphanRequestCount = (int) $this->connection->fetchOne(
                'SELECT COUNT(*) FROM collab_applications a LEFT JOIN collab_requests r ON r.id = a.request_id WHERE r.id IS NULL'
            );

            if (!$hasRequestFk && 0 === $orphanRequestCount) {
                $this->addSql('ALTER TABLE collab_applications ADD CONSTRAINT FK_4F684F86427EB8A5 FOREIGN KEY (request_id) REFERENCES collab_requests (id) ON DELETE CASCADE');
            }
        }

        if ($collabApplicationsExists && $utilisateursExists) {
            $hasCandidateFk = false;
            foreach ($schemaManager->listTableForeignKeys('collab_applications') as $foreignKey) {
                if (['candidate_id'] === $foreignKey->getLocalColumns()) {
                    $hasCandidateFk = true;
                    break;
                }
            }

            $orphanCandidateCount = (int) $this->connection->fetchOne(
                'SELECT COUNT(*) FROM collab_applications a LEFT JOIN utilisateurs u ON u.id = a.candidate_id WHERE u.id IS NULL'
            );

            if (!$hasCandidateFk && 0 === $orphanCandidateCount) {
                $this->addSql('ALTER TABLE collab_applications ADD CONSTRAINT FK_4F684F8691BD8781 FOREIGN KEY (candidate_id) REFERENCES utilisateurs (id)');
            }
        }

        if ($collabRequestsExists && $utilisateursExists) {
            $hasRequesterFk = false;
            foreach ($schemaManager->listTableForeignKeys('collab_requests') as $foreignKey) {
                if (['requester_id'] === $foreignKey->getLocalColumns()) {
                    $hasRequesterFk = true;
                    break;
                }
            }

            $orphanRequesterCount = (int) $this->connection->fetchOne(
                'SELECT COUNT(*) FROM collab_requests r LEFT JOIN utilisateurs u ON u.id = r.requester_id WHERE u.id IS NULL'
            );

            if (!$hasRequesterFk && 0 === $orphanRequesterCount) {
                $this->addSql('ALTER TABLE collab_requests ADD CONSTRAINT FK_10CC4FA4ED442CF4 FOREIGN KEY (requester_id) REFERENCES utilisateurs (id)');
            }
        }

        if ($reservationsExists && $annoncesExists) {
            $hasAnnonceFk = false;
            foreach ($schemaManager->listTableForeignKeys('reservations') as $foreignKey) {
                if (['annonce_id'] === $foreignKey->getLocalColumns()) {
                    $hasAnnonceFk = true;
                    break;
                }
            }

            $orphanReservationCount = (int) $this->connection->fetchOne(
                'SELECT COUNT(*) FROM reservations r LEFT JOIN annonces a ON a.id = r.annonce_id WHERE a.id IS NULL'
            );

            if (!$hasAnnonceFk && 0 === $orphanReservationCount) {
                $this->addSql('ALTER TABLE reservations ADD CONSTRAINT FK_4DA2398805AB2F FOREIGN KEY (annonce_id) REFERENCES annonces (id) ON DELETE CASCADE');
            }
        }
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE collab_applications DROP FOREIGN KEY FK_4F684F86427EB8A5');
        $this->addSql('ALTER TABLE collab_applications DROP FOREIGN KEY FK_4F684F8691BD8781');
        $this->addSql('ALTER TABLE collab_requests DROP FOREIGN KEY FK_10CC4FA4ED442CF4');
        $this->addSql('ALTER TABLE reservations DROP FOREIGN KEY FK_4DA2398805AB2F');
        $this->addSql('DROP TABLE annonces');
        $this->addSql('DROP TABLE collab_applications');
        $this->addSql('DROP TABLE collab_requests');
        $this->addSql('DROP TABLE reservations');
        $this->addSql('DROP TABLE utilisateurs');
    }
}
