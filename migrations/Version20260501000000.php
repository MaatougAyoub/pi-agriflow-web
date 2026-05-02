<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260501000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Rename cultures.id_acheteur to acheteur_id';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE cultures DROP FOREIGN KEY fk_cultures_acheteur');
        $this->addSql('DROP INDEX fk_cultures_acheteur ON cultures');
        $this->addSql('ALTER TABLE cultures CHANGE id_acheteur acheteur_id INT DEFAULT NULL');
        $this->addSql('CREATE INDEX fk_cultures_acheteur ON cultures (acheteur_id)');
        $this->addSql('ALTER TABLE cultures ADD CONSTRAINT fk_cultures_acheteur FOREIGN KEY (acheteur_id) REFERENCES utilisateurs (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE cultures DROP FOREIGN KEY fk_cultures_acheteur');
        $this->addSql('DROP INDEX fk_cultures_acheteur ON cultures');
        $this->addSql('ALTER TABLE cultures CHANGE acheteur_id id_acheteur INT DEFAULT NULL');
        $this->addSql('CREATE INDEX fk_cultures_acheteur ON cultures (id_acheteur)');
        $this->addSql('ALTER TABLE cultures ADD CONSTRAINT fk_cultures_acheteur FOREIGN KEY (id_acheteur) REFERENCES utilisateurs (id) ON DELETE SET NULL');
    }
}
