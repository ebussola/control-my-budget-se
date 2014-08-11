<?php

namespace ControlMyBudgetMigration;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140807165358 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $monthly_goal_table = $schema->getTable('monthly_goal');
        $monthly_goal_table->addColumn('user_id', 'integer', ['default' => 1]);

        $purchase_table = $schema->getTable('purchase');
        $purchase_table->addColumn('user_id', 'integer', ['default' => 1]);

        $access_token = serialize(['access_token' => '111', 'expires' => time() + 86400]);
        $this->connection->insert(
            'user',
            [
                'id' => 1,
                'email' => 'leonardo@ebussola.com',
                'name' => 'Leonardo Shinagawa',
                'facebook_user_id' => '653238442',
                'facebook_access_token' => $access_token,
                'access_token' => '111'
            ]
        );
    }

    public function down(Schema $schema)
    {
        $monthly_goal_table = $schema->getTable('monthly_goal');
        $monthly_goal_table->dropColumn('user_id');

        $purchase_table = $schema->getTable('purchase');
        $purchase_table->dropColumn('user_id');
    }
}
