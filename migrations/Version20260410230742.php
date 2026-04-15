<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260410230742 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create collab_requests table and fix foreign key constraints';
    }

    public function up(Schema $schema): void
    {
        // Create collab_requests table
        $table = $schema->createTable('collab_requests');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('title', 'string', ['length' => 255]);
        $table->addColumn('description', 'text');
        $table->addColumn('requester_id', 'integer', ['notnull' => true]);
        $table->addColumn('location', 'string', ['length' => 255, 'notnull' => false]);
        $table->addColumn('start_date', 'date', ['notnull' => false]);
        $table->addColumn('end_date', 'date', ['notnull' => false]);
        $table->addColumn('status', 'string', ['length' => 50, 'default' => 'PENDING']);
        $table->addColumn('created_at', 'datetime', ['notnull' => false]);
        $table->addColumn('updated_at', 'datetime', ['notnull' => false]);
        $table->setPrimaryKey(['id']);
        $table->addIndex(['requester_id'], 'IDX_10CC4FA4ED442CF4');
        $table->addForeignKeyConstraint('utilisateurs', ['requester_id'], ['id'], [], 'FK_10CC4FA4ED442CF4');
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable('collab_requests');
    }
}
