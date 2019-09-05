<?php
namespace ApiV1Bundle\Repository;

use ApiV1Bundle\Entity\Turno;
use ApiV1Bundle\Helper\ServicesHelper;

/**
 * Class TurnoRepository
 * @package ApiV1Bundle\Repository
 *
 * Esta clase es generada por el ORM. Se pueden agregar debajo los métodos que se requieran
 */
class TurnoRepository extends ApiRepository
{

    /**
     * @return \Doctrine\ORM\EntityRepository
     */

    private function getRepository()
    {
        return $this->getEntityManager()->getRepository('ApiV1Bundle:Turno');
    }

    /**
     * Listado de turnos paginado
     *
     * @param integer $limit Cantidad máxima de registros a retornar
     * @param integer $offset Cantidad de registros a saltar
     * @return mixed
     */
    public function findAllPaginate($offset = 0, $limit = 10, $where = [])
    {
        $query = $this->getRepository()->createQueryBuilder('t');
        
        if (!empty($where)) {
    
            $this->whereFilter($query, $where);
            
        }
    
        $query->setFirstResult($offset);
        $query->setMaxResults($limit);
        $query->orderBy('t.id', 'ASC');
    
    
        return $query->getQuery()->getResult();
    
    }
    
    protected function whereFilter(&$query, $where)
    {
    
        if (isset($where['puntoAtencionId'])) {
            $query->andWhere('t.puntoAtencion = :puntoAtencionId')->setParameter('puntoAtencionId', $where['puntoAtencionId']);
        }
    
        if (isset($where['areaId'])) {
            $query->join('t.puntoAtencion', 'p');
            $query->join('p.area', 'a');
            $query->andWhere('a.id = :areaId')->setParameter('areaId', $where['areaId']);
        }
    
        if (isset($where['tramiteId'])) {
            $query->andWhere('t.tramite = :tramiteId')->setParameter('tramiteId', $where['tramiteId']);
        }
    
        if (isset($where['fechaDesde'])) {
            $query->andWhere('t.fecha >= :fechaDesde')->setParameter('fechaDesde', $where['fechaDesde']);
        }
    
        if (isset($where['fechaHasta'])) {
            $query->andWhere('t.fecha <= :fechaHasta')->setParameter('fechaHasta', $where['fechaHasta']);
        }
    
        if (isset($where['fechaDesde'])) {
            $query->andWhere('t.fecha >= :fechaDesde')->setParameter('fechaDesde', $where['fechaDesde']);
        }
    
        if (isset($where['fechaHasta'])) {
            $query->andWhere('t.fecha <= :fechaHasta')->setParameter('fechaHasta', $where['fechaHasta']);
        }
    
        if (isset($where['estado'])) {
            $query->andWhere('t.estado = :estado')->setParameter('estado', $where['estado']);
        }
        
    }

    /**
     * Listado de turnos por ciudadano
     *
     * @param integer $documento documento del ciudadano
     * @param integer $limit Cantidad máxima de registros a retornar
     * @param integer $offset Cantidad de registros a saltar
     * @return mixed
     */
    public function findTurnosPorCiudadano($documento, $offset = 0, $limit = 10)
    {
        $query = $this->getRepository()->createQueryBuilder('t');
        $query->select([
            'd.cuil',
            't.id',
            't.fecha',
            't.hora',
            't.codigo'
        ]);
        $query->join('t.datosTurno', 'd');
        $query->join('t.tramite', 'tra');
        $query->where('t.estado = :estado')->setParameter('estado', 1);
        $query->andWhere('d.cuil = :documento')->setParameter('documento', $documento);
        $query->andWhere('t.fecha >= :fecha')->setParameter('fecha', date('Y-m-d'));
        $query->setFirstResult($offset);
        $query->setMaxResults($limit);
        return $query->getQuery()->getResult();
    }

    /**
     * Listado de turnos por ciudadano (DNI)
     *
     * @param integer $documento documento del ciudadano
     * @param integer $limit Cantidad máxima de registros a retornar
     * @param integer $offset Cantidad de registros a saltar
     * @return mixed
     */
    public function findTurnosPorCiudadanoDni($documento, $offset = 0, $limit = 10)
    {
        $query = $this->getRepository()->createQueryBuilder('t');
        $query->select([
            'd.cuil',
            't.id',
            't.fecha',
            't.hora',
            't.codigo'
        ]);
        $query->join('t.datosTurno', 'd');
        $query->where(
            $query->expr()->andX(
                $query->expr()->eq($query->expr()->substring('d.cuil', 3, 8), ':documento'),
                $query->expr()->eq('t.estado', 1),
                $query->expr()->gte('t.fecha', ':fecha')
            )
        );

        $query->setParameter('documento', $documento);
        $query->setParameter('fecha', date('Y-m-d'));

        $query->setFirstResult($offset);
        $query->setMaxResults($limit);
        return $query->getQuery()->getResult();
    }

