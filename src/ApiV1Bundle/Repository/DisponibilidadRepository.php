<?php
namespace ApiV1Bundle\Repository;

/**
 * Class DisponibilidadRepository
 * @package ApiV1Bundle\Repository
 *
 * Esta clase es generada por el ORM. Se pueden agregar debajo los métodos que se requieran
 */
class DisponibilidadRepository extends ApiRepository
{
    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    private function getRepository()
    {
        return $this->getEntityManager()->getRepository('ApiV1Bundle:Disponibilidad');
    }

    /**
     * Obtener la disponibilidad por punto de atención y grupo de trámites
     * @param $puntoAtencionId
     * @param $grupoTramiteId
     */
    public function getDisponibilidadByPuntoAtencionGrupoTramite($puntoAtencionId, $grupoTramiteId)
    {
        $disponibilidadSQL = '
            SELECT
             ha.dia_semana AS diaSemana,
             (dis.cantidad_turnos * (EXTRACT(HOUR FROM ha.hora_fin - ha.hora_inicio)) *60/ gt.intervalo_tiempo) AS cantidadTurnos
            FROM grupo_tramite AS gt
            JOIN horario_atencion AS ha ON (ha.puntoatencion_id = gt.puntoAtencion_id)
            JOIN disponibilidad AS dis ON (dis.punto_atencion_id = gt.puntoAtencion_id AND dis.horario_atencion_id = ha.id)
            WHERE gt.id = :grupoTramite AND gt.puntoAtencion_id = :puntoAtencion AND ha.fecha_borrado IS NULL
            GROUP BY ha.hora_inicio, ha.hora_fin, ha.dia_semana
        ';
        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->prepare($disponibilidadSQL);
        $stmt->execute(['puntoAtencion' => $puntoAtencionId, 'grupoTramite' => $grupoTramiteId]);
        return $stmt->fetchAll();
    }

    /**
     * Obtenemos los horarios de atención por punto de atención y grupo de trámites
     * @param $puntoAtencionId
     * @param $grupoTramiteId
     * @return []
     */
    public function getHorariosAtencionBy($puntoAtencionId, $grupoTramiteId)
    {
        $query = $this->getRepository()->createQueryBuilder('dis');

        $query->where('dis.puntoAtencion = :puntoAtencion');
        $query->setParameter('puntoAtencion', $puntoAtencionId);
        $query->andWhere('dis.grupoTramite = :grupoTramite');
        $query->setParameter('grupoTramite', $grupoTramiteId);
        $query->andWhere('dis.cantidadTurnos > 0');

        $result = $query->getQuery()->getResult();

        $horariosAtencion = [];
        foreach ($result as $disponibilidad) {
            $horariosAtencion[] = $disponibilidad->getHorarioAtencion();
        }

        return $horariosAtencion;
    }
}
