<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20190823114723 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $conn = $this->connection;
        $newColumn = "mi_argentina";
        $tableName = "tramite";
        $sql = " SHOW COLUMNS FROM ".$tableName." ";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $res  =  $stmt->fetchAll();
        $exist = false;

        foreach ($res as $key => $value) {
            if($newColumn == $value['Field']){
                $exist= true;
            }
        }

        if(!$exist){
            $this->addSql('ALTER TABLE '.$tableName.' ADD COLUMN '.$newColumn.'  TINYINT(1) NOT NULL DEFAULT 0');
        } else {
            $this->write(" Ya existe la columna  mi_argentina ");
        }


    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql('ALTER TABLE tramite DROP mi_argentina');

    }
}
