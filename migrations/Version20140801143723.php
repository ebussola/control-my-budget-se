<?php

namespace ControlMyBudgetMigration;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140801143723 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $table = $schema->getTable('purchase');
        $table->addColumn('is_forecast', 'boolean', ['default' => false]);

    }

    public function down(Schema $schema)
    {
        $table = $schema->getTable('purchase');
        $table->dropColumn('is_forecast');

    }
}
