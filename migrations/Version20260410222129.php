<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260410222129 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $schemaManager = $this->connection->createSchemaManager();
        $dropIndexIfExists = function (string $table, string $indexName) use ($schemaManager): void {
            if (!$schemaManager->tablesExist([$table])) {
                return;
            }

            $indexes = $schemaManager->listTableIndexes($table);
            if (isset($indexes[$indexName])) {
                $this->addSql(sprintf('DROP INDEX %s ON %s', $indexName, $table));
            }
        };

        $createIndexIfMissing = function (string $table, string $indexName, string $indexSql) use ($schemaManager): void {
            if (!$schemaManager->tablesExist([$table])) {
                return;
            }

            $indexes = $schemaManager->listTableIndexes($table);
            $indexMap = [];
            foreach ($indexes as $name => $index) {
                $indexMap[strtolower($name)] = $index;
            }

            if (!isset($indexMap[strtolower($indexName)])) {
                $this->addSql($indexSql);
            }
        };

        $addFkIfNoOrphans = function (string $table, string $fkName, string $fkSql, string $orphanSql) use ($schemaManager): void {
            if (!$schemaManager->tablesExist([$table])) {
                return;
            }

            foreach ($schemaManager->listTableForeignKeys($table) as $foreignKey) {
                if ($foreignKey->getName() === $fkName) {
                    return;
                }
            }

            $orphanCount = (int) $this->connection->fetchOne($orphanSql);
            if (0 === $orphanCount) {
                $this->addSql($fkSql);
            }
        };

        $dropIndexIfExists('annonces', 'idx_categorie');
        $dropIndexIfExists('annonces', 'idx_prix');
        $dropIndexIfExists('annonces', 'idx_type');
        $dropIndexIfExists('annonces', 'idx_proprietaire');
        $dropIndexIfExists('annonces', 'idx_statut');

        $annoncesAlterParts = [];
        if ($schemaManager->tablesExist(['annonces'])) {
            $annoncesColumns = $schemaManager->listTableColumns('annonces');
            if (!isset($annoncesColumns['image_url'])) {
                $annoncesAlterParts[] = 'ADD image_url VARCHAR(255) DEFAULT NULL';
            }
            if (!isset($annoncesColumns['localisation_normalisee'])) {
                $annoncesAlterParts[] = 'ADD localisation_normalisee VARCHAR(255) DEFAULT NULL';
            }

            $annoncesDropColumns = [
                'marque',
                'modele',
                'annee_fabrication',
                'date_debut_disponibilite',
                'date_fin_disponibilite',
                'avec_operateur',
                'assurance_incluse',
                'caution',
                'conditions_location',
                'unite_quantite',
            ];

            foreach ($annoncesDropColumns as $column) {
                if (isset($annoncesColumns[$column])) {
                    $annoncesAlterParts[] = sprintf('DROP %s', $column);
                }
            }

            $annoncesChangeColumns = [
                'titre' => 'CHANGE titre titre VARCHAR(150) NOT NULL',
                'description' => 'CHANGE description description LONGTEXT NOT NULL',
                'type' => 'CHANGE type type VARCHAR(20) NOT NULL',
                'statut' => 'CHANGE statut statut VARCHAR(20) NOT NULL',
                'unite_prix' => 'CHANGE unite_prix unite_prix VARCHAR(20) NOT NULL',
                'categorie' => 'CHANGE categorie categorie VARCHAR(120) NOT NULL',
                'localisation' => 'CHANGE localisation localisation VARCHAR(120) NOT NULL',
                'latitude' => 'CHANGE latitude latitude DOUBLE PRECISION DEFAULT NULL',
                'longitude' => 'CHANGE longitude longitude DOUBLE PRECISION DEFAULT NULL',
                'date_creation' => 'CHANGE date_creation date_creation DATETIME NOT NULL',
                'date_modification' => 'CHANGE date_modification date_modification DATETIME NOT NULL',
                'quantite_disponible' => 'CHANGE quantite_disponible quantite_disponible INT NOT NULL',
            ];

            foreach ($annoncesChangeColumns as $column => $definition) {
                if (isset($annoncesColumns[$column])) {
                    $annoncesAlterParts[] = $definition;
                }
            }
        }

        if ([] !== $annoncesAlterParts) {
            $this->addSql('ALTER TABLE annonces ' . implode(', ', $annoncesAlterParts));
        }

        $dropIndexIfExists('collab_applications', 'idx_status');
        $dropIndexIfExists('collab_applications', 'unique_application');

        $dropIndexIfExists('collab_requests', 'idx_dates');
        $dropIndexIfExists('collab_requests', 'idx_status');
        $dropIndexIfExists('collab_requests', 'idx_location');

        $collabRequestsAlterParts = [];
        if ($schemaManager->tablesExist(['collab_requests'])) {
            $collabRequestColumns = $schemaManager->listTableColumns('collab_requests');
            $collabRequestsChangeColumns = [
                'id' => 'CHANGE id id INT AUTO_INCREMENT NOT NULL',
                'description' => 'CHANGE description description LONGTEXT NOT NULL',
                'requester_id' => 'CHANGE requester_id requester_id INT NOT NULL',
            ];

            foreach ($collabRequestsChangeColumns as $column => $definition) {
                if (isset($collabRequestColumns[$column])) {
                    $collabRequestsAlterParts[] = $definition;
                }
            }
        }

        if ([] !== $collabRequestsAlterParts) {
            $this->addSql('ALTER TABLE collab_requests ' . implode(', ', $collabRequestsAlterParts));
        }

        $addFkIfNoOrphans(
            'collab_requests',
            'FK_10CC4FA4ED442CF4',
            'ALTER TABLE collab_requests ADD CONSTRAINT FK_10CC4FA4ED442CF4 FOREIGN KEY (requester_id) REFERENCES utilisateurs (id)',
            'SELECT COUNT(*) FROM collab_requests r LEFT JOIN utilisateurs u ON u.id = r.requester_id WHERE u.id IS NULL'
        );
        $createIndexIfMissing(
            'collab_requests',
            'IDX_10CC4FA4ED442CF4',
            'CREATE INDEX IDX_10CC4FA4ED442CF4 ON collab_requests (requester_id)'
        );

        $collabApplicationsAlterParts = [];
        if ($schemaManager->tablesExist(['collab_applications'])) {
            $collabAppColumns = $schemaManager->listTableColumns('collab_applications');
            $collabApplicationsChangeColumns = [
                'id' => 'CHANGE id id INT AUTO_INCREMENT NOT NULL',
                'request_id' => 'CHANGE request_id request_id INT NOT NULL',
                'candidate_id' => 'CHANGE candidate_id candidate_id INT NOT NULL',
                'motivation' => 'CHANGE motivation motivation LONGTEXT NOT NULL',
                'expected_salary' => 'CHANGE expected_salary expected_salary NUMERIC(10, 2) DEFAULT \'0.00\' NOT NULL',
            ];

            foreach ($collabApplicationsChangeColumns as $column => $definition) {
                if (isset($collabAppColumns[$column])) {
                    $collabApplicationsAlterParts[] = $definition;
                }
            }
        }

        if ([] !== $collabApplicationsAlterParts) {
            $this->addSql('ALTER TABLE collab_applications ' . implode(', ', $collabApplicationsAlterParts));
        }

        $addFkIfNoOrphans(
            'collab_applications',
            'FK_4F684F86427EB8A5',
            'ALTER TABLE collab_applications ADD CONSTRAINT FK_4F684F86427EB8A5 FOREIGN KEY (request_id) REFERENCES collab_requests (id) ON DELETE CASCADE',
            'SELECT COUNT(*) FROM collab_applications a LEFT JOIN collab_requests r ON r.id = a.request_id WHERE r.id IS NULL'
        );
        $addFkIfNoOrphans(
            'collab_applications',
            'FK_4F684F8691BD8781',
            'ALTER TABLE collab_applications ADD CONSTRAINT FK_4F684F8691BD8781 FOREIGN KEY (candidate_id) REFERENCES utilisateurs (id)',
            'SELECT COUNT(*) FROM collab_applications a LEFT JOIN utilisateurs u ON u.id = a.candidate_id WHERE u.id IS NULL'
        );
        $createIndexIfMissing(
            'collab_applications',
            'IDX_4F684F86427EB8A5',
            'CREATE INDEX IDX_4F684F86427EB8A5 ON collab_applications (request_id)'
        );
        $dropIndexIfExists('collab_applications', 'idx_candidate');
        $createIndexIfMissing(
            'collab_applications',
            'IDX_4F684F8691BD8781',
            'CREATE INDEX IDX_4F684F8691BD8781 ON collab_applications (candidate_id)'
        );

        $dropIndexIfExists('reservations', 'idx_demandeur');
        $dropIndexIfExists('reservations', 'idx_proprietaire');
        $dropIndexIfExists('reservations', 'idx_statut');

        $reservationsAlterParts = [];
        if ($schemaManager->tablesExist(['reservations'])) {
            $reservationColumns = $schemaManager->listTableColumns('reservations');
            if (!isset($reservationColumns['commission'])) {
                $reservationsAlterParts[] = 'ADD commission NUMERIC(10, 2) NOT NULL';
            }
            if (!isset($reservationColumns['message'])) {
                $reservationsAlterParts[] = 'ADD message LONGTEXT DEFAULT NULL';
            }

            $reservationDropColumns = [
                'caution',
                'message_demande',
                'reponse_proprietaire',
                'date_demande',
                'date_reponse',
                'contrat_url',
                'contrat_signe',
                'date_signature_contrat',
                'paiement_effectue',
                'date_paiement',
                'mode_paiement',
            ];

            foreach ($reservationDropColumns as $column) {
                if (isset($reservationColumns[$column])) {
                    $reservationsAlterParts[] = sprintf('DROP %s', $column);
                }
            }

            $reservationChangeColumns = [
                'quantite' => 'CHANGE quantite quantite INT NOT NULL',
                'statut' => 'CHANGE statut statut VARCHAR(20) NOT NULL',
                'date_creation' => 'CHANGE date_creation date_creation DATETIME NOT NULL',
            ];

            foreach ($reservationChangeColumns as $column => $definition) {
                if (isset($reservationColumns[$column])) {
                    $reservationsAlterParts[] = $definition;
                }
            }
        }

        if ([] !== $reservationsAlterParts) {
            $this->addSql('ALTER TABLE reservations ' . implode(', ', $reservationsAlterParts));
        }

        $addFkIfNoOrphans(
            'reservations',
            'FK_4DA2398805AB2F',
            'ALTER TABLE reservations ADD CONSTRAINT FK_4DA2398805AB2F FOREIGN KEY (annonce_id) REFERENCES annonces (id) ON DELETE CASCADE',
            'SELECT COUNT(*) FROM reservations r LEFT JOIN annonces a ON a.id = r.annonce_id WHERE a.id IS NULL'
        );
        $dropIndexIfExists('reservations', 'fk_reservation_annonce');
        $createIndexIfMissing(
            'reservations',
            'IDX_4DA2398805AB2F',
            'CREATE INDEX IDX_4DA2398805AB2F ON reservations (annonce_id)'
        );
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE annonces ADD marque VARCHAR(100) DEFAULT NULL, ADD modele VARCHAR(100) DEFAULT NULL, ADD annee_fabrication INT DEFAULT NULL, ADD date_debut_disponibilite DATE DEFAULT NULL, ADD date_fin_disponibilite DATE DEFAULT NULL, ADD avec_operateur TINYINT DEFAULT 0, ADD assurance_incluse TINYINT DEFAULT 0, ADD caution NUMERIC(10, 2) DEFAULT \'0.00\', ADD conditions_location TEXT DEFAULT NULL, ADD unite_quantite VARCHAR(20) DEFAULT \'kg\', DROP image_url, DROP localisation_normalisee, CHANGE titre titre VARCHAR(255) NOT NULL, CHANGE description description TEXT DEFAULT NULL, CHANGE type type ENUM(\'LOCATION\', \'VENTE\') NOT NULL, CHANGE statut statut ENUM(\'DISPONIBLE\', \'RESERVEE\', \'LOUEE\', \'VENDUE\', \'EXPIREE\') DEFAULT \'DISPONIBLE\', CHANGE categorie categorie VARCHAR(100) DEFAULT NULL, CHANGE localisation localisation VARCHAR(255) DEFAULT NULL, CHANGE latitude latitude NUMERIC(10, 8) DEFAULT NULL, CHANGE longitude longitude NUMERIC(11, 8) DEFAULT NULL, CHANGE quantite_disponible quantite_disponible INT DEFAULT 0, CHANGE unite_prix unite_prix VARCHAR(20) DEFAULT \'jour\', CHANGE date_creation date_creation DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE date_modification date_modification DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('CREATE INDEX idx_categorie ON annonces (categorie)');
        $this->addSql('CREATE INDEX idx_prix ON annonces (prix)');
        $this->addSql('CREATE INDEX idx_type ON annonces (type)');
        $this->addSql('CREATE INDEX idx_proprietaire ON annonces (proprietaire_id)');
        $this->addSql('CREATE INDEX idx_statut ON annonces (statut)');
        $this->addSql('ALTER TABLE collab_applications DROP FOREIGN KEY FK_4F684F86427EB8A5');
        $this->addSql('ALTER TABLE collab_applications DROP FOREIGN KEY FK_4F684F8691BD8781');
        $this->addSql('DROP INDEX IDX_4F684F86427EB8A5 ON collab_applications');
        $this->addSql('ALTER TABLE collab_applications DROP FOREIGN KEY FK_4F684F8691BD8781');
        $this->addSql('ALTER TABLE collab_applications CHANGE id id BIGINT AUTO_INCREMENT NOT NULL, CHANGE motivation motivation TEXT NOT NULL, CHANGE expected_salary expected_salary NUMERIC(10, 2) DEFAULT \'0.00\', CHANGE request_id request_id BIGINT NOT NULL, CHANGE candidate_id candidate_id BIGINT DEFAULT 1 NOT NULL');
        $this->addSql('CREATE INDEX idx_status ON collab_applications (status)');
        $this->addSql('CREATE UNIQUE INDEX unique_application ON collab_applications (request_id, candidate_id)');
        $this->addSql('DROP INDEX idx_4f684f8691bd8781 ON collab_applications');
        $this->addSql('CREATE INDEX idx_candidate ON collab_applications (candidate_id)');
        $this->addSql('ALTER TABLE collab_applications ADD CONSTRAINT FK_4F684F8691BD8781 FOREIGN KEY (candidate_id) REFERENCES utilisateurs (id)');
        $this->addSql('ALTER TABLE collab_requests DROP FOREIGN KEY FK_10CC4FA4ED442CF4');
        $this->addSql('DROP INDEX IDX_10CC4FA4ED442CF4 ON collab_requests');
        $this->addSql('ALTER TABLE collab_requests CHANGE id id BIGINT AUTO_INCREMENT NOT NULL, CHANGE description description TEXT NOT NULL, CHANGE requester_id requester_id BIGINT DEFAULT 1 NOT NULL');
        $this->addSql('CREATE INDEX idx_dates ON collab_requests (start_date, end_date)');
        $this->addSql('CREATE INDEX idx_status ON collab_requests (status)');
        $this->addSql('CREATE INDEX idx_location ON collab_requests (location)');
        $this->addSql('ALTER TABLE reservations DROP FOREIGN KEY FK_4DA2398805AB2F');
        $this->addSql('ALTER TABLE reservations DROP FOREIGN KEY FK_4DA2398805AB2F');
        $this->addSql('ALTER TABLE reservations ADD caution NUMERIC(10, 2) DEFAULT \'0.00\', ADD message_demande TEXT DEFAULT NULL, ADD reponse_proprietaire TEXT DEFAULT NULL, ADD date_demande DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, ADD date_reponse DATETIME DEFAULT NULL, ADD contrat_url VARCHAR(500) DEFAULT NULL, ADD contrat_signe TINYINT DEFAULT 0, ADD date_signature_contrat DATETIME DEFAULT NULL, ADD paiement_effectue TINYINT DEFAULT 0, ADD date_paiement DATETIME DEFAULT NULL, ADD mode_paiement VARCHAR(50) DEFAULT NULL, DROP commission, DROP message, CHANGE quantite quantite INT DEFAULT 1, CHANGE statut statut ENUM(\'EN_ATTENTE\', \'ACCEPTEE\', \'REFUSEE\', \'EN_COURS\', \'TERMINEE\', \'ANNULEE\') DEFAULT \'EN_ATTENTE\', CHANGE date_creation date_creation DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('CREATE INDEX idx_demandeur ON reservations (demandeur_id)');
        $this->addSql('CREATE INDEX idx_proprietaire ON reservations (proprietaire_id)');
        $this->addSql('CREATE INDEX idx_statut ON reservations (statut)');
        $this->addSql('DROP INDEX idx_4da2398805ab2f ON reservations');
        $this->addSql('CREATE INDEX fk_reservation_annonce ON reservations (annonce_id)');
        $this->addSql('ALTER TABLE reservations ADD CONSTRAINT FK_4DA2398805AB2F FOREIGN KEY (annonce_id) REFERENCES annonces (id) ON DELETE CASCADE');
    }
}
