<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260403201434 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create Marketplace entities for annonces and reservations';
    }

    public function up(Schema $schema): void
    {
        // Keep migration compatible with existing databases created from older SQL dumps.
        $schemaManager = $this->connection->createSchemaManager();
        $reservationsTableExists = (int) $this->connection->fetchOne(
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'reservations'"
        ) > 0;

        $this->addSql('CREATE TABLE IF NOT EXISTS annonces (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(150) NOT NULL, description LONGTEXT NOT NULL, type VARCHAR(20) NOT NULL, statut VARCHAR(20) NOT NULL, prix NUMERIC(10, 2) NOT NULL, categorie VARCHAR(120) NOT NULL, image_url VARCHAR(255) NOT NULL, localisation VARCHAR(120) NOT NULL, proprietaire_id INT NOT NULL, quantite_disponible INT NOT NULL, unite_prix VARCHAR(20) NOT NULL, date_creation DATETIME NOT NULL, date_modification DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE IF NOT EXISTS reservations (id INT AUTO_INCREMENT NOT NULL, demandeur_id INT NOT NULL, proprietaire_id INT NOT NULL, date_debut DATE NOT NULL, date_fin DATE NOT NULL, quantite INT NOT NULL, prix_total NUMERIC(10, 2) NOT NULL, commission NUMERIC(10, 2) NOT NULL, statut VARCHAR(20) NOT NULL, message LONGTEXT DEFAULT NULL, date_creation DATETIME NOT NULL, annonce_id INT NOT NULL, INDEX IDX_4DA2398805AB2F (annonce_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');

        if (!$reservationsTableExists) {
            $this->addSql('ALTER TABLE reservations ADD CONSTRAINT FK_4DA2398805AB2F FOREIGN KEY (annonce_id) REFERENCES annonces (id) ON DELETE CASCADE');

            return;
        }

        $columnMap = [];
        foreach ($schemaManager->listTableColumns('reservations') as $column) {
            $columnMap[strtolower($column->getName())] = true;
        }

        if (!isset($columnMap['commission'])) {
            $this->addSql('ALTER TABLE reservations ADD commission NUMERIC(10, 2) NOT NULL DEFAULT 0.00');
        }

        if (!isset($columnMap['message'])) {
            $this->addSql('ALTER TABLE reservations ADD message LONGTEXT DEFAULT NULL');
        }

        if (!isset($columnMap['annonce_id'])) {
            $this->addSql('ALTER TABLE reservations ADD annonce_id INT NOT NULL');
        }

        $indexMap = [];
        foreach ($schemaManager->listTableIndexes('reservations') as $indexName => $index) {
            $indexMap[strtolower((string) $indexName)] = true;
        }

        if (!isset($indexMap[strtolower('IDX_4DA2398805AB2F')])) {
            $this->addSql('CREATE INDEX IDX_4DA2398805AB2F ON reservations (annonce_id)');
        }

        $hasAnnonceForeignKey = false;
        foreach ($schemaManager->listTableForeignKeys('reservations') as $foreignKey) {
            if (['annonce_id'] === $foreignKey->getLocalColumns()) {
                $hasAnnonceForeignKey = true;
                break;
            }
        }

        $orphanReservationCount = (int) $this->connection->fetchOne(
            'SELECT COUNT(*) FROM reservations r LEFT JOIN annonces a ON a.id = r.annonce_id WHERE a.id IS NULL'
        );

        if (!$hasAnnonceForeignKey && 0 === $orphanReservationCount) {
            $this->addSql('ALTER TABLE reservations ADD CONSTRAINT FK_4DA2398805AB2F FOREIGN KEY (annonce_id) REFERENCES annonces (id) ON DELETE CASCADE');
        }
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reservations DROP FOREIGN KEY FK_4DA2398805AB2F');
        $this->addSql('DROP TABLE annonces');
        $this->addSql('DROP TABLE reservations');
    }
}
