<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260405000001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create collaboration module tables: collab_requests and collab_applications';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE IF NOT EXISTS collab_requests (
            id             INT AUTO_INCREMENT NOT NULL,
            requester_id   INT NOT NULL,
            title          VARCHAR(150) NOT NULL,
            description    LONGTEXT NOT NULL,
            start_date     DATE NOT NULL,
            end_date       DATE NOT NULL,
            needed_people  INT NOT NULL,
            status         VARCHAR(20) NOT NULL DEFAULT \'open\',
            location       VARCHAR(255) NOT NULL DEFAULT \'Non spécifié\',
            salary         NUMERIC(10,2) NOT NULL DEFAULT 0,
            salary_per_day NUMERIC(10,2) NOT NULL DEFAULT 0,
            publisher      VARCHAR(255) DEFAULT NULL,
            latitude       NUMERIC(10,7) DEFAULT NULL,
            longitude      NUMERIC(10,7) DEFAULT NULL,
            created_at     DATETIME NOT NULL,
            updated_at     DATETIME NOT NULL,
            INDEX idx_collab_req_status  (status),
            INDEX idx_collab_req_end_date (end_date),
            INDEX IDX_collab_req_requester (requester_id),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');

        $this->addSql('CREATE TABLE IF NOT EXISTS collab_applications (
            id                    INT AUTO_INCREMENT NOT NULL,
            request_id            INT NOT NULL,
            candidate_id          INT NOT NULL,
            full_name             VARCHAR(100) NOT NULL,
            phone                 VARCHAR(30) NOT NULL,
            email                 VARCHAR(180) NOT NULL,
            years_of_experience   INT NOT NULL,
            motivation            LONGTEXT NOT NULL,
            expected_salary       NUMERIC(10,2) NOT NULL DEFAULT 0,
            status                VARCHAR(20) NOT NULL DEFAULT \'pending\',
            applied_at            DATETIME NOT NULL,
            updated_at            DATETIME NOT NULL,
            UNIQUE INDEX uq_candidate_request (candidate_id, request_id),
            INDEX idx_collab_app_status (status),
            INDEX IDX_collab_app_request   (request_id),
            INDEX IDX_collab_app_candidate (candidate_id),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');

        // Keep migration compatible with databases that may already contain the tables
        $schemaManager = $this->connection->createSchemaManager();

        $collabApplicationsExists = (int) $this->connection->fetchOne(
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'collab_applications'"
        ) > 0;

        $collabRequestsExists = (int) $this->connection->fetchOne(
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'collab_requests'"
        ) > 0;

        if (!$collabApplicationsExists || !$collabRequestsExists) {
            // If either table is not present yet, just add the FK (fresh DB case)
            $this->addSql('ALTER TABLE collab_applications
            ADD CONSTRAINT FK_collab_app_request
            FOREIGN KEY (request_id) REFERENCES collab_requests (id) ON DELETE CASCADE');

            return;
        }

        $hasFk = false;
        foreach ($schemaManager->listTableForeignKeys('collab_applications') as $foreignKey) {
            if (['request_id'] === $foreignKey->getLocalColumns()) {
                $hasFk = true;
                break;
            }
        }

        $orphanCount = (int) $this->connection->fetchOne(
            'SELECT COUNT(*) FROM collab_applications a LEFT JOIN collab_requests r ON r.id = a.request_id WHERE r.id IS NULL'
        );

        if (!$hasFk && 0 === $orphanCount) {
            $this->addSql('ALTER TABLE collab_applications
            ADD CONSTRAINT FK_collab_app_request
            FOREIGN KEY (request_id) REFERENCES collab_requests (id) ON DELETE CASCADE');
        }
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE collab_applications DROP FOREIGN KEY FK_collab_app_request');
        $this->addSql('DROP TABLE collab_applications');
        $this->addSql('DROP TABLE collab_requests');
    }
}
