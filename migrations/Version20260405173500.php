<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260405173500 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create irrigation module tables (culture, plan_irrigation, plan_irrigation_jour, diagnostic, produit_phytosanitaire)';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE IF NOT EXISTS culture (
            id INT AUTO_INCREMENT NOT NULL,
            nom VARCHAR(100) NOT NULL,
            type_culture VARCHAR(50) NOT NULL,
            superficie DOUBLE PRECISION NOT NULL,
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE IF NOT EXISTS plan_irrigation (
            id INT AUTO_INCREMENT NOT NULL,
            culture_id INT NOT NULL,
            besoin_eau DOUBLE PRECISION NOT NULL,
            statut VARCHAR(50) NOT NULL,
            date_creation DATETIME NOT NULL,
            INDEX IDX_87A0A53DC50A5BBF (culture_id),
            PRIMARY KEY(id),
            CONSTRAINT FK_87A0A53DC50A5BBF FOREIGN KEY (culture_id) REFERENCES culture (id) ON DELETE CASCADE
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE IF NOT EXISTS plan_irrigation_jour (
            id INT AUTO_INCREMENT NOT NULL,
            plan_id INT NOT NULL,
            jour VARCHAR(3) NOT NULL,
            eau_mm DOUBLE PRECISION NOT NULL,
            duree_min INT NOT NULL,
            temperature DOUBLE PRECISION DEFAULT NULL,
            humidite DOUBLE PRECISION DEFAULT NULL,
            pluie_mm DOUBLE PRECISION DEFAULT NULL,
            date_semaine DATE DEFAULT NULL,
            INDEX IDX_641958379E89928 (plan_id),
            PRIMARY KEY(id),
            CONSTRAINT FK_641958379E89928 FOREIGN KEY (plan_id) REFERENCES plan_irrigation (id) ON DELETE CASCADE
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE IF NOT EXISTS diagnostic (
            id INT AUTO_INCREMENT NOT NULL,
            id_agriculteur INT NOT NULL,
            nom_culture VARCHAR(100) NOT NULL,
            description VARCHAR(255) DEFAULT NULL,
            image_path VARCHAR(255) DEFAULT NULL,
            reponse_expert LONGTEXT DEFAULT NULL,
            statut VARCHAR(50) NOT NULL,
            date_envoi DATETIME NOT NULL,
            INDEX IDX_5A013C7A891E41E5 (id_agriculteur),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE IF NOT EXISTS produit_phytosanitaire (
            id INT AUTO_INCREMENT NOT NULL,
            nom_produit VARCHAR(100) NOT NULL,
            dosage VARCHAR(100) DEFAULT NULL,
            frequence_application VARCHAR(100) DEFAULT NULL,
            remarques LONGTEXT DEFAULT NULL,
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS plan_irrigation_jour');
        $this->addSql('DROP TABLE IF EXISTS plan_irrigation');
        $this->addSql('DROP TABLE IF EXISTS diagnostic');
        $this->addSql('DROP TABLE IF EXISTS produit_phytosanitaire');
        $this->addSql('DROP TABLE IF EXISTS culture');
    }
}
