<?php

namespace ApiV1Bundle\Repository;
use PDO;
use ApiV1Bundle\ApplicationServices\RedisServices;

/**
 * Class PuntoAtencionRepository
 * @package ApiV1Bundle\Repository
 *
 * Esta clase es generada por el ORM. Se pueden agregar debajo los métodos que se requieran
 */

class PuntoAtencionRepository extends ApiRepository
{
    
     public function __construct( $em,  $class)
    {
        parent::__construct($em, $class);
    }

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    private function getRepository()
    {
        return $this->getEntityManager()->getRepository('ApiV1Bundle:PuntoAtencion');
    }

    /**
     * Listado de puntos de atención paginado
     *
     * @param integer $limit Cantidad máxima de registros a retornar
     * @param integer $offset Cantidad de registros a saltar
     * @param object $usuarioLogueado objeto usuario
     * @return mixed
     */
    public function findAllPaginate($offset, $limit, $usuarioLogueado)
    {
        $organismoID = $usuarioLogueado->getOrganismoId();
        $areaID = $usuarioLogueado->getAreaId();
        $puntoAtencionID = $usuarioLogueado->getPuntoAtencionId();

        $query = $this->getRepository()->createQueryBuilder('p');
        $query->select([
            'p.id',
            'p.nombre',
            'a.nombre as area',
            'r.nombre as provincia',
            'l.nombre as localidad',
            'p.direccion',
            'p.latitud',
            'p.longitud'
        ]);
        $query->join('p.area', 'a');
        $query->join('a.organismo', 'o');
        $query->join('p.provincia', 'r');
        $query->join('p.localidad', 'l');

        if (isset($organismoID)) {
            $query->where('o.id = :organismo')->setParameter('organismo', $organismoID);
        }

        if (isset($areaID)) {
            $query->andWhere('a.id = :area')->setParameter('area', $areaID);
        }

        if (isset($puntoAtencionID)) {
            $query->andWhere('p.id = :puntoAtencion')->setParameter('puntoAtencion', $puntoAtencionID);
        }

        $query->setFirstResult($offset);
        $query->setMaxResults($limit);
        $query->orderBy('p.id', 'ASC');
        return $query->getQuery()->getResult();
    }

    /**
     * Número total de puntos de atencion
     *
     * @param object $usuarioLogueado objeto usuario
     * @return integer
     */
    public function getTotal($usuarioLogueado)
    {
        $organismoID = $usuarioLogueado->getOrganismoId();
        $areaID = $usuarioLogueado->getAreaId();
        $puntoAtencionID = $usuarioLogueado->getPuntoAtencionId();

        $query = $this->getRepository()->createQueryBuilder('p');
        $query->select('count(p.id)')
            ->join('p.area', 'a')
            ->join('a.organismo', 'o');

        if (isset($organismoID)) {
            $query->where('o.id = :organismo')->setParameter('organismo', $organismoID);
        }

        if (isset($areaID)) {
            $query->andWhere('a.id = :area')->setParameter('area', $areaID);
        }

        if (isset($puntoAtencionID)) {
            $query->andWhere('p.id = :puntoAtencion')->setParameter('puntoAtencion', $puntoAtencionID);
        }
        $total = $query->getQuery()->getSingleScalarResult();
        return (int) $total;
    }

    /**
     * Lista de tramites por punto de atención
     *
     * @param integer $limit Cantidad máxima de registros a retornar
     * @param integer $offset Cantidad de registros a saltar
     * @param integer $puntoAtencionId identificador único de punto de Atención
     * @return mixed
     */
    public function findTramitesPaginated($puntoAtencionId, $limit, $offset)
    {
        $query = $this->getRepository()->createQueryBuilder('p');
        $query->select(['t.id', 't.nombre', 'a.nombre as area']);
        $query->join('p.tramites', 't');
        $query->join('t.area', 'a');
        $query->where('p.id = :puntoAtencionId')->setParameter('puntoAtencionId', $puntoAtencionId);
        $query->setFirstResult($offset);
        $query->setMaxResults($limit);
        $query->orderBy('t.nombre', 'ASC');
        return $query->getQuery()->getResult();
    }

