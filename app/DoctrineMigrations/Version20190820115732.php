<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20190820115732 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $conn = $this->connection;
        $newColumn = "cuil_solicitante";
        $tableName = "turno";
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
            $this->addSql('ALTER TABLE '.$tableName.' ADD COLUMN '.$newColumn.' VARCHAR(255) NULL;');
        } else {
            $this->write(" Ya existe la columna  cuil_solicitante ");
        }

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql('ALTER TABLE turno DROP cuil_solicitante');

    }
}
