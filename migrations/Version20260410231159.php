<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260410231159 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Fix collab_applications foreign key constraints';
    }

    public function up(Schema $schema): void
    {
        // Drop existing table if it exists to recreate with correct constraints
        if ($schema->hasTable('collab_applications')) {
            $schema->dropTable('collab_applications');
        }
        
        // Create collab_applications table with correct foreign key constraints
        $table = $schema->createTable('collab_applications');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('request_id', 'integer', ['notnull' => true]);
        $table->addColumn('candidate_id', 'integer', ['notnull' => true]);
        $table->addColumn('full_name', 'string', ['length' => 255]);
        $table->addColumn('phone', 'string', ['length' => 20]);
        $table->addColumn('email', 'string', ['length' => 100]);
        $table->addColumn('years_of_experience', 'integer', ['default' => 0]);
        $table->addColumn('motivation', 'text');
        $table->addColumn('expected_salary', 'decimal', ['precision' => 10, 'scale' => 2, 'default' => '0.00']);
        $table->addColumn('status', 'string', ['length' => 50, 'default' => 'PENDING']);
        $table->addColumn('applied_at', 'datetime', ['notnull' => false]);
        $table->addColumn('updated_at', 'datetime', ['notnull' => false]);
        $table->setPrimaryKey(['id']);
        $table->addIndex(['request_id'], 'IDX_4F684F86427EB8A5');
        $table->addIndex(['candidate_id'], 'IDX_4F684F8691BD8781');
        $table->addForeignKeyConstraint('collab_requests', ['request_id'], ['id'], ['onDelete' => 'CASCADE'], 'FK_4F684F86427EB8A5');
        $table->addForeignKeyConstraint('utilisateurs', ['candidate_id'], ['id'], [], 'FK_4F684F8691BD8781');
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable('collab_applications');
    }
}