    /**
     * total de turnos por ciudadano (DNI)
     *
     * @param integer $documento documento del ciudadano
     * @param integer $limit Cantidad máxima de registros a retornar
     * @param integer $offset Cantidad de registros a saltar
     * @return integer
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getTotalCiudadanoDni($documento, $offset = 0, $limit = 10)
    {
        $query = $this->getRepository()->createQueryBuilder('t');
        $query->select('count(t.id)');
        $query->join('t.datosTurno', 'd');
        $query->where(
            $query->expr()->andX(
                $query->expr()->eq($query->expr()->substring('d.cuil', 3, 8), ':documento'),
                $query->expr()->eq('t.estado', 1),
                $query->expr()->gte('t.fecha', ':fecha')
            )
        );
        $query->setParameter('documento', $documento);
        $query->setParameter('fecha', date('Y-m-d'));
        return (int)$query->getQuery()->getSingleScalarResult();
    }

    /**
     * Número total de turnos por ciudadano
     *
     * @param integer $cuil cuil del ciudadano
     * @return int
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getTotalCiudadano($cuil, $puntoAtencionId = null)
    {
        $query = $this->getRepository()->createQueryBuilder('t');
        $query->select('count(t.id)');
        $query->join('t.datosTurno', 'd');
        $query->join('t.puntoAtencion', 'p');
        $query->where('t.estado = :estado')->setParameter('estado', 1);
        if ($puntoAtencionId) {
            $query->andWhere('p.id = :puntoAtencionId')->setParameter('puntoAtencionId', $puntoAtencionId);
        }
        $query->andWhere('d.cuil = :cuil')->setParameter('cuil', $cuil);
        $query->andWhere('t.fecha >= :fecha')->setParameter('fecha', date('Y-m-d'));
        $total = $query->getQuery()->getSingleScalarResult();
        return (int)$total;
    }

    /**
     * Número total de turnos
     *
     * @param integer $puntoAtencionId identificador único de punto de atención
     * @param integer $tramiteId  identificador único de trámite
     * @return int
     */
    public function getTotal($where = [])
    {
        $query = $this->getRepository()->createQueryBuilder('t');
        $query->select('count(t.id)');
        
        if (!empty($where)) {
    
            $this->whereFilter($query, $where);
            
        }
        
        return (int) $query->getQuery()->getSingleScalarResult();
    }

    /**
     * Busqueda de turnos por cuil y código
     *
     * @param string $documento documento del ciudadano
     * @param string $codigo código del turno
     * @return mixed
     */
    public function search($documento, $codigo)
    {
        $query = $this->getRepository()->createQueryBuilder('t');
        $query->join('t.datosTurno', 'd');
        $query->where('d.cuil = :documento')->setParameter('documento', $documento);
        $query->andWhere('lower(t.codigo) LIKE :codigo')->setParameter('codigo', strtolower($codigo) . '%');
        $query->andWhere('t.estado = :turnoEstado')->setParameter('turnoEstado', Turno::ESTADO_ASIGNADO);
        return $query->getQuery()->setMaxResults(1)->getOneOrNullResult();
    }

    /**
     * Busqueda de turnos por código
     *
     * @param string $codigo código del turno
     * @param integer $puntoAtencionId identificador único de punto de atención
     * @param integer $limit Cantidad máxima de registros a retornar
     * @param integer $offset Cantidad de registros a saltar
     * @return mixed
     */
    public function searchByCodigo($codigo, $puntoAtencionId, $offset = null, $limit = null)
    {
        $fecha = new \DateTime('now');
        $fecha = $fecha->format('Y-m-d');

        $query = $this->getRepository()->createQueryBuilder('t');
        $query->join('t.datosTurno', 'd');
        $query->join('t.puntoAtencion', 'p');
        $query->where('lower(t.codigo) LIKE :codigo')->setParameter('codigo', strtolower($codigo) . '%');
        $query->andWhere('p.id = :puntoAtencionId')->setParameter('puntoAtencionId', $puntoAtencionId);
        $query->andWhere('t.fecha >= :fecha')->setParameter('fecha', $fecha);
        $query->andWhere('t.estado = :turnoEstado')->setParameter('turnoEstado', Turno::ESTADO_ASIGNADO);

        if ($limit) {
            $query->setMaxResults($limit);
        }

        if ($offset) {
            $query->setFirstResult($offset);
        }

        return $query->getQuery()->getResult();
    }

