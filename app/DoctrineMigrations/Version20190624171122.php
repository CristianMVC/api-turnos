<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20190624171122 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // truncate table
        $this->addSql('TRUNCATE TABLE `view_intervalos_acumulados` ');
        
        $intervalo = [10,15,30,60];
        foreach ($intervalo as $key => $min) {
            $this->addrange($min);
        }
        // falta actualizar los grupo tramites
        $this->addSql(' UPDATE  `grupo_tramite` SET intervalo_tiempo = 15 WHERE intervalo_tiempo = 0.25  ');
        $this->addSql(' UPDATE  `grupo_tramite` SET intervalo_tiempo = 30 WHERE intervalo_tiempo = 0.5  ');
        $this->addSql(' UPDATE  `grupo_tramite` SET intervalo_tiempo = 60 WHERE intervalo_tiempo = 1  ');
       

    }
    
    function addrange($min, $fracc=null) {
        $range = range(0, 1440, $min);
        foreach ($range as $int) {
            
            if($fracc){
                $min= $fracc;
            }
            $this->addSql('INSERT INTO view_intervalos_acumulados VALUES ( '.$min.', '.$int.')');
        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql(' UPDATE  `grupo_tramite` SET intervalo_tiempo = 0.25 WHERE intervalo_tiempo = 15  ');
        $this->addSql(' UPDATE  `grupo_tramite` SET intervalo_tiempo = 0.5 WHERE intervalo_tiempo = 30  ');
        $this->addSql(' UPDATE  `grupo_tramite` SET intervalo_tiempo = 1 WHERE intervalo_tiempo = 60 ');
        
        $this->addSql('TRUNCATE TABLE `view_intervalos_acumulados` ');
        $intervalo = ["0.25"=>15,"0.5"=>30, 1=>60];
        foreach ($intervalo as $key => $min) {
            
            $this->addrange($min, $key);
        }
    }
}
