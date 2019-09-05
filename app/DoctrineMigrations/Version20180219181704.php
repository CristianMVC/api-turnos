<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180219181704 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('
                CREATE OR REPLACE VIEW view_punto_atencion_fecha_disponible AS
                  SELECT
                    vf.fecha_calendario as fecha,
                    pa.id AS punto_atencion,
                    pa.localidad_id AS localidad_id,
                    pa.provincia_id AS provincia_id,
                    gt.id as grupo_tramite,
                    SUM(dis.cantidad_turnos * (EXTRACT(HOUR FROM ha.hora_fin - ha.hora_inicio)) / gt.intervalo_tiempo) - (SELECT COUNT(*) FROM turno WHERE turno.fecha = vf.fecha_calendario AND turno.grupo_tramite_id = gt.id AND turno.punto_atencion_id = pa.id AND (turno.estado = 0 OR turno.estado = 1) AND turno.fecha_borrado IS NULL) AS turnos_disponibles
                  FROM punto_atencion AS pa
                    INNER JOIN grupo_tramite AS gt ON pa.id = gt.puntoatencion_id
                    INNER JOIN horario_atencion AS ha ON pa.id = ha.puntoatencion_id
                    INNER JOIN disponibilidad AS dis ON pa.id = dis.punto_atencion_id AND ha.id = dis.horario_atencion_id AND gt.id = dis.grupo_tramite_id
                    INNER JOIN view_fechas AS vf ON vf.dia_semana = ha.dia_semana
                  WHERE vf.fecha_calendario >= CURRENT_DATE AND vf.fecha_calendario <= CURRENT_DATE + INTERVAL 90 DAY
                  GROUP BY punto_atencion, localidad_id, provincia_id, grupo_tramite, fecha
                  ORDER BY fecha, punto_atencion, grupo_tramite;
        ');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql('DROP VIEW IF EXISTS view_punto_atencion_fecha_disponible');
    }
}