    /**
     * Busqueda de turnos por código
     *
     * @param string $cuil cuil del ciudadano
     * @param integer $puntoAtencionId identificador único de punto de atención
     * @param integer $limit Cantidad máxima de registros a retornar
     * @param integer $offset Cantidad de registros a saltar
     * @return mixed
     */
    public function searchByCuil($cuil, $puntoAtencionId, $offset = null, $limit = null)
    {
        $fecha = new \DateTime('now');
        // query
        $query = $this->getRepository()->createQueryBuilder('t');
        $query->join('t.datosTurno', 'd');
        $query->join('t.puntoAtencion', 'p');
        $query->where('d.cuil = :cuil')->setParameter('cuil', $cuil);
        $query->andWhere('p.id = :puntoAtencionId')->setParameter('puntoAtencionId', $puntoAtencionId);
        $query->andWhere('t.fecha >= :fecha')->setParameter('fecha', $fecha->format('Y-m-d'));
        $query->andWhere('t.estado = :turnoEstado')->setParameter('turnoEstado', Turno::ESTADO_ASIGNADO);
        $query->orderBy('t.fecha', 'asc');

        if ($limit) {
            $query->setMaxResults($limit);
        }

        if ($offset) {
            $query->setFirstResult($offset);
        }

        return $query->getQuery()->getResult();
    }

    /**
     * Buscar por código o cuil
     *
     * @param string $codigo código del turno
     * @param integer $puntoAtencionId identificador único de punto de atención
     * @param integer $limit Cantidad máxima de registros a retornar
     * @param integer $offset Cantidad de registros a saltar
     * @return mixed
     */
    public function searchByCodigoOCuil($codigo, $puntoAtencionId, $offset = null, $limit = null)
    {
        $fecha = new \DateTime('now');
        $fecha = $fecha->format('Y-m-d');

        $query = $this->getRepository()->createQueryBuilder('t');
        $query->join('t.datosTurno', 'd');
        $query->join('t.puntoAtencion', 'p');
        $query->where('p.id = :puntoAtencionId')->setParameter('puntoAtencionId', $puntoAtencionId);
        $query->andWhere('t.fecha >= :fecha')->setParameter('fecha', $fecha);
        $query->andWhere('t.estado = :turnoEstado')->setParameter('turnoEstado', Turno::ESTADO_ASIGNADO);
        $query->andWhere($query->expr()->orX(
            $query->expr()->like('t.codigo', ':like'),
            $query->expr()->eq('d.cuil', ':codigo')
        ));

        $query->setParameter('like', $codigo . '%');
        $query->setParameter('codigo', $codigo);

        if ($limit) {
            $query->setMaxResults($limit);
        }
        if ($offset) {
            $query->setFirstResult($offset);
        }

        $query->orderBy('t.fecha', 'asc');

        return $query->getQuery()->getResult();
    }

    /**
     * total de la Busqueda por código o cuil
     *
     * @param string $codigo código del turno
     * @param integer $puntoAtencionId identificador único de punto de atención
     * @return int
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getTotalByCodigoOCuil($codigo, $puntoAtencionId)
    {
        $fecha = new \DateTime('now');
        $fecha = $fecha->format('Y-m-d');

        $query = $this->getRepository()->createQueryBuilder('t');
        $query->select('count(t.id)');
        $query->join('t.datosTurno', 'd');
        $query->join('t.puntoAtencion', 'p');
        $query->where('p.id = :puntoAtencionId')->setParameter('puntoAtencionId', $puntoAtencionId);
        $query->andWhere('t.fecha >= :fecha')->setParameter('fecha', $fecha);
        $query->andWhere('t.estado = :turnoEstado')->setParameter('turnoEstado', Turno::ESTADO_ASIGNADO);
        $query->andWhere($query->expr()->orX(
            $query->expr()->like('t.codigo', ':like'),
            $query->expr()->eq('d.cuil', ':codigo')
        ));

        $query->setParameter('like', $codigo . '%');
        $query->setParameter('codigo', $codigo);

        return (int)$query->getQuery()->getSingleScalarResult();
    }

    /**
     * Elimina los turnos que tienen más de 5 minutos de vida
     */
    public function deleteTurnosExpirados()
    {

        $sql = '
        UPDATE turno AS t SET t.fecha_borrado = NOW()
            WHERE t.estado = 0 AND t.fecha_creado < (now() - INTERVAL 5 MINUTE) AND t.fecha_borrado IS NULL ;
        ';

        $em = $this->getEntityManager();
        $stmt = $em->getConnection()->prepare($sql);
        $stmt->execute();
    }

