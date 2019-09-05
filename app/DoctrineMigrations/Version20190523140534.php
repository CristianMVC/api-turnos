<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20190523140534 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('CREATE TABLE `tramite_area` (
                       `tramite_id` int(11) NOT NULL,
                       `area_id` int(11) NOT NULL,
                        PRIMARY KEY (`tramite_id`,`area_id`),
                        KEY `IDX_AAF7F544820C2849` (`tramite_id`),
                        KEY `IDX_AAF7F544BD0F409C` (`area_id`),
                        CONSTRAINT `FK_AAF7F544820C2849` FOREIGN KEY (`tramite_id`) REFERENCES `tramite` (`id`) ON DELETE CASCADE,
                        CONSTRAINT `FK_AAF7F544BD0F409C` FOREIGN KEY (`area_id`) REFERENCES `area` (`id`) ON DELETE CASCADE);');

        $this->addSql('INSERT INTO tramite_area (tramite_id, area_id) SELECT id, area_id FROM tramite WHERE area_id IS NOT NULL;');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql('DROP TABLE `tramite_area`;');
    }
}
