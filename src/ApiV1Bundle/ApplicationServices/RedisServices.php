<?php
namespace ApiV1Bundle\ApplicationServices;
use ApiV1Bundle\Entity\ValidateResultado;
use ApiV1Bundle\Entity\Turno;
use Symfony\Component\DependencyInjection\Container;

/**
 * Class RedisServices
 * @package ApiV1Bundle\ApplicationServices
 */
class RedisServices extends SNTServices
{
    
    
    private $expire = 300;// 5minutos en segundos // 60 * 5 
    private $enable = false;


    /**
     * RedisServices constructor.
     *
     * @param Container $container
     */
    public function __construct(Container $container, $enable){
        parent::__construct($container);
        $this->enable = $enable;
    }
    
    //
    // DisponibilidadTurnos
    /**
     * se usa para obtener los turnos dados para un tramite y punto de atencion
     * es parte de la consulta de disponibilidad
     * @param type $puntoAtencionId
     * @param type $tramiteId
     * @param type $result
     * @return ValidateResultado
     */
    public function setDispTurno($puntoAtencionId, $tramiteId, $result){
        if(!$this->enable) {
            return false;
        }
        $key =  'disp_turno_' . $puntoAtencionId . '_tramite_' . $tramiteId. '_fecha_' . date("Y-m-d");
        $val = $this->getContainerRedis()->set($key, json_encode($result));
        $this->getContainerRedis()->expire($key, $this->expire);
        return $val;
    }
    
    /**
     * 
     * @param type $puntoAtencionId
     * @param type $tramiteId
     * @return boolean
     */
    public function getDispTurno($puntoAtencionId, $tramiteId){
        if(!$this->enable) {
            return false;
        }
        $key =  'disp_turno_' . $puntoAtencionId . '_tramite_' . $tramiteId. '_fecha_' . date("Y-m-d");
        $val = $this->getContainerRedis()->get($key);
        if ($val) {
            return json_decode($val,1);
        }
        return false;
    }
    /**
     * 
     * @param type $puntoAtencionId
     * @param type $tramiteId
     */
    private function delDispTurno($puntoAtencionId, $tramiteId){
        if(!$this->enable) {
            return false;
        }
        $key =  'disp_turno_' . $puntoAtencionId . '_tramite_' . $tramiteId. '_fecha_' . date("Y-m-d");
        $this->deleteKey($key);
    }
    
    // Disponibilidad
    /**
     * Para obtener la disponibilidad por tramite y punto de atención
     * @param type $puntoAtencionId
     * @param type $tramiteId
     * @param type $result
     * @return ValidateResultado
     */
    public function setDisp($puntoAtencionId, $tramiteId, $result){
        if(!$this->enable) {
            return false;
        }
        $key =  'disp_' . $puntoAtencionId . '_tramite_' . $tramiteId. '_fecha_' . date("Y-m-d");
        $val = $this->getContainerRedis()->set($key, json_encode($result));
        $this->getContainerRedis()->expire($key, $this->expire);
        return $val;
    }
    
    /**
     * 
     * @param type $puntoAtencionId
     * @param type $tramiteId
     * @return boolean
     */
    public function getDisp($puntoAtencionId, $tramiteId){
        if(!$this->enable) {
            return false;
        }
        $key =  'disp_' . $puntoAtencionId . '_tramite_' . $tramiteId. '_fecha_' . date("Y-m-d");
        $val = $this->getContainerRedis()->get($key);
        if ($val) {
            return json_decode($val,1);
        }
        return false;
    }

    
    /**
     * Elimina todos los caches de turnos dadospara el punto de atención
     * @param type $puntoAtencionId
     */
    public function redisDelTurnoByPuntoDeAtencion($puntoAtencionId) {
        if(!$this->enable) {
            return false;
        }
        $prefix= 'disp_turno_' . $puntoAtencionId . '_tramite_';
        $r = $this->getContainerRedis(); 
        $keys = $r->keys($prefix.'*');
        foreach ($keys as $key => $value) {
            $r->del($value);
        }
        $this->delAllDisponibilidadTramite();
    }
    
    /**
     * Elimina todos los cache de disponibilidad para el punto de atención
     * @param type $puntoAtencionId
     */
    public function redisDelDispByPuntoDeAtencion($puntoAtencionId) {
        if(!$this->enable) {
            return false;
        }
        $prefix= 'disp_' . $puntoAtencionId . '_tramite_';
        $r = $this->getContainerRedis(); 
        $keys = $r->keys($prefix.'*');
        foreach ($keys as $key => $value) {
            $r->del($value);
        }
        $this->delAllDisponibilidadTramite();
    }
    
    
    