    /**
     * Total tramites por punto de atención
     *
     * @param integer $puntoAtencionId identificador único de punto de Atención
     * @return integer
     */
    public function getTotalTramites($puntoAtencionId)
    {
        $query = $this->getRepository()->createQueryBuilder('p');
        $query->select('count(t.id)');
        $query->join('p.tramites', 't');
        $query->where('p.id = :puntoAtencionId')->setParameter('puntoAtencionId', $puntoAtencionId);
        $total = $query->getQuery()->getSingleScalarResult();
        return (int) $total;
    }

    /**
     * Comprobamos si un tramite lo puede realizar un punto de atención
     *
     * @param integer $puntoAtencionId identificador único de punto de Atención
     * @param integer $tramiteId identificador único de tramite
     * @return integer
     */
    public function checkTramiteRelationship($puntoAtencionId, $tramiteId)
    {
        $query = $this->getRepository()->createQueryBuilder('p');
        $query->select('count(p.id)');
        $query->join('p.tramites', 'pt');
        $query->join('pt.tramite', 't');
        $query->where('p.id = :puntoAtencionId')->setParameter('puntoAtencionId', $puntoAtencionId);
        $query->andWhere('t.id = :tramiteId')->setParameter('tramiteId', $tramiteId);
        $total = $query->getQuery()->getSingleScalarResult();
        return (int) $total;
    }

    /**
     * Buscar por Id y estado
     *
     * @param integer $id identofocador único de punto de atención
     * @param integer $estado Estado
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findFechaByIdEstado($id, $estado)
    {
        $query = $this->getRepository()->createQueryBuilder('p');
        $query->select(['t.id', 't.fecha']);
        $query->join('p.turnos', 't');
        $query->where('p.id = :puntoId')->setParameter('puntoId', $id);
        $query->andWhere('t.id = :');
        $query->andWhere('tu.estado = :estado')->setParameter('estado', $estado);
        $query->orderBy('tu.fecha', 'DESC');
        return $query->getQuery()->setMaxResults(1)->getOneOrNullResult();
    }

    /**
     * Busqueda de puntos de atención por nombre
     *
     * @param string $term cadena para buscar
     * @param integer $limit Cantidad máxima de registros a retornar
     * @param integer $offset Cantidad de registros a saltar
     * @return mixed
     */
    public function search($term, $offset, $limit)
    {
        $query = $this->getRepository()->createQueryBuilder('p');
        $query->select([
            'p.id',
            'p.nombre',
            'a.nombre as area',
            'r.nombre as provincia',
            'l.nombre as localidad',
            'p.direccion',
            'p.latitud',
            'p.longitud'
        ]);
        $query->join('p.area', 'a');
        $query->join('p.provincia', 'r');
        $query->join('p.localidad', 'l');
        $query->where('lower(p.nombre) LIKE :term');
        $query->orWhere('lower(a.nombre) LIKE :term');
        $query->orWhere('lower(p.direccion) LIKE :term');
        $query->setParameter('term', '%' . strtolower($term) . '%');
        $query->setFirstResult($offset);
        $query->setMaxResults($limit);
        $query->orderBy('p.id', 'ASC');
        return $query->getQuery()->getResult();
    }

    /**
     * Total items dentro de la busqueda
     *
     * @param string $term cadena para buscar
     * @return integer
     */
    public function getTotalSearch($term)
    {
        $query = $this->getRepository()->createQueryBuilder('p');
        $query->select('count(p.id)');
        $query->join('p.area', 'a');
        $query->where('lower(p.nombre) LIKE :term');
        $query->orWhere('lower(a.nombre) LIKE :term');
        $query->setParameter('term', '%' . strtolower($term) . '%');
        $total = $query->getQuery()->getSingleScalarResult();
        return (int) $total;
    }

