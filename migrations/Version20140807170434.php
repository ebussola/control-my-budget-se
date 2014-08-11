<?php

namespace ControlMyBudgetMigration;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140807170434 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $table = $schema->getTable('monthly_goal');
        $table->addForeignKeyConstraint(
            $schema->getTable('user'),
            ['user_id'],
            ['id'],
            ['onDelete' => 'CASCADE']
        );

        $purchase_table = $schema->getTable('purchase');
        $purchase_table->addForeignKeyConstraint(
            $schema->getTable('user'),
            ['user_id'],
            ['id'],
            ['onDelete' => 'CASCADE']
        );
    }

    public function down(Schema $schema)
    {
        $table = $schema->getTable('monthly_goal');
        foreach ($table->getForeignKeys() as $fk) {
            if ($fk->getForeignTableName() == 'user') {
                $table->removeForeignKey($fk->getName());
            }
        }

        $table = $schema->getTable('purchase');
        foreach ($table->getForeignKeys() as $fk) {
            if ($fk->getForeignTableName() == 'user') {
                $table->removeForeignKey($fk->getName());
            }
        }
    }
}