    /**
     * Obtener el último turno de un punto de atencion
     *
     * @param integer $puntoAtencionId identificador único de punto de atención
     * @param integer $grupoTramitesId identificador único del grupo trámites
     * @param integer $tramiteId identificador único del trámite
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findUltimoTurno($puntoAtencionId = null, $grupoTramitesId = null, $tramiteId = null)
    {
        $query = $this->getRepository()->createQueryBuilder('t');
        $query->select(['t.id', 't.fecha, t.hora']);
        $query->where('t.estado = :turnoEstado')->setParameter('turnoEstado', 1);

        if ($tramiteId) {
            $query->andWhere('t.tramite = :tramiteId')->setParameter('tramiteId', $tramiteId);
        }
        if ($puntoAtencionId) {
            $query->andWhere('t.puntoAtencion = :puntoAtencionId')->setParameter('puntoAtencionId', $puntoAtencionId);
        }
        if ($grupoTramitesId) {
            $query->andWhere('t.grupoTramite = :grupoTramitesId')->setParameter('grupoTramitesId', $grupoTramitesId);
        }
        $query->orderBy('t.fecha', 'DESC');
        $query->addOrderBy('t.hora', 'DESC');
        return $query->getQuery()->setMaxResults(1)->getOneOrNullResult();
    }


    /**
     * Obtener el último trámite de un grupo de tramites
     *
     * @param integer $grupoTramiteId identificador único del grupo trámites
     * @return mixed
     */
    public function findUltimoTurnoByGrupoTramite($grupoTramiteId)
    {
        // Se agrega fecha del día de hoy para ignorar turnos viejos al eliminar
        // un grupo de trámite
        $fecha = new \DateTime('now');
        // query
        $query = $this->getRepository()->createQueryBuilder('t');
        $query->select(['t.id', 't.fecha, t.hora']);
        $query->where('t.grupoTramite = :grupoTramiteId')->setParameter('grupoTramiteId', $grupoTramiteId);
        $query->andWhere('t.estado = :turnoEstado')->setParameter('turnoEstado', 1);
        $query->andWhere('t.fecha >= :fecha')->setParameter('fecha', $fecha->format('Y-m-d'));
        $query->orderBy('t.fecha', 'DESC');
        $query->addOrderBy('t.hora', 'DESC');
        return $query->getQuery()->setMaxResults(1)->getOneOrNullResult();
    }

