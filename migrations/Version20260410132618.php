<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260410132618 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX idx_proprietaire ON annonces');
        $this->addSql('DROP INDEX idx_statut ON annonces');
        $this->addSql('DROP INDEX idx_categorie ON annonces');
        $this->addSql('DROP INDEX idx_prix ON annonces');
        $this->addSql('DROP INDEX idx_type ON annonces');
        $this->addSql('ALTER TABLE annonces DROP marque, DROP modele, DROP annee_fabrication, DROP date_debut_disponibilite, DROP date_fin_disponibilite, DROP avec_operateur, DROP assurance_incluse, DROP caution, DROP conditions_location, DROP unite_quantite, CHANGE titre titre VARCHAR(150) NOT NULL, CHANGE description description LONGTEXT NOT NULL, CHANGE type type VARCHAR(20) NOT NULL, CHANGE statut statut VARCHAR(20) NOT NULL, CHANGE unite_prix unite_prix VARCHAR(20) NOT NULL, CHANGE categorie categorie VARCHAR(120) NOT NULL, CHANGE localisation localisation VARCHAR(120) NOT NULL, CHANGE latitude latitude DOUBLE PRECISION DEFAULT NULL, CHANGE longitude longitude DOUBLE PRECISION DEFAULT NULL, CHANGE date_creation date_creation DATETIME NOT NULL, CHANGE date_modification date_modification DATETIME NOT NULL, CHANGE quantite_disponible quantite_disponible INT NOT NULL');
        $this->addSql('DROP INDEX unique_application ON collab_applications');
        $this->addSql('DROP INDEX idx_status ON collab_applications');
        $this->addSql('ALTER TABLE collab_applications CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE request_id request_id INT NOT NULL, CHANGE candidate_id candidate_id INT NOT NULL, CHANGE motivation motivation LONGTEXT NOT NULL, CHANGE expected_salary expected_salary NUMERIC(10, 2) DEFAULT \'0.00\' NOT NULL');
        $this->addSql('ALTER TABLE collab_applications ADD CONSTRAINT FK_4F684F86427EB8A5 FOREIGN KEY (request_id) REFERENCES collab_requests (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE collab_applications ADD CONSTRAINT FK_4F684F8691BD8781 FOREIGN KEY (candidate_id) REFERENCES utilisateurs (id)');
        $this->addSql('CREATE INDEX IDX_4F684F86427EB8A5 ON collab_applications (request_id)');
        $this->addSql('DROP INDEX idx_candidate ON collab_applications');
        $this->addSql('CREATE INDEX IDX_4F684F8691BD8781 ON collab_applications (candidate_id)');
        $this->addSql('DROP INDEX idx_location ON collab_requests');
        $this->addSql('DROP INDEX idx_dates ON collab_requests');
        $this->addSql('DROP INDEX idx_status ON collab_requests');
        $this->addSql('ALTER TABLE collab_requests CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE description description LONGTEXT NOT NULL, CHANGE requester_id requester_id INT NOT NULL');
        $this->addSql('ALTER TABLE collab_requests ADD CONSTRAINT FK_10CC4FA4ED442CF4 FOREIGN KEY (requester_id) REFERENCES utilisateurs (id)');
        $this->addSql('CREATE INDEX IDX_10CC4FA4ED442CF4 ON collab_requests (requester_id)');
        $this->addSql('DROP INDEX idx_proprietaire ON reservations');
        $this->addSql('DROP INDEX fk_reservation_annonce ON reservations');
        $this->addSql('DROP INDEX idx_statut ON reservations');
        $this->addSql('DROP INDEX idx_demandeur ON reservations');
        $this->addSql('ALTER TABLE reservations DROP caution, DROP message_demande, DROP reponse_proprietaire, DROP date_demande, DROP date_reponse, DROP contrat_url, DROP contrat_signe, DROP date_signature_contrat, DROP paiement_effectue, DROP date_paiement, DROP mode_paiement, CHANGE quantite quantite INT NOT NULL, CHANGE statut statut VARCHAR(20) NOT NULL, CHANGE date_creation date_creation DATETIME NOT NULL, CHANGE commission commission NUMERIC(10, 2) NOT NULL');
        $this->addSql('ALTER TABLE reservations ADD CONSTRAINT FK_4DA2398805AB2F FOREIGN KEY (annonce_id) REFERENCES annonces (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE annonces ADD marque VARCHAR(100) DEFAULT NULL, ADD modele VARCHAR(100) DEFAULT NULL, ADD annee_fabrication INT DEFAULT NULL, ADD date_debut_disponibilite DATE DEFAULT NULL, ADD date_fin_disponibilite DATE DEFAULT NULL, ADD avec_operateur TINYINT DEFAULT 0, ADD assurance_incluse TINYINT DEFAULT 0, ADD caution NUMERIC(10, 2) DEFAULT \'0.00\', ADD conditions_location TEXT DEFAULT NULL, ADD unite_quantite VARCHAR(20) DEFAULT \'kg\', CHANGE titre titre VARCHAR(255) NOT NULL, CHANGE description description TEXT DEFAULT NULL, CHANGE type type ENUM(\'LOCATION\', \'VENTE\') NOT NULL, CHANGE statut statut ENUM(\'DISPONIBLE\', \'RESERVEE\', \'LOUEE\', \'VENDUE\', \'EXPIREE\') DEFAULT \'DISPONIBLE\', CHANGE categorie categorie VARCHAR(100) DEFAULT NULL, CHANGE localisation localisation VARCHAR(255) DEFAULT NULL, CHANGE latitude latitude NUMERIC(10, 8) DEFAULT NULL, CHANGE longitude longitude NUMERIC(11, 8) DEFAULT NULL, CHANGE quantite_disponible quantite_disponible INT DEFAULT 0, CHANGE unite_prix unite_prix VARCHAR(20) DEFAULT \'jour\', CHANGE date_creation date_creation DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE date_modification date_modification DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('CREATE INDEX idx_proprietaire ON annonces (proprietaire_id)');
        $this->addSql('CREATE INDEX idx_statut ON annonces (statut)');
        $this->addSql('CREATE INDEX idx_categorie ON annonces (categorie)');
        $this->addSql('CREATE INDEX idx_prix ON annonces (prix)');
        $this->addSql('CREATE INDEX idx_type ON annonces (type)');
        $this->addSql('ALTER TABLE collab_applications DROP FOREIGN KEY FK_4F684F86427EB8A5');
        $this->addSql('ALTER TABLE collab_applications DROP FOREIGN KEY FK_4F684F8691BD8781');
        $this->addSql('DROP INDEX IDX_4F684F86427EB8A5 ON collab_applications');
        $this->addSql('ALTER TABLE collab_applications DROP FOREIGN KEY FK_4F684F8691BD8781');
        $this->addSql('ALTER TABLE collab_applications CHANGE id id BIGINT AUTO_INCREMENT NOT NULL, CHANGE motivation motivation TEXT NOT NULL, CHANGE expected_salary expected_salary NUMERIC(10, 2) DEFAULT \'0.00\', CHANGE request_id request_id BIGINT NOT NULL, CHANGE candidate_id candidate_id BIGINT DEFAULT 1 NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX unique_application ON collab_applications (request_id, candidate_id)');
        $this->addSql('CREATE INDEX idx_status ON collab_applications (status)');
        $this->addSql('DROP INDEX idx_4f684f8691bd8781 ON collab_applications');
        $this->addSql('CREATE INDEX idx_candidate ON collab_applications (candidate_id)');
        $this->addSql('ALTER TABLE collab_applications ADD CONSTRAINT FK_4F684F8691BD8781 FOREIGN KEY (candidate_id) REFERENCES utilisateurs (id)');
        $this->addSql('ALTER TABLE collab_requests DROP FOREIGN KEY FK_10CC4FA4ED442CF4');
        $this->addSql('DROP INDEX IDX_10CC4FA4ED442CF4 ON collab_requests');
        $this->addSql('ALTER TABLE collab_requests CHANGE id id BIGINT AUTO_INCREMENT NOT NULL, CHANGE description description TEXT NOT NULL, CHANGE requester_id requester_id BIGINT DEFAULT 1 NOT NULL');
        $this->addSql('CREATE INDEX idx_location ON collab_requests (location)');
        $this->addSql('CREATE INDEX idx_dates ON collab_requests (start_date, end_date)');
        $this->addSql('CREATE INDEX idx_status ON collab_requests (status)');
        $this->addSql('ALTER TABLE reservations DROP FOREIGN KEY FK_4DA2398805AB2F');
        $this->addSql('ALTER TABLE reservations ADD caution NUMERIC(10, 2) DEFAULT \'0.00\', ADD message_demande TEXT DEFAULT NULL, ADD reponse_proprietaire TEXT DEFAULT NULL, ADD date_demande DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, ADD date_reponse DATETIME DEFAULT NULL, ADD contrat_url VARCHAR(500) DEFAULT NULL, ADD contrat_signe TINYINT DEFAULT 0, ADD date_signature_contrat DATETIME DEFAULT NULL, ADD paiement_effectue TINYINT DEFAULT 0, ADD date_paiement DATETIME DEFAULT NULL, ADD mode_paiement VARCHAR(50) DEFAULT NULL, CHANGE quantite quantite INT DEFAULT 1, CHANGE commission commission NUMERIC(10, 2) DEFAULT \'0.00\' NOT NULL, CHANGE statut statut ENUM(\'EN_ATTENTE\', \'ACCEPTEE\', \'REFUSEE\', \'EN_COURS\', \'TERMINEE\', \'ANNULEE\') DEFAULT \'EN_ATTENTE\', CHANGE date_creation date_creation DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('CREATE INDEX idx_proprietaire ON reservations (proprietaire_id)');
        $this->addSql('CREATE INDEX fk_reservation_annonce ON reservations (annonce_id)');
        $this->addSql('CREATE INDEX idx_statut ON reservations (statut)');
        $this->addSql('CREATE INDEX idx_demandeur ON reservations (demandeur_id)');
    }
}
