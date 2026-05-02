<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260501002000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Rename plans_irrigation.id_culture to culture_id';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('DROP INDEX id_culture ON plans_irrigation');
        $this->addSql('ALTER TABLE plans_irrigation CHANGE id_culture culture_id INT DEFAULT NULL');
        $this->addSql('CREATE INDEX id_culture ON plans_irrigation (culture_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX id_culture ON plans_irrigation');
        $this->addSql('ALTER TABLE plans_irrigation CHANGE culture_id id_culture INT DEFAULT NULL');
        $this->addSql('CREATE INDEX id_culture ON plans_irrigation (id_culture)');
    }
}
