-- Doctrine Migration File Generated on 2026-05-02 14:18:48

-- Version DoctrineMigrations\Version20260405000001
CREATE TABLE IF NOT EXISTS collab_requests (
            id             INT AUTO_INCREMENT NOT NULL,
            requester_id   INT NOT NULL,
            title          VARCHAR(150) NOT NULL,
            description    LONGTEXT NOT NULL,
            start_date     DATE NOT NULL,
            end_date       DATE NOT NULL,
            needed_people  INT NOT NULL,
            status         VARCHAR(20) NOT NULL DEFAULT 'open',
            location       VARCHAR(255) NOT NULL DEFAULT 'Non spécifié',
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
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`;
CREATE TABLE IF NOT EXISTS collab_applications (
            id                    INT AUTO_INCREMENT NOT NULL,
            request_id            INT NOT NULL,
            candidate_id          INT NOT NULL,
            full_name             VARCHAR(100) NOT NULL,
            phone                 VARCHAR(30) NOT NULL,
            email                 VARCHAR(180) NOT NULL,
            years_of_experience   INT NOT NULL,
            motivation            LONGTEXT NOT NULL,
            expected_salary       NUMERIC(10,2) NOT NULL DEFAULT 0,
            status                VARCHAR(20) NOT NULL DEFAULT 'pending',
            applied_at            DATETIME NOT NULL,
            updated_at            DATETIME NOT NULL,
            UNIQUE INDEX uq_candidate_request (candidate_id, request_id),
            INDEX idx_collab_app_status (status),
            INDEX IDX_collab_app_request   (request_id),
            INDEX IDX_collab_app_candidate (candidate_id),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`;
-- Version DoctrineMigrations\Version20260405000001 update table metadata;
INSERT INTO doctrine_migration_versions (version, executed_at, execution_time) VALUES ('DoctrineMigrations\\Version20260405000001', '2026-05-02 14:18:48', 0);
