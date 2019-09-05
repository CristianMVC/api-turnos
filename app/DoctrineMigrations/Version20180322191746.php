<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180322191746 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {

        $this->addSql('
                -- CREA INDICES EN LA TABLA TURNOS para Fecha y Hora
                CREATE INDEX IF NOT EXISTS idx_hora ON turno (hora);
                CREATE INDEX IF NOT EXISTS idx_fecha ON turno (fecha);                
        ');

        $this->addSql('DROP VIEW IF EXISTS view_punto_atencion_fecha_disponible');
        $this->addSql('DROP VIEW IF EXISTS view_punto_atencion_fecha_horarios');
        $this->addSql('DROP VIEW IF EXISTS view_punto_atencion_horario_disponible');

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