    /**
     * Obtiene los puntos de atención por provincia y localidad
     *
     * @param integer $provinciaId identificador único de provincia
     * @param integer $localidadId identificador único de localidad
     * @return mixed
     */
    public function findPuntoAtencionBy($provinciaId, $localidadId)
    {
        $query = $this->getRepository()->createQueryBuilder('p');
        $query->where('p.provincia = :provincia')->setParameter('provincia', $provinciaId);
        $query->andWhere('p.localidad = :localidad')->setParameter('localidad', $localidadId);
        return $query->getQuery()->getResult();
    }

    /*********************************************************************************************************/
    /*                                     NO TOCAR. GRACIAS                                                 */
    /*********************************************************************************************************/

    /**
     * Obtiene las fechas disponibles desde hoy hasta la fecha horizonte, excluyendo las fechas de dias no laborables
     *
     * @param object $puntoAtencion objeto PuntoAtencion
     * @param object $tramite objeto trámite
     * @return mixed
     */
    public function getDisponibilidadFecha($puntoAtencion, $tramite, $puntoTramite=null, $params= null, RedisServices $redisServices = null, $diaNoLaborableTramiteRepository = null){
        
        /**
         * // solo para Testing
         * @todo Eliminar una vez pasado QA
         */
        
        if(!isset($params['caller'])){
            return $this->getDisponibilidadFechaV1($puntoAtencion, $tramite, $puntoTramite,$redisServices, $diaNoLaborableTramiteRepository);
        }

        $diasNoLaborables = $puntoAtencion->getDiasNoLaborables();
        $fechasNoDisponibles = [];

        // agregamos los días no laborables como fecha no disponible
        foreach ($diasNoLaborables as $diaNoLaborable) {
            $fechasNoDisponibles[] = $diaNoLaborable->getFecha()->format('Y-m-d');
        }
        
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT fecha, hora_inicio, hora_fin, capacidad, turnos_dados FROM
                (
                    SELECT 
                        vf.fecha_calendario as fecha, 
                        hora_inicio, 
                        hora_fin, 
                        SUM(dis.cantidad_turnos * (timestampdiff(MINUTE,ha.hora_inicio, ha.hora_fin)) / (gt.intervalo_tiempo)) as capacidad,
                        (
                            select COUNT(*) from turno where turno.fecha = vf.fecha_calendario and turno.grupo_tramite_id = gt.id
                            and turno.punto_atencion_id = :puntoAtencion and turno.estado != 2 and turno.fecha_borrado IS NULL
                        ) as turnos_dados
                    FROM punto_atencion AS pa
                    LEFT JOIN grupo_tramite AS gt ON pa.id = gt.puntoatencion_id
                    LEFT JOIN tramites_grupotramite AS tgt ON gt.id = tgt.grupo_tramite_id
                    LEFT JOIN tramite AS t ON tgt.tramite_id = t.id
                    LEFT JOIN horario_atencion AS ha ON pa.id = ha.puntoatencion_id
                    INNER JOIN disponibilidad AS dis ON pa.id = dis.punto_atencion_id AND ha.id = dis.horario_atencion_id AND gt.id = dis.grupo_tramite_id
                    LEFT JOIN view_fechas AS vf ON vf.dia_semana = ha.dia_semana
                    WHERE vf.fecha_calendario >= CURRENT_DATE AND vf.fecha_calendario <= CURRENT_DATE + INTERVAL gt.horizonte DAY
                    AND pa.id = :puntoAtencion AND t.id = :tramite
                    GROUP BY fecha
                    ORDER BY fecha
                )  AS fecha_disponibilidad
            WHERE capacidad - turnos_dados > 0;
        ';

        $stmt = $conn->prepare($sql);
        $stmt->execute(['puntoAtencion' => $puntoAtencion->getId(), 'tramite' => $tramite->getId()]);

        $arrayFechas =  $stmt->fetchAll();

        //remove diasNoLaborables
        foreach ($arrayFechas as $key => $value) {
            if (in_array($value['fecha'], $fechasNoDisponibles)) {
                unset($arrayFechas[$key]);
            }
        }

        $arrayFechas = array_values($arrayFechas);

        // returns an array of arrays (i.e. a raw data set)
        return $arrayFechas;
    }
    
     
    public function getDisponibilidadFechaV1($puntoAtencion, $tramite, $puntoTramite, $redisServices, $diaNoLaborableTramiteRepository){

        $diasNoLaborables = $puntoAtencion->getDiasNoLaborables();
        $fechasNoDisponibles = [];
        $DiaNoLaborableTramite = $diaNoLaborableTramiteRepository->findAllByTramitePda($puntoAtencion->getId(), $tramite->getId());
        // agregamos los días no laborables como fecha no disponible
        foreach ($diasNoLaborables as $diaNoLaborable) {
            $fechasNoDisponibles[] = $diaNoLaborable->getFecha()->format('Y-m-d');
        }
        foreach ($DiaNoLaborableTramite as $unDiaNoLaborableTramite) {
            //$fechasNoDisponiblesTramite[] = $unDiaNoLaborableTramite->getFecha()->format('Y-m-d');
            if (!in_array($unDiaNoLaborableTramite->getFecha()->format('Y-m-d'), $fechasNoDisponibles)) {
                $fechasNoDisponibles[] = $unDiaNoLaborableTramite->getFecha()->format('Y-m-d');
                continue;
            }
        }

        $conn = $this->getEntityManager()->getConnection();
        $arrayFechas_turnos_dados = $redisServices->getDispTurno($puntoAtencion->getId(), $tramite->getId());
            
        if(!$arrayFechas_turnos_dados){
            // Se cuentan los turnos dados dentro del horizonte del puntoAtencion
            $sql_pre = "SELECT  turno.fecha, COUNT(1) as turnos_dados
                        FROM turno
                        INNER JOIN grupo_tramite AS gt 
                            ON turno.grupo_tramite_id =  gt.id
                            AND  gt.puntoatencion_id = :puntoAtencion
                        INNER JOIN tramites_grupotramite tgt ON tgt.grupo_tramite_id = gt.id AND tgt.tramite_id = :tramite           
                        WHERE    
                            turno.punto_atencion_id = :puntoAtencion
                            AND turno.estado != 2
                            AND turno.fecha_borrado IS NULL
                            AND turno.fecha >= CURRENT_DATE
                            AND turno.fecha <= (CURRENT_DATE + INTERVAL gt.horizonte DAY)
                        GROUP BY turno.fecha";
            $stmt_pre = $conn->prepare($sql_pre);
            $stmt_pre->execute(['puntoAtencion' => $puntoAtencion->getId(), 'tramite' => $tramite->getId()]);
            // se  obtiene la disponibilidad dentro del horizonte
            $arrayFechas_turnos_dados =  $stmt_pre->fetchAll(PDO::FETCH_COLUMN | PDO::FETCH_GROUP);
            $redisServices->setDispTurno($puntoAtencion->getId(), $tramite->getId(), $arrayFechas_turnos_dados);
        }
        $arrayFechas = $redisServices->getDisp($puntoAtencion->getId(), $tramite->getId());
        
        if(!$arrayFechas ){
        
            $sql = 'SELECT 
                        vf.fecha_calendario as fecha,
                        ha.hora_inicio,
                        max(ha.hora_fin) as hora_fin,
                        sum(dis.cantidad_turnos * (TIMESTAMPDIFF(MINUTE, ha.hora_inicio, ha.hora_fin)) / (gt.intervalo_tiempo )) AS capacidad,
                        0 as turnos_dados
                    FROM horario_atencion ha 
                    INNER JOIN disponibilidad dis ON dis.horario_atencion_id = ha.id
                    INNER JOIN grupo_tramite gt ON gt.id = dis.grupo_tramite_id
                    LEFT JOIN tramites_grupotramite AS tgt ON gt.id = tgt.grupo_tramite_id
                    LEFT JOIN tramite AS t ON tgt.tramite_id = t.id
                    INNER JOIN view_fechas vf ON vf.dia_semana = ha.dia_semana
                    WHERE dis.punto_atencion_id = :puntoAtencion AND t.id = :tramite
                        AND vf.fecha_calendario >= CURRENT_DATE
                        AND vf.fecha_calendario <= CURRENT_DATE + INTERVAL gt.horizonte DAY
                        AND ha.fecha_borrado is null
                    group by fecha        
                    having capacidad > 0
                    ORDER BY vf.fecha_calendario ASC ;';

            $stmt = $conn->prepare($sql);
            $stmt->execute(['puntoAtencion' => $puntoAtencion->getId(), 'tramite' => $tramite->getId()]);
            $arrayFechas =  $stmt->fetchAll();
            $redisServices->setDisp($puntoAtencion->getId(), $tramite->getId(), $arrayFechas);
        }
        $deshabilitarHoy = false;
        if($puntoTramite && $puntoTramite->getDeshabilitarHoy() ){
            $deshabilitarHoy = true;
        }
        
        foreach ($arrayFechas as $key => &$value) {
            //verificar si ya terminó el horario de antención

            if($value['fecha'] == date("Y-m-d") && $value['hora_fin'] < date("H:i:s")){
                unset($arrayFechas[$key]);
            }
            
            //remove diasNoLaborables
            if (in_array($value['fecha'], $fechasNoDisponibles)) {
                unset($arrayFechas[$key]);
                continue;
            }
            $fecha = $value['fecha'];
            
            // verificar Si hay disponibilidad, es decir disponibilidad - turnos dados > 0
            if ( isset($arrayFechas_turnos_dados[$fecha])) {
                $turnos_dados =  $arrayFechas_turnos_dados[$fecha][0];
                if($turnos_dados > 0 && ($value['capacidad']  - $turnos_dados <= 0 ) ){
                    unset($arrayFechas[$key]);
                    continue;
                }else{
                    $value['turnos_dados'] = $turnos_dados;
                }
            }
            
            // verificar si debe deshabilitar la fecha actual
            if( $deshabilitarHoy && $fecha=date("Y-m-d") ){
                unset($arrayFechas[$key]);
                $deshabilitarHoy = false;
                continue;
            }
            
        }
        $arrayFechas = array_values($arrayFechas);
        return $arrayFechas;
    }
    /**
     * 
     * @param type $tramite
     * @param type $params
     * @return type
     */
    public function getDisponibilidadFechaTramites($tramite, $params , $redisServices){
        
        $arrayFechas =  $this->getDisponibilidadTramite($params, $tramite, $redisServices); 
        return array_values($arrayFechas);
    }
    
