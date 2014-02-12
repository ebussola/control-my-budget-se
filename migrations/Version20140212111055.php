<?php

namespace ControlMyBudgetMigration;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140212111055 extends AbstractMigration {

    public function up(Schema $schema) {
        $table = $schema->createTable('purchase');
        $table->addColumn('id', 'integer', array('autoincrement' => true));
        $table->addColumn('date', 'date');
        $table->addColumn('place', 'string');
        $table->addColumn('amount', 'float');

        $table->setPrimaryKey(array('id'));
    }

    public function down(Schema $schema) {
        $schema->dropTable('purchase');
    }

}
