<?php
namespace TotemV1Bundle\Controller;

use TotemV1Bundle\ApplicationServices\CategoriaServices;
use TotemV1Bundle\Entity\Response\Respuesta;
use TotemV1Bundle\Entity\Response\RespuestaConEstado;
use FOS\RestBundle\Controller\FOSRestController;
use TotemV1Bundle\ApplicationServices\TramiteServices;

/**
 * Class ApiController
 *
 * Clase base de los controladores de la API
 * @author Javier Ignacio Tibi <jtibi@hexacta.com>
 *
 * @package TotemV1Bundle\Controller
 */

class ApiController extends FOSRestController
{
    /**
     * Obtiene el Usuario Service
     *
     * @return CategoriaServices
     */
    protected function getCategoriaServices()
    {
        return $this->container->get('totem.categoria.services');
    }

    /**
     * Obtiene Tramite service
     *
     * @return object
     */
    protected function getTramiteServices()
    {
        return $this->container->get('totem.tramite.services');
    }

    /**
     * Obtiene Turno service
     *
     * @return object
     */
    protected function getTurnoServices()
    {
        return $this->container->get('totem.turno.services');
    }

    /**
     * Obtiene el logger
     *
     * @return object
     */
    protected function getLogger()
    {
        return $this->container->get('logger');
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
     * @param $metadata
     * @param $result
     * @return \TotemV1Bundle\Entity\Respuesta
     */
    protected function respuestaData($metadata, $result)
    {
        return new Respuesta($metadata, $result);
    }
}
