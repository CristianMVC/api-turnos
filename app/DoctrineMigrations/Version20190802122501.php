<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20190802122501 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema){
        $this->addSql('CREATE TABLE IF NOT EXISTS  `dias_no_laborables_tramite` (
                        `id` int(11) NOT NULL AUTO_INCREMENT,
                        `punto_atencion_id` int(11) DEFAULT NULL,
                        `tramite_id` int(11) DEFAULT NULL,
                        `fecha` date NOT NULL,
                        `fecha_creado` datetime NOT NULL,
                        `fecha_modificado` datetime NOT NULL,
                        PRIMARY KEY (`id`),
                        KEY `IDX_punto_atencion_id` (`punto_atencion_id`),
                        KEY `IDX_tramite_id` (`tramite_id`),
                        CONSTRAINT `IDX_punto_atencion_id` FOREIGN KEY (`punto_atencion_id`) REFERENCES `punto_atencion` (`id`),
                        CONSTRAINT `IDX_tramite_id` FOREIGN KEY (`tramite_id`) REFERENCES `tramite` (`id`)
                      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 ');
   }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema){
         $this->addSql('DROP TABLE `dias_no_laborables_tramite`');
    }
}
