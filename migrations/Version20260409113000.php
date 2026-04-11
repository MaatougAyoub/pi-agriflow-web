<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260409113000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create culture history table for culture lifecycle events';
    }

    public function up(Schema $schema): void
    {
        $tableExists = (int) $this->connection->fetchOne(
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'culture_history'"
        ) > 0;

        if (!$tableExists) {
            $this->addSql('CREATE TABLE culture_history (id INT AUTO_INCREMENT NOT NULL, culture_id INT NOT NULL, utilisateur_id INT DEFAULT NULL, action VARCHAR(50) NOT NULL, performed_at DATETIME NOT NULL, details LONGTEXT DEFAULT NULL, INDEX IDX_3D4E689C45C4487A (culture_id), INDEX IDX_3D4E689CFB88E14F (utilisateur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        }

        $schemaManager = $this->connection->createSchemaManager();
        $foreignKeys = $schemaManager->listTableForeignKeys('culture_history');
        $hasCultureForeignKey = false;
        $hasUtilisateurForeignKey = false;

        foreach ($foreignKeys as $foreignKey) {
            if (['culture_id'] === $foreignKey->getLocalColumns()) {
                $hasCultureForeignKey = true;
            }

            if (['utilisateur_id'] === $foreignKey->getLocalColumns()) {
                $hasUtilisateurForeignKey = true;
            }
        }

        if (!$hasCultureForeignKey) {
            $this->addSql('ALTER TABLE culture_history ADD CONSTRAINT FK_3D4E689C45C4487A FOREIGN KEY (culture_id) REFERENCES cultures (id) ON DELETE CASCADE');
        }

        if (!$hasUtilisateurForeignKey) {
            $this->addSql('ALTER TABLE culture_history ADD CONSTRAINT FK_3D4E689CFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs (id) ON DELETE SET NULL');
        }
    }

    public function down(Schema $schema): void
    {
        $tableExists = (int) $this->connection->fetchOne(
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'culture_history'"
        ) > 0;

        if ($tableExists) {
            $this->addSql('DROP TABLE culture_history');
        }
    }
}