    /**
     * Obtener los turnos para un punto de atencion, tramite, fecha y cuil
     *
     * @param integer $puntoAtencionId identificador único de punto de atención
     * @param integer $tramiteId identificador único del trámite
     * @param date $fecha fecha a buscar
     * @param string $cuil cuil del ciudadano
     * @return mixed
     */
    public function findTurnosByPuntoTramiteFechaCuil($puntoAtencionId, $tramiteId, $fecha, $cuil)
    {
        $query = $this->getRepository()->createQueryBuilder('t');
        $query->select(['t.id', 't.fecha, t.hora']);
        $query->join('t.datosTurno', 'd');
        $query->where('t.estado = :turnoEstado')->setParameter('turnoEstado', 1);
        $query->andWhere('t.puntoAtencion = :puntoAtencionId')->setParameter('puntoAtencionId', $puntoAtencionId);
        $query->andWhere('t.tramite = :tramiteId')->setParameter('tramiteId', $tramiteId);
        $query->andWhere('t.fecha = :fecha')->setParameter('fecha', $fecha);
        $query->andWhere('d.cuil = :cuil')->setParameter('cuil', $cuil);
        return $query->getQuery()->getResult();
    }

    
    /**
     * TODO: Se obtiene los primeros 8 carateres del codigo del turno con la funcion SUBSTRING()
     *
     *  Obtener turnos por fecha y punto de atencion
     *
     * @param integer $puntoAtencionId identificador único de punto de atención
     * @param date $fecha fecha de los turnos a buscar
     * @param integer $limit Cantidad máxima de registros a retornar
     * @param integer $offset Cantidad de registros a saltar
     * @param string $codigosTurnos lista de codigos de turnos
     * @return mixed
     */
    public function findTurnosBySnc($puntoAtencionId, $fecha, $offset, $limit, $codigosTurnos, $params = null)
    {
        $fecha = new \DateTime($fecha);
        // query
        $query = $this->getRepository()->createQueryBuilder('t');
        $query->select([
            't.id as id',
            'p.id as punto_atencion',
            'dt.campos as campos',
            't.fecha',
            't.hora',
            't.estado',
            'tr.nombre as tramite',
            'tr.excepcional as excepcional',
            'SUBSTRING(t.codigo, 1 , 8) as codigo'
        ]);
        $query->join('t.puntoAtencion', 'p');
        $query->join('t.tramite', 'tr');
        $query->join('t.datosTurno', 'dt');
        $query->where('p.id = :puntoAtencionId')->setParameter('puntoAtencionId', (int) $puntoAtencionId);
        $query->andWhere('t.fecha = :fecha')->setParameter('fecha', $fecha->format('Y-m-d'));
        $estado = isset($params['estado'])?$params['estado']:Turno::ESTADO_ASIGNADO;
        if($estado != "-1"){
            $query->andWhere('t.estado = :estado')->setParameter('estado', $estado);
            if (count($codigosTurnos) > 0) {
                $query->andWhere('t.codigo not in (:codigos)')->setParameter('codigos', $codigosTurnos);
            }
        }
        $query->setFirstResult($offset);
        if($limit != "-1"){
            $query->setMaxResults($limit);
        }

        $query->orderBy('t.hora', 'ASC');

	$result =  $query->getQuery()->getResult();

        if(count($result)) {
         if (strlen($result[0]['campos']['cuil']) <= 9) {
             $result[0]['campos']['documento'] = $result[0]['campos']['cuil'];
             unset($result[0]['campos']['cuil']);

            }
          }

        return  $result;

    }

    /**
     * Número total de turnos
     *
     * @param integer $puntoAtencionId identificador único de punto de atención
     * @param type $fecha Fecha para buscar los turnos
     * @param array $params
     * @return integer
     */
    public function getTotalTurnos($puntoAtencionId, $fecha, $codigosTurnos = null, $params = [])
    {

        $fecha = new \DateTime($fecha);
        $fechaFormat = $fecha->format('Y-m-d');


        $query = $this->getRepository()->createQueryBuilder('t');
        $query->select('count(t.id)');
        $query->join('t.puntoAtencion', 'p');
        $query->where('p.id = :puntoAtencionId')->setParameter('puntoAtencionId', $puntoAtencionId);
        $query->andWhere('t.fecha = :fecha')->setParameter('fecha', $fechaFormat);
        $estado = isset($params['estado'])?$params['estado']:Turno::ESTADO_ASIGNADO;
        if($estado != "-1"){
            $query->andWhere('t.estado = :estado')->setParameter('estado', $estado);
            if (count($codigosTurnos) > 0) {
                $query->andWhere('t.codigo not in (:codigos)')->setParameter('codigos', $codigosTurnos);
            }
        }    
        
        $total = $query->getQuery()->getSingleScalarResult();
        return (int) $total;
    }

    /**
     * Obtener turnos por fecha y punto de atención
     *
     * @param \DateTime $fecha fecha a buscar
     * @param integer $puntoAtencionId identificador único de punto de atención
     * @return mixed
     */
    public function getTurnosByFecha($fecha, $puntoAtencionId = null)
    {
        $query = $this->getRepository()->createQueryBuilder('t');
        $query->select([
            't.id',
            't.fecha',
            't.hora',
            'dt.nombre',
            'dt.apellido',
            'dt.email',
            'dt.cuil',
            'p.nombre as lugar',
            'p.direccion',
            'tr.nombre as tramite',
            'tr.id as tramite_id'
        ]);
        $query->join('t.puntoAtencion', 'p');
        $query->join('t.datosTurno', 'dt');
        $query->join('t.tramite', 'tr');
        $query->where('t.fecha = :fecha')->setParameter('fecha', $fecha->format('Y-m-d'));
        $query->andWhere('p.id = :puntoAtencionId')->setParameter('puntoAtencionId', (int) $puntoAtencionId);
        $query->andWhere('t.estado = :estado')->setParameter('estado', Turno::ESTADO_ASIGNADO);
        $query->orderBy('t.id', 'ASC');
        return $query->getQuery()->getResult();
    }

