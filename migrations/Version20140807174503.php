<?php

namespace ControlMyBudgetMigration;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140807174503 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $purchases = $this->connection->executeQuery('SELECT * FROM purchase')->fetchAll();
        foreach ($purchases as $purchase) {
            $hash = md5(
                join('.', [$purchase['date'], $purchase['place'], $purchase['amount'], '1'])
            );
            $this->connection->exec('UPDATE purchase SET hash="'.$hash.'" WHERE id='.$purchase['id']);
        }
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
