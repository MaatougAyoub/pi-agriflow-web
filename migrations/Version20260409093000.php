<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260409093000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add geocoding fields to marketplace annonces';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE annonces ADD COLUMN IF NOT EXISTS latitude DOUBLE PRECISION DEFAULT NULL, ADD COLUMN IF NOT EXISTS longitude DOUBLE PRECISION DEFAULT NULL, ADD COLUMN IF NOT EXISTS localisation_normalisee VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE annonces DROP COLUMN IF EXISTS localisation_normalisee, DROP COLUMN IF EXISTS longitude, DROP COLUMN IF EXISTS latitude');
    }
}
