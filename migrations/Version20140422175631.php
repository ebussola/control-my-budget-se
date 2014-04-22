<?php

namespace ControlMyBudgetMigration;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140422175631 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $table = $schema->getTable('purchase');
        $table->addColumn('hash', 'string');
        $table->addUniqueIndex(['hash']);
    }

    public function down(Schema $schema)
    {
        $table = $schema->getTable('purchase');
        $table->dropColumn('hash');

    }
}