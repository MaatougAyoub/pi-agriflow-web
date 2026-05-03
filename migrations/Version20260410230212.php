<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260410230212 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add image_url column to annonces table';
    }

    public function up(Schema $schema): void
    {
        $schemaManager = $this->connection->createSchemaManager();
        if (!$schemaManager->tablesExist(['annonces'])) {
            return;
        }

        $columns = $schemaManager->listTableColumns('annonces');
        if (!isset($columns['image_url'])) {
            $this->addSql('ALTER TABLE annonces ADD image_url VARCHAR(255) DEFAULT NULL');
        }
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE annonces DROP image_url');
    }
}
