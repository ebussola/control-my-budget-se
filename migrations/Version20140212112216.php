<?php

namespace ControlMyBudgetMigration;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140212112216 extends AbstractMigration {

    public function up(Schema $schema) {
        $table = $schema->createTable('monthly_goal');
        $table->addColumn('id', 'integer', array('autoincrement' => true));
        $table->addColumn('month', 'integer');
        $table->addColumn('year', 'integer');
        $table->addColumn('amount_goal', 'float');

        $table->setPrimaryKey(array('id'));
    }

    public function down(Schema $schema) {
        $schema->dropTable('monthly_goal');
    }

}