    /**
     * Obtenemos el total de turnos por tramite
     *
     * @param integer $tramiteId identificador único de trámite
     * @param integer $grupoTramite identificador único de grupo trámite
     * @return integer
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findTotalTurnosByTramite($tramiteId, $grupoTramite = null)
    {
        $fecha = new \DateTime('now');
        // query
        $query = $this->getRepository()->createQueryBuilder('t');
        $query->select('count(t.id)');
        $query->where('t.estado = :turnoEstado')->setParameter('turnoEstado', 1);
        $query->andWhere('t.tramite = :tramiteId')->setParameter('tramiteId', $tramiteId);
        $query->andWhere('t.fecha >= :fecha')->setParameter('fecha', $fecha->format('Y-m-d'));

        if ($grupoTramite) {
            $query->andWhere('t.grupoTramite = :grupoTramiteId')
                ->setParameter('grupoTramiteId', $grupoTramite);
        }

        $total = $query->getQuery()->getSingleScalarResult();
        return (int) $total;
    }

    /**
     * Obtenemos los turnos dados por fecha, punto atencion y tramite
     *
     * @param integer $puntoAtencionId identificador único de punto de atención
     * @param integer $grupoTramiteId identificador único de grupo trámite
     * @param date $fecha fecha
     * @param time $hora hora
     * @return mixed
     */
    public function findTurnosDados($puntoAtencionId, $grupoTramiteId, $fecha, $hora = null)
    {
        $query = $this->getRepository()->createQueryBuilder('t');
        $query->select('count(t.id) AS turnos_dados');
        $query->where('t.fecha = :fecha')->setParameter('fecha', $fecha);
        $query->andWhere('t.grupoTramite = :grupoTramiteId')->setParameter('grupoTramiteId', (int) $grupoTramiteId);
        $query->andWhere('t.puntoAtencion = :puntoAtencionId')->setParameter('puntoAtencionId', (int) $puntoAtencionId);
        $query->andWhere('t.estado <> :estado')->setParameter('estado', Turno::ESTADO_CANCELADO);
        $query->andWhere('t.fechaBorrado IS NULL');
        if (isset($hora)) {
            $query->andWhere('t.hora = :hora')->setParameter('hora', $hora);
        }
        $query->groupBy('t.hora');
        $query->orderBy('t.hora', 'ASC');

        try {
            return (int) $query->getQuery()->getSingleScalarResult();
        } catch (\Exception $exception) {
            return 0;
        }
    }

    /**
     * buscar turnos pasados
     *
     * @return mixed
     */
    public function findTurnosPasados()
    {
        $ayer = (new \DateTime('yesterday'))->setTime(23, 59, 59);
        $query = $this->getRepository()->createQueryBuilder('t');
        $query->where('t.fecha <= :ayer')
            ->setParameter('ayer', $ayer);
        return $query->getQuery()->getResult();
    }

    /**
     * @param integer $puntoAtencionId
     * @param integer $grupoTramiteId
     * @param \DateTime $fecha
     * @return array
     */
    public function findTurnosAReasignar($puntoAtencionId, $grupoTramiteId, $fecha) {
        $today = new \DateTime('now');

        $query = $this->getRepository()->createQueryBuilder('t');
        $query->where('t.fecha = :fecha')->setParameter('fecha', $fecha);
        $query->andWhere('t.puntoAtencion = :puntoAtencionId')->setParameter('puntoAtencionId', $puntoAtencionId);
        $query->andWhere('t.grupoTramite = :grupoTramiteId')->setParameter('grupoTramiteId', $grupoTramiteId);
        $query->andWhere('t.estado = :estado')->setParameter('estado', 1);

        if ($today->format('Y-m-d') == $fecha->format('Y-m-d')) {
            $query->andWhere('t.hora >= :hora')->setParameter('hora', $today->format('H:m'));
        }

        $query->orderBy('t.hora', 'ASC');
        return $query->getQuery()->getResult();
    }

    /**
     * Obtener listado de turnos a reasignar agrupados por grupo de trámites
     * @param $puntoAtencionId
     * @param $fecha
     * @return array
     */
    public function findTurnosByPuntoAtencionFecha($puntoAtencionId, $fecha)
    {
        $today = new \DateTime('now');

        $query = $this->getRepository()->createQueryBuilder('t');
        $query->select('count(t.id) AS total_turnos, g.id, g.nombre');
        $query->join('t.grupoTramite', 'g');
        $query->where('t.fecha = :fecha')->setParameter('fecha', $fecha);
        $query->andWhere('t.puntoAtencion = :puntoAtencionId')->setParameter('puntoAtencionId', (int) $puntoAtencionId);
        $query->andWhere('t.estado = :estado')->setParameter('estado', 1);

        if ($today->format('Y-m-d') == $fecha) {
            $query->andWhere('t.hora >= :hora')->setParameter('hora', $today->format('H:m'));
        }

        $query->groupBy('t.grupoTramite');
        $query->orderBy('g.nombre', 'ASC');
        return $query->getQuery()->getResult();
    }

