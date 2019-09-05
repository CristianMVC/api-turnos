<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180118174748 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('
                CREATE OR REPLACE VIEW view_punto_atencion_horarios_disponible AS
                    SELECT
                      vpafh.punto_atencion,
                      vpafh.grupo_tramite,
                      vpafh.fecha,
                      vpafh.horario,
                      vpafh.cantidad_turnos - COUNT(tur.id) as turnos_disponibles
                    FROM view_punto_atencion_fecha_horarios AS vpafh
                      INNER JOIN tramites_grupotramite AS tgt ON vpafh.grupo_tramite = tgt.grupo_tramite_id
                      INNER JOIN tramite AS t ON tgt.tramite_id = t.id
                      LEFT JOIN turno AS tur ON t.id = tur.tramite_id AND vpafh.punto_atencion = tur.punto_atencion_id AND vpafh.fecha = tur.fecha AND vpafh.horario = tur.hora AND (tur.estado = 0 OR tur.estado = 1) and tur.fecha_borrado IS NULL
                    GROUP BY vpafh.punto_atencion, vpafh.grupo_tramite, vpafh.fecha, vpafh.horario, vpafh.cantidad_turnos
                    ORDER BY vpafh.punto_atencion, vpafh.grupo_tramite, vpafh.fecha, vpafh.horario
        ');

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql('DROP VIEW IF EXISTS view_punto_atencion_horario_disponible');
    }
}
