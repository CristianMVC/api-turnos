<?php
namespace ApiV1Bundle\Controller;

use ApiV1Bundle\ApplicationServices\CategoriaServices;
use ApiV1Bundle\ApplicationServices\UsuarioServices;
use ApiV1Bundle\Entity\Response\Respuesta;
use ApiV1Bundle\Entity\Response\RespuestaConEstado;
use FOS\RestBundle\Controller\FOSRestController;
use ApiV1Bundle\ApplicationServices\TurnoServices;

/**
 * Class ApiController
 *
 * Clase base de los controladores de la API
 * @author Javier Ignacio Tibi <jtibi@hexacta.com>
 *
 * @package ApiV1Bundle\Controller
 */

class  ApiController extends FOSRestController
{
    protected function getLogger()
    {
        return $this->container->get('logger');
    }

    /**
     * Obtiene Tramite service
     *
     * @return object
     */
    protected function getTramiteServices()
    {
        return $this->container->get('snt.tramite.services');
    }

    /**
     * Obtiene Turno services
     *
     * @return \ApiV1Bundle\ApplicationServices\TurnoServices
     */
    protected function getTurnoServices()
    {
        return $this->container->get('snt.turno.services');
    }

    /**
     * Obtiene Localidades services
     *
     * @return object
     */
    protected function getLocalidadesServices()
    {
        return $this->container->get('snt.localidad.services');
    }

    /**
     * Obtiene Provincia services
     *
     * @return object
     */
    protected function getProvinciaServices()
    {
        return $this->container->get('snt.provincia.services');
    }

    /**
     * Obtiene Horario de atención services
     *
     * @return object
     */
    protected function getHorarioAtencionServices()
    {
        return $this->container->get('snt.horarioatencion.services');
    }

    /**
     * Obtiene Organismo Services
     *
     * @return object
     */
    protected function getOrganismoServices()
    {
        return $this->container->get('snt.organismo.services');
    }

    /**
     * Obtiene Area Services
     *
     * @return object
     */
    protected function getAreaServices()
    {
        return $this->container->get('snt.area.services');
    }

    /**
     * Obtiene Grupo tramites Services
     *
     * @return object
     */
    protected function getGrupoTramiteServices()
    {
        return $this->container->get('snt.grupotramite.services');
    }

    /**
     * Obtiene Punto de atención Services
     *
     * @return object
     */
    protected function getPuntoAtencionServices()
    {
        return $this->container->get('snt.puntoatencion.services');
    }

    /**
     * Obtiene Disponibilidad Services
     *
     * @return object
     */
    protected function getDisponibilidadServices()
    {
        return $this->container->get('snt.disponibilidad.services');
    }

    /**
     * Obtiene Feriado Nacional Services
     *
     * @return object
     */
    protected function getFeriadoNacionalServices()
    {
        return $this->container->get('snt.feriadonacional.services');
    }

    /**
     * Obtiene el Security Service
     *
     * @return object
     */
    protected function getSecurityServices()
    {
        return $this->container->get('snt.security.services');
    }

    /**
     * Obtiene el Usuario Service
     *
     * @return object
     */
    protected function getUsuarioServices()
    {
        return $this->container->get('snt.usuario.services');
    }

    /**
     * Obtiene el Usuario Service
     *
     * @return object
     */
    protected function getCategoriaServices()
    {
        return $this->container->get('snt.categorias.services');
    }

    protected function getRolesServices(){
        return $this->container->get('snt.roles.services');
    }

    /**
     * Obtiene Tramite service
     *
     * @return object
     */
    protected function getCategoriaTramiteServices()
    {
        return $this->container->get('snt.categoria.tramite.services');
    }


    /**
     * Obtiene Etiqueta service
     *
     * @return object
     */
    protected function getEtiquetaServices()
    {
        return $this->container->get('snt.etiqueta.services');
    }


    protected function getDiaNoLaborableTramiteServices(){
        return $this->container->get('snt.dianolaborabletramite.services');
    }
    
    protected function getContainerRedis(){
        return $this->container->get('snc_redis.default');
    }

    protected function getMiArgentinaServices(){
        return $this->container->get('snt.miargentina.services');
    }
    
    /**
     * Obtiene Redis Services
     *
     * @return object
     */
    protected function getRedisServices(){
        return $this->container->get('snt.redis.services');
    }
    /**
     * Retorna una Respuesta con estado SUCCESS
     *
     * @param string $message Mensaje de éxito
     * @return RespuestaConEstado
     */
    protected function respuestaOk($message, $additional = '')
    {
        return new RespuestaConEstado(
            RespuestaConEstado::STATUS_SUCCESS,
            RespuestaConEstado::CODE_SUCCESS,
            $message,
            '',
            $additional
        );
    }

    /**
     * Retorna una Respuesta con estado FATAL
     *
     * @param string $message Mensaje Fatal
     * @return RespuestaConEstado
     */
    protected function respuestaError($message)
    {
        return new RespuestaConEstado(
            RespuestaConEstado::STATUS_FATAL,
            RespuestaConEstado::CODE_FATAL,
            $message,
            '',
            ''
        );
    }

    /**
     * Retorna una Respuesta con estado Not Found
     *
     * @param string $message Mensaje No encontrado
     * @return RespuestaConEstado
     */
    protected function respuestaNotFound($message)
    {
        return new RespuestaConEstado(
            RespuestaConEstado::STATUS_NOT_FOUND,
            RespuestaConEstado::CODE_NOT_FOUND,
            $message
        );
    }

    /**
     * Retorna una Respuesta con estado Bad Request
     *
     * @param string $message Mensaje respuesta errónea
     * @return RespuestaConEstado
     */
    protected function respuestaBadRequest($message)
    {
        return new RespuestaConEstado(
            RespuestaConEstado::STATUS_BAD_REQUEST,
            RespuestaConEstado::CODE_BAD_REQUEST,
            $message
        );
    }

    /**
     * Retorna una respuesta con estado Forbidden
     *
     * @param string $message
     * @return RespuestaConEstado
     */
    protected function respuestaForbidden($message)
    {
        return new RespuestaConEstado(
            RespuestaConEstado::STATUS_FORBIDDEN,
            RespuestaConEstado::CODE_FORBIDDEN,
            $message
        );
    }

    /**
     * Retorna una Respuesta con datos
     *
     * @param array $metadata Arreglo con la metadata
     * @param array $result Arreglo con el resultado
     * @return object
     */
    protected function respuestaData($metadata, $result)
    {
        return new Respuesta($metadata, $result);
    }

}