    public function delDispTurnoByTurno(Turno $turno) {
        if(!$this->enable) {
            return false;
        }
        $tramiteId = $turno->getTramite()->getId();
        $puntoAtencionId = $turno->getPuntoAtencion()->getId();
        $this->delDispTurno($puntoAtencionId, $tramiteId);
        $this->delDisponibilidadTramite($tramiteId);
    }
    
    // DisponibilidadTramite
    /**
     * 
     * @param type $tramiteId
     * @param type $offset
     * @param type $limit
     * @param type $fecha_inicio
     * @param type $result
     * @return type
     */
    public function setDisponibilidadTramite($tramiteId, $offset, $limit, $fecha_inicio, $horizonte, $redis_xt, $result) {
        if(!$this->enable) {
            return false;
        }
        $key = 'disp_tramite_' . $tramiteId . '_inicio_' . $fecha_inicio . '_offset_' . $offset . '_limit_' . $limit . '_fecha_' . date("Y-m-d").'_hrzn_'.$horizonte."_xt_".$redis_xt;
        $val = $this->getContainerRedis()->set($key, json_encode($result));
        $this->getContainerRedis()->expire($key, $this->expire);
        return $val;
    }

    /**
     * 
     * @param type $puntoAtencionId
     * @param type $tramiteId
     * @return boolean
     */
    public function getDisponibilidadTramite($tramiteId, $offset, $limit, $fecha_inicio,$horizonte, $redis_xt){
        if(!$this->enable) {
            return false;
        }
        $key = 'disp_tramite_' . $tramiteId . '_inicio_' . $fecha_inicio . '_offset_' . $offset . '_limit_' . $limit . '_fecha_' . date("Y-m-d").'_hrzn_'.$horizonte."_xt_".$redis_xt;
        $val = $this->getContainerRedis()->get($key);
        if ($val) {
            return json_decode($val,1);
        }
        return false;
    }
    /**
     * 
     * @param type $puntoAtencionId
     * @param type $tramiteId
     */
    private function delDisponibilidadTramite($tramiteId){
        if(!$this->enable) {
            return false;
        }
        $prefix= 'disp_tramite_' . $tramiteId . '_inicio_';
        $r = $this->getContainerRedis(); 
        $keys = $r->keys($prefix.'*');
        foreach ($keys as $key => $value) {
            $r->del($value);
        }
    }
    /**
     * 
     * @param type $puntoAtencionId
     * @param type $tramiteId
     */
    private function delAllDisponibilidadTramite(){
        if(!$this->enable) {
            return false;
        }
        $prefix= 'disp_tramite_';
        $r = $this->getContainerRedis(); 
        $keys = $r->keys($prefix.'*');
        foreach ($keys as $key => $value) {
            $r->del($value);
        }
    }
    
    /**
     * 
     * @param type $puntoAtencionId
     * @param type $tramiteId
     */
    public function flushAllDisp(){
        if(!$this->enable) {
            return $this->respuestaData([], "Redis no Habilitado");
        }
        $prefix= 'disp_';
        $r = $this->getContainerRedis(); 
        $keys = $r->keys($prefix.'*');
        foreach ($keys as $key => $value) {
            $r->del($value);
        }
       return $this->respuestaData([], "Ok");
        
    }
    /**
     * Elimina una Key específica
     *
     * @param $key | key
     * @throws \Exception
     */
    private function deleteKey($key){
        $this->getContainerRedis()->del($key);
    }

    /**
     * Obtiene el primer elemento de uan transacción
     *
     * @param $cola | cola
     * @return mixed
     */
    private function getFirstElementTransaction($cola, $tx){
        $turnos = $tx->zrange($cola, 0, -1);
        return $turnos[0];
    }

    /**
     * Obtiene la posicion de un turno en la oola
     * @param object $turno Turno
     * @param object $cola Cola
     * @return int
     * @throws \Exception
     */
    public function getPosicion($turno, $cola){
        $turnos = $this->getCola(
            $turno->getPuntoAtencion()->getId(),
            $cola->getId(),
            0,
            -1
        );

        for ($i = 0; $i < count($turnos); $i++) {
            if (json_decode($turnos[$i])->codigo == $turno->getCodigo()) {
               return $i;
            }
        }

        return -1;
    }

    /**
     * verifica si existe una Key
     *
     * @param $key | key
     * @param null|object $tx Transacción
     * @return mixed
     * @throws \Exception
     */
    private function exists($key, $tx = null){
        if ($tx) {
            return $tx->exists($key);
        }
        return $this->getContainerRedis()->exists($key);
    }

}