    /**
     * Obtener el total de turnos a reasignar agrupados por grupo de trámites
     * @param integer $puntoAtencionId identificador único de punto de atención
     * @param date $fecha fecha a buscar
     * @return integer
     */
    public function findTotalTurnosByPuntoAtencionFecha($puntoAtencionId, $grupoTramiteId, $fecha)
    {
        $today = new \DateTime('now');

        $query = $this->getRepository()->createQueryBuilder('t');
        $query->select('count(t.id)');
        $query->where('t.fecha = :fecha')->setParameter('fecha', $fecha);
        $query->andWhere('t.puntoAtencion = :puntoAtencionId')->setParameter('puntoAtencionId', (int) $puntoAtencionId);
        $query->andWhere('t.grupoTramite = :grupoTramiteId')->setParameter('grupoTramiteId', (int) $grupoTramiteId);
        $query->andWhere('t.estado = :estado')->setParameter('estado', 1);

        if ($today->format('Y-m-d') == $fecha) {
            $query->andWhere('t.hora >= :hora')->setParameter('hora', $today->format('H:m'));
        }

        $total = $query->getQuery()->getSingleScalarResult();
        return (int) $total;
    }
    /**
     * Busqueda de turnos por código
     *
     * @param string $codigo código del turno
     * @return mixed
     */
    public function searchOneByCodigo($codigo)
    {
        $query = $this->getRepository()->createQueryBuilder('t');
        $query->where('lower(t.codigo) LIKE :codigo')->setParameter('codigo', strtolower($codigo) . '%');

        return $query->getQuery()->getOneOrNullResult();
    }
    
