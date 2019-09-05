<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20190612153554 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('CREATE TABLE IF NOT EXISTS `categoria_del_tramite` (
                      `id` int(11) NOT NULL AUTO_INCREMENT,
                      `nombre` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
                       PRIMARY KEY (`id`))');

        $this->addSql('CREATE TABLE IF NOT EXISTS `etiqueta` (
                      `id` int(11) NOT NULL AUTO_INCREMENT,
                      `nombre` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
                       PRIMARY KEY (`id`))');
        
        $this->addSql('CREATE TABLE IF NOT EXISTS `etiquetas_tramites` (
                      `etiqueta_id` int(11) NOT NULL,
                      `tramite_id` int(11) NOT NULL,
                       PRIMARY KEY (`etiqueta_id`,`tramite_id`),
                       KEY `IDX_662F0C7BD53DA3AB` (`etiqueta_id`),
                       KEY `IDX_662F0C7B820C2849` (`tramite_id`),
                       CONSTRAINT `FK_662F0C7B820C2849` FOREIGN KEY (`tramite_id`) REFERENCES `tramite` (`id`) ON DELETE CASCADE,
                       CONSTRAINT `FK_662F0C7BD53DA3AB` FOREIGN KEY (`etiqueta_id`) REFERENCES `etiqueta` (`id`) ON DELETE CASCADE)');
        
        $this->addSql('alter table tramite add categoria_tramite_id int(11)');

        $this->addSql('alter table tramite  ADD CONSTRAINT `tramite_ibfk_1`  FOREIGN KEY (`categoria_tramite_id`) REFERENCES `categoria_del_tramite` (`id`)');

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
         $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
         $this->addSql('ALTER TABLE tramite DROP FOREIGN KEY tramite_ibfk_1');
         $this->addSql('DROP TABLE categoria_del_tramite');
         $this->addSql('DROP TABLE etiquetas_tramites');
         $this->addSql('DROP TABLE etiqueta');
         $this->addSql('ALTER TABLE tramite DROP categoria_tramite_id');

    }
}
