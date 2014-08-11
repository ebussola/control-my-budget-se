<?php

namespace ControlMyBudgetMigration;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140807162026 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $user_table = $schema->createTable('user');
        $user_table->addColumn('id', 'integer', ['autoincrement' => true]);
        $user_table->addColumn('email', 'string');
        $user_table->addColumn('name', 'string');
        $user_table->addColumn('facebook_user_id', 'string');
        $user_table->addIndex(['facebook_user_id']);
        $user_table->setPrimaryKey(['id']);
        $user_table->addUniqueIndex(['facebook_user_id', 'email']);
    }

    public function down(Schema $schema)
    {
        $schema->dropTable('user');
    }
}