    /**
     * Si tiene habilitado multiple, se debe cuantos turnos tienen en el horizonte
     * @param type $puntoTramite
     * @param type $turno
     * @param type $cuil
     * @return type
     */
    public function verificarMultipleTramite($puntoTramite, $turno, $tramite_horizonte, $cuil, $fecha=false, $tramiteId=false, $puntoAtencionId=false)
    {
        if($turno){
            $tramite = $turno->getTramite();
            $fecha = $turno->getFecha();
        }
        if(!$puntoTramite->getMultiple()){
            return false;
        }
        $horizonte = $tramite_horizonte['horizonte'];
        
        $fecha_horizonte =  new \DateTime("now");
        $fecha_horizonte->add( new \DateInterval('P'.trim($horizonte).'D'));
        if( $fecha > $fecha_horizonte){
            return false;
        }
        $intervalo_dias = $puntoTramite->getMultipleHorizonte();        
        $fechas_intervalo = $this->obtenerIntervalo( $intervalo_dias, $turno, $horizonte, $fecha );
        if(!$fechas_intervalo){
            return false;
        }
        // si la fecha NO esta dentro del horizonte retorna false;
        if($fecha > $fechas_intervalo->fin){
            return false;
        }
        if(!$puntoAtencionId){
            $puntoAtencionId = $puntoTramite->getPuntoAtencion()->getId();
        }
        if(!$tramiteId){
            $tramiteId = $tramite->getId();
        }
        
        $query = $this->getRepository()->createQueryBuilder('t');
        $query->select('count(t.id)');
        $query->join('t.datosTurno', 'd');
        $query->where('t.estado = :turnoEstado')->setParameter('turnoEstado', 1);
        $query->andWhere('t.puntoAtencion = :puntoAtencionId')->setParameter('puntoAtencionId', $puntoAtencionId);
        $query->andWhere('t.tramite = :tramiteId')->setParameter('tramiteId', $tramiteId);
        $query->andWhere('t.fecha >= :fecha')->setParameter('fecha', $fechas_intervalo->inicio->format('Y-m-d'));
        $query->andWhere('t.fecha <= :fecha_horizonte')->setParameter('fecha_horizonte', $fechas_intervalo->fin->format('Y-m-d'));
        $query->andWhere('d.cuil = :cuil')->setParameter('cuil', $cuil);
        $total_turnos =  $query->getQuery()->getSingleScalarResult();
        if($total_turnos < $puntoTramite->getMultipleMax()){
            if(!$puntoAtencionId){
                $puntoAtencionId = $turno->getPuntoAtencion()->getId();
            }
            if(!$tramiteId){
                $tramiteId = $turno->getTramite()->getId();
            }
            if($turno){
                $fecha = $turno->getFecha();
            }
            $verificacionTurno = $this->findTurnosByPuntoTramiteFechaCuil(
                $puntoAtencionId,
                $tramiteId,
                $fecha,
                ServicesHelper::buildValidDocument($cuil)
            );
            if(count($verificacionTurno)){
                return false;
            }
            return true;
        }    
        return false;
    }
    /**
     * 
     * @param type $puntoTramite
     * @param type $turno
     * @param type $cuil
     * @return boolean
     */
    public function verificarPermiteOtro($puntoTramite, $turno,  $cuil, $fecha=false, $tramiteId=false, $puntoAtencionId=false)
    {
        if($turno){
            $tramite = $turno->getTramite();
            $fecha  = $turno->getFecha();
        }
        if(!$puntoTramite->getPermiteOtro()){
            return false;
        }
        if(!$puntoAtencionId){
            $puntoAtencionId = $puntoTramite->getPuntoAtencion()->getId();
        }
        if(!$tramiteId){
            $tramiteId = $tramite->getId();
        }
        
        $query = $this->getRepository()->createQueryBuilder('t');
        $query->select('count(t.id)');
        $query->join('t.datosTurno', 'd');
        $query->where('t.estado = :turnoEstado')->setParameter('turnoEstado', 1);
        $query->andWhere('t.puntoAtencion = :puntoAtencionId')->setParameter('puntoAtencionId', $puntoAtencionId);
        $query->andWhere('t.tramite = :tramiteId')->setParameter('tramiteId', $tramiteId);
        $query->andWhere('t.fecha = :fecha')->setParameter('fecha', $fecha);
        $query->andWhere('d.cuil = :cuil')->setParameter('cuil', $cuil);
        $total_turnos =  $query->getQuery()->getSingleScalarResult();
        if($total_turnos < $puntoTramite->getPermiteOtroCantidad()){
            return true;
        }    
        return false;
    }
    /**
     * Obtener los turnos para un punto de atencion, tramite, fecha y cuil MAyor a hoy
     *
     * @param integer $puntoAtencionId identificador único de punto de atención
     * @param integer $tramiteId identificador único del trámite
     * @param date $fecha fecha a buscar
     * @param string $cuil cuil del ciudadano
     * @return mixed
     */
    public function findTurnosByPuntoTramiteCuil($puntoAtencionId, $tramiteId, $fecha, $cuil)
    {
        $fecha_actual = new \DateTime('now');
        $query = $this->getRepository()->createQueryBuilder('t');
        $query->select(['t.id', 't.fecha, t.hora']);
        $query->join('t.datosTurno', 'd');
        $query->where('t.estado = :turnoEstado')->setParameter('turnoEstado', 1);
        $query->andWhere('t.puntoAtencion = :puntoAtencionId')->setParameter('puntoAtencionId', $puntoAtencionId);
        $query->andWhere('t.tramite = :tramiteId')->setParameter('tramiteId', $tramiteId);
        $query->andWhere('t.fecha >= :fecha')->setParameter('fecha',  $fecha_actual->format('Y-m-d'));
        $query->andWhere('d.cuil = :cuil')->setParameter('cuil', $cuil);
        return $query->getQuery()->getResult();
    }
    
    
    /**
     * Obtiene fecha inicio y fecha fin del intervalo de fecha 
     * @param type $intervalo_dias
     * @param type $turno
     * @param type $horizonte
     * @return boolean
     */
    public function obtenerIntervalo( $intervalo_dias, $turno, $horizonte, $fecha = false ) {
        $inicio =  new \DateTime('now');
        $fin =  new \DateTime('now');
        $total = 0;
        
        while($horizonte > ($intervalo_dias*$total) ){
            $fin->add( new \DateInterval('P'.trim($intervalo_dias).'D'));
            if($turno){
               $fecha =  $turno->getFecha();
            }
            if($fecha < $fin){
                $intervalo= new \stdClass();
                $intervalo->inicio = $inicio;
                $intervalo->fin = $fin;
                return $intervalo;
            }
            $total++;
            $inicio->add( new \DateInterval('P'.trim($intervalo_dias+1).'D'));
        }
        return false;
        
    }


}
