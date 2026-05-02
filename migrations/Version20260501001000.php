<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260501001000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Rename diagnosti.id_agriculteur to agriculteur_id';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE diagnosti DROP FOREIGN KEY diag');
        $this->addSql('DROP INDEX diag ON diagnosti');
        $this->addSql('ALTER TABLE diagnosti CHANGE id_agriculteur agriculteur_id INT NOT NULL');
        $this->addSql('CREATE INDEX diag ON diagnosti (agriculteur_id)');
        $this->addSql('ALTER TABLE diagnosti ADD CONSTRAINT diag FOREIGN KEY (agriculteur_id) REFERENCES utilisateurs (id) ON DELETE CASCADE ON UPDATE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE diagnosti DROP FOREIGN KEY diag');
        $this->addSql('DROP INDEX diag ON diagnosti');
        $this->addSql('ALTER TABLE diagnosti CHANGE agriculteur_id id_agriculteur INT NOT NULL');
        $this->addSql('CREATE INDEX diag ON diagnosti (id_agriculteur)');
        $this->addSql('ALTER TABLE diagnosti ADD CONSTRAINT diag FOREIGN KEY (id_agriculteur) REFERENCES utilisateurs (id) ON DELETE CASCADE ON UPDATE CASCADE');
    }
}