    /**
     * ejemplos de groupby
     *      "horario"=>' GROUP BY fecha,horario, punto_atencion_id,  turno.id HAVING CONCAT( fecha," ",horario) > NOW() AND turnos_dados < capacidad ',
            "fecha"=>'  AND CONCAT( vf.fecha_calendario," ", (ha.hora_inicio + INTERVAL via.acumulado MINUTE )) >= NOW() GROUP BY fecha, turno.id HAVING  turnos_dados < capacidad ',
            "fecha_pda"=>' AND CONCAT( vf.fecha_calendario," ", (ha.hora_inicio + INTERVAL via.acumulado MINUTE )) >= NOW()  GROUP BY fecha,punto_atencion_id,  turno.id HAVING  turnos_dados < capacidad ',
            "fecha_prov"=>' AND CONCAT( vf.fecha_calendario," ", (ha.hora_inicio + INTERVAL via.acumulado MINUTE )) >= NOW()  GROUP BY fecha, p.id ,  turno.id HAVING  turnos_dados < capacidad ',
            "localidad"=>' AND CONCAT( vf.fecha_calendario," ", (ha.hora_inicio + INTERVAL via.acumulado MINUTE )) >= NOW()  GROUP BY l.id ,  turno.id HAVING  turnos_dados < capacidad '
        ];
     * @param type $params
     * @param type $tramite
     * @return type
     */
    public function getDisponibilidadTramite($params, $tramite, $redisServices) {
        
        $horizonte = $params['horizonte'];
        $fecha_inicio = $params['fecha'];
        $offset = (int)$params['offset'];
        $limit = (int)$params['limit'];
        
        $groupby = isset($params['groupby'])?$params['groupby']:false;
        $sql_groupby = ' AND CONCAT( vf.fecha_calendario," ", (ha.hora_inicio + INTERVAL via.acumulado MINUTE )) >= NOW() GROUP BY ';
        $separador = "";
        
        if(strstr($groupby,"fecha")){
            $sql_groupby .= $separador." fecha";
            $separador = ", ";
        }
        if(strstr($groupby,"punto_atencion_id")){
            $sql_groupby .= $separador." punto_atencion_id";
            $separador = ", ";
        }
        if(strstr($groupby,"turno")){
            $sql_groupby .= $separador." turno.id";
            $separador = ", ";
        }
        if(strstr($groupby,"provincia_id")){
            $sql_groupby .= $separador." provincia_id";
            $separador = ", ";
        }
        if(strstr($groupby,"localidad_id")){
            $sql_groupby .= $separador." localidad_id";
            $separador = ", ";
        }
        
        
        $sql_groupby .= " HAVING  turnos_dados < capacidad ";
        
        if(!$groupby){
            $sql_groupby = ' GROUP BY fecha,horario, punto_atencion_id,  turno.id HAVING CONCAT( fecha," ",horario) > NOW() AND turnos_dados < capacidad ';
        }
        
        
        $filters = [];
        $redis_xt =  $groupby;
        
        $sql_where = "  "; 
        
        if(isset($params['punto_atencion_id'])  ){
            $filters["punto_atencion_id"] = isset($params['punto_atencion_id'])?$params['punto_atencion_id']:0;
            $redis_xt .=  "pda_".$filters["punto_atencion_id"];
            $sql_where .= " AND pt.punto_atencion_id = :punto_atencion_id ";
        }
        
        if(isset($params['multiturno']) && ($params['multiturno']==0 || $params['multiturno']==1) ){
            $filters["multiturno"] = isset($params['multiturno'])?$params['multiturno']:0;
            $redis_xt .=  "mt_".$filters["multiturno"];
            $sql_where .= " AND pt.multiturno = :multiturno ";
        }
        
        if(isset($params['multiturno_cantidad']) && ($params['multiturno_cantidad']> 0 ) ){
            $filters["multiturno_cantidad"] = $params['multiturno_cantidad'];
            $redis_xt .=  "mtc_".$filters["multiturno_cantidad"];
            $sql_where .= " AND pt.multiturno_cantidad >= :multiturno_cantidad ";
        }
        
        if(isset($params['provincia_id'])  ){
            $filters["provincia_id"] = isset($params['provincia_id'])?$params['provincia_id']:0;
            $redis_xt .=  "prov_".$filters["provincia_id"];
            $sql_where .= " AND pa.provincia_id = :provincia_id ";
        }
        
        if(isset($params['localidad_id']) ){
            $filters["localidad_id"] = isset($params['localidad_id'])?$params['localidad_id']:0;
            $redis_xt .=  "loc_".$filters["localidad_id"];
            $sql_where .= " AND pa.localidad_id = :localidad_id ";
        }
          
        $result_cache = $redisServices->getDisponibilidadTramite($tramite->getId(), $offset, $limit, $fecha_inicio, $horizonte, $redis_xt);
        if($result_cache){
            return $result_cache;
        }
      
        $sql = 'SELECT 
                     vf.fecha_calendario as fecha,
                    ha.hora_inicio,
                    max(ha.hora_fin) as hora_fin,
                    sum(disp.cantidad_turnos) AS capacidad,
                    disp.punto_atencion_id,
                    pa.nombre,
                    pa.direccion as direccion,
                    (ha.hora_inicio + INTERVAL via.acumulado MINUTE ) as horario,
                    count(turno.id) as turnos_dados,
                    pt.multiturno,
                    pt.multiturno_cantidad,
                    l.nombre as localidad,
                    l.id as localidad_id,
                    p.nombre as provincia,
                    p.id as provincia_id
                FROM horario_atencion ha 
                INNER JOIN disponibilidad AS disp ON ha.id = disp.horario_atencion_id 
                INNER JOIN punto_atencion pa ON pa.id = disp.punto_atencion_id
                INNER JOIN grupo_tramite gt ON gt.id = disp.grupo_tramite_id
                INNER JOIN tramites_grupotramite AS tgt ON gt.id = tgt.grupo_tramite_id
                INNER JOIN tramite AS t ON tgt.tramite_id = t.id
                INNER JOIN punto_tramite pt ON pt.punto_atencion_id = disp.punto_atencion_id AND pt.tramite_id = :tramite
                INNER JOIN view_fechas vf ON vf.dia_semana = ha.dia_semana
                INNER JOIN view_intervalos_acumulados via ON via.intervalo_tiempo = gt.intervalo_tiempo
                INNER JOIN localidad l ON pa.localidad_id = l.id
                INNER JOIN provincia p ON pa.provincia_id = p.id
                LEFT JOIN dias_no_laborables dnl ON dnl.punto_atencion_id = disp.punto_atencion_id AND dnl.fecha = vf.fecha_calendario
                LEFT JOIN dias_no_laborables_tramite dnlt ON dnlt.punto_atencion_id = disp.punto_atencion_id AND dnlt.tramite_id = t.id AND  dnlt.fecha = vf.fecha_calendario 
                LEFT JOIN turno  ON turno.grupo_tramite_id =  gt.id AND turno.fecha = vf.fecha_calendario 
                    AND turno.estado != 2  
                    AND turno.fecha_borrado IS NULL 
                    AND turno.hora = (ha.hora_inicio + INTERVAL via.acumulado MINUTE)
                WHERE t.id = :tramite
                    AND (ha.hora_inicio + INTERVAL via.acumulado MINUTE ) < ha.hora_fin
                    AND vf.fecha_calendario >= :fechaInicio
                    AND vf.fecha_calendario < :fechaInicio + INTERVAL :horizon DAY
                    AND ha.fecha_borrado is null
                    AND dnl.punto_atencion_id IS NULL
                    AND dnlt.punto_atencion_id IS NULL
                '.$sql_where.'    
                '.$sql_groupby.'
                ORDER BY vf.fecha_calendario ASC
                LIMIT    :offset, :limite';

        $tramiteId = $tramite->getId();
        $conn1 = $this->getEntityManager()->getConnection();
        $stmt = $conn1->prepare($sql);
        $stmt->bindParam('limite', $limit, \PDO::PARAM_INT);
        $stmt->bindParam('offset', $offset, \PDO::PARAM_INT);
        $stmt->bindParam('tramite',$tramiteId, \PDO::PARAM_INT);
        $stmt->bindParam('fechaInicio',$fecha_inicio);
        $stmt->bindParam('horizon', $horizonte, \PDO::PARAM_INT);
        
        if(isset($params['multiturno']) ){
            $stmt->bindParam("multiturno", $params['multiturno'], \PDO::PARAM_INT);
        }
        if(isset($params['multiturno_cantidad']) ){
            $stmt->bindParam("multiturno_cantidad", $params['multiturno_cantidad'], \PDO::PARAM_INT);
        }
        if(isset($params['localidad_id']) ){
            $stmt->bindParam("localidad_id", $params['localidad_id'], \PDO::PARAM_INT);
        }    
        if(isset($params['provincia_id']) ){
            $stmt->bindParam("provincia_id", $params['provincia_id'], \PDO::PARAM_INT);
        }
        if(isset($params['punto_atencion_id']) ){
            $stmt->bindParam("punto_atencion_id", $params['punto_atencion_id'], \PDO::PARAM_INT);
        }
        
        /*
         * @todo hacerlo dinamico. no lo toma actualmente
         */
        foreach ($filters as $key => $value) {
            //$stmt->bindParam($key, $value, \PDO::PARAM_INT);
        }
        try {
           
            $stmt->execute();    
        } catch (Exception $exc) {
            return false;
        }

        $result =  $stmt->fetchAll();
        
        
        $redisServices->setDisponibilidadTramite($tramite->getId(), $offset, $limit, $fecha_inicio, $horizonte, $redis_xt, $result);
        return $result;
    }
     
}
