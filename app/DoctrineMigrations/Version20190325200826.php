<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20190325200826 extends AbstractMigration
{
    /**
     * php app/console doctrine:migrations:execute --up 20190325200826
     * 
     * @param Schema $schema
     * 
     */
    public function up(Schema $schema){
        $this->addSql('CREATE TABLE `datos_telefono_bck` ( `id` int(11) ) ENGINE=InnoDB ;');
        $this->addSql('CREATE TABLE `datos_telefonoh_bck` ( `id` int(11)) ENGINE=InnoDB ;');
        $this->addSql("INSERT INTO `datos_telefono_bck` (id) ( "
                . "SELECT id FROM  datos_turno "
                . " WHERE (telefono is null AND campos like '%fono\"%'));");
        $this->addSql("INSERT INTO `datos_telefonoh_bck` (id) ("
                . " SELECT id FROM  datos_turno_historico "
                . " where (telefono is null AND campos like '%fono\"%'));");
        $this->addSql("UPDATE datos_turno SET telefono=SUBSTRING_INDEX(SUBSTRING_INDEX(campos, 'fono\":\"', -1), '\"', 1) "
                . " WHERE (telefono is null AND campos LIKE '%fono\"%');");
        $this->addSql("UPDATE datos_turno_historico SET telefono=SUBSTRING_INDEX(SUBSTRING_INDEX(campos, 'fono\":\"', -1), '\"', 1) "
                . " WHERE (telefono is null AND campos LIKE '%fono\"%');");
    }
    /**
     * php app/console doctrine:migrations:execute --down 20190325200826
     * @param Schema $schema
     * 
     */
    public function down(Schema $schema){
        $this->addSql("UPDATE datos_turno dt "
                . " INNER JOIN `datos_telefono_bck` bkp ON dt.id = bkp.id "
                . " SET dt.telefono= null "
                . " WHERE dt.campos like '%fono\"%';");
        $this->addSql("UPDATE datos_turno_historico dth "
                . " INNER JOIN `datos_telefonoh_bck` bkp ON bkp.id = dth.id "
                . " SET dth.telefono=null "
                . " WHERE dth.campos like '%fono\"%';");
        $this->addSql('drop TABLE `datos_telefono_bck`;');
        $this->addSql('drop TABLE `datos_telefonoh_bck`;');
    }
}
