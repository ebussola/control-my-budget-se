<?php

namespace ControlMyBudgetMigration;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140212112408 extends AbstractMigration {

    public function up(Schema $schema) {
        $table = $schema->createTable('event');
        $table->addColumn('id', 'integer', array('autoincrement' => true));
        $table->addColumn('name', 'string');
        $table->addColumn('date_start', 'date');
        $table->addColumn('date_end', 'date');
        $table->addColumn('variation', 'float');
        $table->addColumn('category', 'string');
        $table->addColumn('monthly_goal_id', 'integer');

        $table->setPrimaryKey(array('id'));
        $table->addForeignKeyConstraint(
            $schema->getTable('monthly_goal'),
            array('monthly_goal_id'),
            array('id'),
            array('onDelete' => 'CASCADE')
        );
    }

    public function down(Schema $schema) {
        $schema->dropTable('event');
    }

}