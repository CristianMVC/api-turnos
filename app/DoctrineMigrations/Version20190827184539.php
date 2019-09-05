<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20190827184539 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $conn = $this->connection;
        $newColumn = "multiturno_cantidad"; 
        $tableName = "punto_tramite"; 
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
            $this->addSql('ALTER TABLE '.$tableName.' ADD COLUMN '.$newColumn.'  INT  DEFAULT 0;');
        } else {
            $this->write(" Ya existe la columna  multiturno_cantidad ");
        }
        

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql('ALTER TABLE punto_tramite DROP permite_prioridad;');

    }
}
