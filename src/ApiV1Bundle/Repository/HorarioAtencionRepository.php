<?php
namespace ApiV1Bundle\Repository;

/**
 * Class HorarioAtencionRepository
 * @package ApiV1Bundle\Repository
 *
 * Esta clase es generada por el ORM. Se pueden agregar debajo los métodos que se requieran
 */
class HorarioAtencionRepository extends ApiRepository
{

    /**
     * @return \Doctrine\ORM\EntityRepository
     */

    private function getRepository()
    {
        return $this->getEntityManager()->getRepository('ApiV1Bundle:HorarioAtencion');
    }

    /**
     * Listado de horarios por punto de atención
     *
     * @param integer $puntoAtencionId identificador único de punto de Atención
     * @param integer $limit Cantidad máxima de registros a retornar
     * @param integer $offset Cantidad de registros a saltar
     * @return mixed
     */
    public function findAllPaginate($puntoAtencionId, $offset, $limit)
    {
        $query = $this->getRepository()->createQueryBuilder('h');
        $query->select(['h.id', 'h.horaInicio', 'h.horaFin', 'h.diaSemana']);
        $query->join('h.puntoAtencion', 'p');
        $query->where('p.id = :puntoAtencionId')->setParameter('puntoAtencionId', $puntoAtencionId);
        $query->setFirstResult($offset);
        $query->setMaxResults($limit);
        $query->orderBy('h.diaSemana', 'ASC');
        $query->addOrderBy('h.horaInicio', 'ASC');
        return $query->getQuery()->getResult();
    }

    /**
     * Número total de horarios de atención por punto de atención
     *
     * @param integer $puntoAtencionId identificador único de punto de Atención
     * @return integer
     */

    public function getTotal($puntoAtencionId)
    {
        $query = $this->getRepository()->createQueryBuilder('h');
        $query->select('count(h.id)');
        $query->join('h.puntoAtencion', 'p');
        $query->where('p.id = :puntoAtencionId')->setParameter('puntoAtencionId', $puntoAtencionId);
        $total = $query->getQuery()->getSingleScalarResult();
        return (int) $total;
    }

    /**
     * Siguiente RowId
     *
     * @return mixed
     */
    public function getNextRowId()
    {
        $query = $this->getRepository()->createQueryBuilder('h');
        $query->select('h.idRow');
        $query->orderBy('h.idRow', 'DESC');
        $query->setMaxResults(1);
        $result = $query->getQuery()->getResult();
        return (is_null($result) || empty($result)) ? 1 : $result[0]['idRow'] + 1;
    }

    /**
     * Retorna los días de la semana en los que atiende un Punto de Atención por row
     *
     * @param integer $puntoAtencionId identificador único de punto de Atención
     * @param integer $rowId identificador de fila de horario
     * @return array
     */
    public function getDiasSemanaByRow($puntoAtencionId, $rowId)
    {
        $diasSemana = [];
        $query = $this->getRepository()->createQueryBuilder('h');
        $query->select('h.diaSemana');
        $query->orderBy('h.diaSemana', 'DESC');
        $query->join('h.puntoAtencion', 'p');
        $query->where('p.id = :puntoAtencionId')->setParameter('puntoAtencionId', $puntoAtencionId);
        $query->andWhere('h.idRow = :rowId')->setParameter('rowId', $rowId);
        $result = $query->getQuery()->getResult();
        if ($result) {
            foreach ($result as $horario) {
                $diasSemana[] = $horario['diaSemana'];
            }
            return $diasSemana;
        }
        return [];
    }

    /**
     * Obtiene el rango horario agrupado por row id
     *
     * @param integer $puntoAtencionId identificador único de punto de Atención
     * @return mixed
     */
    public function getHorariosGroupByRowId($puntoAtencionId)
    {
        $query = $this->getRepository()->createQueryBuilder('h');
        $query->join('h.puntoAtencion', 'p')
            ->where('p.id = :puntoAtencionId')->setParameter('puntoAtencionId', $puntoAtencionId)
            ->groupBy('h.idRow');

        return $query->getQuery()->getResult();
    }

    /*********************************************************************************************************/
    /*                                     NO TOCAR. GRACIAS                                                 */
    /*********************************************************************************************************/

    /**
     * Obtiene la cantidad de turnos para un horario, fecha, punto de atención y tramite
     *
     * @param integer $puntoAtencionId identificador único de punto de Atención
     * @param integer $grupoTramiteId identificador único de grupoTramiteId
     * @param date $fecha fecha a buscar
     * @return mixed
     */
    public function getDisponibilidadFechaHora($puntoAtencionId, $grupoTramiteId, $fecha)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
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
            WHERE (ha.hora_inicio + INTERVAL via.acumulado MINUTE ) < ha.hora_fin AND vf.fecha_calendario = :fecha      
            AND pa.id = :puntoAtencion AND gt.id = :grupoTramite 
            AND disp.cantidad_turnos > 0
            ORDER BY fecha, punto_atencion, grupo_tramite, dia_semana, horario
        ';

        $stmt = $conn->prepare($sql);
        $stmt->execute(['puntoAtencion' => $puntoAtencionId, 'grupoTramite' => $grupoTramiteId, 'fecha' => $fecha]);

        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchAll();
    }

    /**
     * Obtiene la cantidad de turnos dados para una fecha y hora
     *
     * @param integer $puntoAtencionId identificador único de punto de Atención
     * @param integer $grupoTramiteId identificador único de grupoTramiteId
     * @param date $fecha fecha a buscar
     * @return mixed
     */
    public function getDisponibilidadHora($puntoAtencionId, $grupoTramiteId, $fecha)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = 'SELECT t.hora, COUNT(*) as turnos_dados
                FROM turno AS t
                WHERE t.punto_atencion_id = :puntoAtencion AND t.grupo_tramite_id = :grupoTramite AND t.fecha = :fecha  AND t.estado <>  2 AND (t.fecha_borrado IS NULL)                  
                GROUP BY t.hora
                ORDER BY t.hora';

        $stmt = $conn->prepare($sql);
        $stmt->execute(['puntoAtencion' => $puntoAtencionId, 'grupoTramite' => $grupoTramiteId, 'fecha' => $fecha]);

        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchAll();
    }
}
