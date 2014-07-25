<?php

namespace ControlMyBudgetMigration;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140725143533 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $table = $schema->getTable('monthly_goal');
        $table->addColumn('is_deleted', 'boolean', ['default' => 0]);

    }

    public function down(Schema $schema)
    {
        $table = $schema->getTable('monthly_goal');
        $table->dropColumn('is_deleted');

    }
}
