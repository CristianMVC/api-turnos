<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170921202015 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('DROP TABLE IF EXISTS view_fechas');
        $this->addSql('DROP TABLE IF EXISTS view_intervalos_acumulados');
        $this->addSql('DROP VIEW IF EXISTS view_punto_atencion_fecha_disponible');
        $this->addSql('DROP VIEW IF EXISTS view_punto_atencion_fecha_horarios');
        $this->addSql('DROP VIEW IF EXISTS view_punto_atencion_horario_disponible');

        $this->addSql('
                CALL generate_series_date_day(\'2017-01-01\', \'2022-12-31\', 1);

                -- CREAR TABLA view_fechas
                CREATE TABLE IF NOT EXISTS view_fechas AS
                  SELECT DATE(st.series) AS fecha_calendario, WEEKDAY(st.series) + 1 AS dia_semana
                  FROM series_tmp AS st;
        ');

        $this->addSql('
                -- CREAR VISTA view_punto_atencion_fecha_disponible
                CREATE OR REPLACE VIEW view_punto_atencion_fecha_disponible AS
                  SELECT
                    vf.fecha_calendario as fecha,
                    pa.id AS punto_atencion,
                    pa.localidad_id AS localidad_id,
                    pa.provincia_id AS provincia_id,
                    gt.id as grupo_tramite,
                    SUM(dis.cantidad_turnos * (EXTRACT(HOUR FROM ha.hora_fin - ha.hora_inicio)) / gt.intervalo_tiempo) - (select COUNT(*) from turno where turno.fecha = vf.fecha_calendario and turno.grupo_tramite_id = gt.id and turno.punto_atencion_id = pa.id) as turnos_disponibles
                  FROM punto_atencion AS pa
                    INNER JOIN grupo_tramite AS gt ON pa.id = gt.puntoatencion_id
                    INNER JOIN horario_atencion AS ha ON pa.id = ha.puntoatencion_id
                    INNER JOIN disponibilidad AS dis ON pa.id = dis.punto_atencion_id AND ha.id = dis.horario_atencion_id AND gt.id = dis.grupo_tramite_id
                    INNER JOIN view_fechas AS vf ON vf.dia_semana = ha.dia_semana
                  WHERE vf.fecha_calendario >= CURRENT_DATE AND vf.fecha_calendario <= CURRENT_DATE + INTERVAL 90 DAY
                  GROUP BY punto_atencion, localidad_id, provincia_id, grupo_tramite, fecha
                  ORDER BY fecha, punto_atencion, grupo_tramite;
        ');


        $this->addSql('
                -- CREAR TABLA view_intervalos_acumulados
                CREATE TABLE IF NOT EXISTS view_intervalos_acumulados (
                    intervalo_tiempo FLOAT,
                    acumulado SMALLINT
                );
                               
                -- CREAR VISTA view_punto_atencion_fecha_horarios
                CREATE OR REPLACE VIEW view_punto_atencion_fecha_horarios AS
                  SELECT
                    vf.fecha_calendario as fecha,
                    pa.id as punto_atencion,
                    gt.id as grupo_tramite,
                    ha.dia_semana,
                    (ha.hora_inicio + INTERVAL via.acumulado MINUTE ) as horario,
                    disp.cantidad_turnos AS cantidad_turnos
                  FROM horario_atencion ha
                    INNER JOIN punto_atencion pa ON ha.puntoatencion_id = pa.id
                    INNER JOIN grupo_tramite gt ON pa.id = gt.puntoAtencion_id
                    INNER JOIN view_intervalos_acumulados via ON via.intervalo_tiempo = gt.intervalo_tiempo
                    INNER JOIN disponibilidad AS disp ON ha.id = disp.horario_atencion_id AND gt.id = disp.grupo_tramite_id AND pa.id = disp.punto_atencion_id
                    INNER JOIN view_fechas AS vf ON vf.dia_semana = ha.dia_semana
                  WHERE (ha.hora_inicio + INTERVAL via.acumulado MINUTE ) < ha.hora_fin AND vf.fecha_calendario >= CURRENT_DATE AND vf.fecha_calendario <= CURRENT_DATE + INTERVAL 90 DAY
                  ORDER BY fecha, punto_atencion, grupo_tramite, dia_semana, horario;

                -- CREA INDICES EN TABLAS view_fechas Y view_intervalos_acumulados
                CREATE INDEX idx_dia_semana ON view_fechas (dia_semana);
                CREATE INDEX idx_intervalo_tiempo ON view_intervalos_acumulados (intervalo_tiempo);
        ');

        $int15 = range(0, 1440, 15);
        $int30 = range(0, 1440, 30);
        $int60 = range(0, 1440, 60);

        foreach ($int15 as $int) {
            $this->addSql('INSERT INTO view_intervalos_acumulados VALUES (0.25, '.$int.')');
        }

        foreach ($int30 as $int) {
            $this->addSql('INSERT INTO view_intervalos_acumulados VALUES (0.5, '.$int.')');
        }

        foreach ($int60 as $int) {
            $this->addSql('INSERT INTO view_intervalos_acumulados VALUES (1, '.$int.')');
        }

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
                      LEFT JOIN turno AS tur ON t.id = tur.tramite_id AND vpafh.punto_atencion = tur.punto_atencion_id AND vpafh.fecha = tur.fecha AND vpafh.horario = tur.hora
                    GROUP BY vpafh.punto_atencion, vpafh.grupo_tramite, vpafh.fecha, vpafh.horario, vpafh.cantidad_turnos
                    ORDER BY vpafh.punto_atencion, vpafh.grupo_tramite, vpafh.fecha, vpafh.horario
        ');

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql('DROP TABLE IF EXISTS view_fechas');
        $this->addSql('DROP TABLE IF EXISTS view_intervalo_acumulados');
        $this->addSql('DROP VIEW IF EXISTS view_punto_atencion_fecha_disponible');
        $this->addSql('DROP VIEW IF EXISTS view_punto_atencion_fecha_horarios');
        $this->addSql('DROP VIEW IF EXISTS view_punto_atencion_horario_disponible');

    }
}
