<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20190815190642 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {

        $conn = $this->connection;
        $newColumn = "descripcion"; 
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
            $this->addSql('ALTER TABLE `tramite` ADD COLUMN `descripcion` VARCHAR(255) NULL DEFAULT NULL AFTER `categoria_tramite_id`');
        } else {
            $this->write(" Ya existe la columna  descripcion ");
        }

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql('ALTER TABLE tramite DROP descripcion;');

    }
}
