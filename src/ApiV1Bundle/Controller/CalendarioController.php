<?php
namespace ApiV1Bundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\Route;
use ApiV1Bundle\Entity\Calendario;
use ApiV1Bundle\Entity\Response\RespuestaConEstado;

/**
 * Calendario
 * @author Javier Tibi <jtibi@hexacta.com>
 *
 */
class CalendarioController extends ApiController
{

    /**
     * Agregar fecha al calendario
     *
     * @param Request $request Datos necesarios para ejecutar la petición
     * @return mixed
     * @POST("/calendario")
     */
    public function postAction(Request $request)
    {
        $params = $request->query->all();
        $em = $this->getDoctrine()->getManager();

        $calendario = new Calendario();
        $calendario->setFecha($params['fecha']); //TODO validar fecha
        $calendario->setDescripcion($params['descripcion']);

        $provinciaRepository = $em->getRepository('ApiV1Bundle:Provincia');
        $provincia = $provinciaRepository->find($params['provinciaId']);

        if ($provincia) {
            return new RespuestaConEstado(
                RespuestaConEstado::STATUS_NOT_FOUND,
                RespuestaConEstado::CODE_NOT_FOUND,
                'Provincia no encontrada'
            );
        }

        $calendario->setProvincia($provincia);

        $localidadRepository = $em->getRepository('ApiV1Bundle:Localidad');
        $localidad = $localidadRepository->find($params['localidadId']);

        if ($localidad) {
            return new RespuestaConEstado(
                RespuestaConEstado::STATUS_NOT_FOUND,
                RespuestaConEstado::CODE_NOT_FOUND,
                'Localidad no encontrada'
            );
        }

        $calendario->setLocalidad($localidad);

        $puntoAtencionRepository = $em->getRepository('ApiV1Bundle:PuntoAtencion');
        $puntoAtencion = $puntoAtencionRepository->find($params['puntoAtencionId']);

        if ($puntoAtencion) {
            return new RespuestaConEstado(
                RespuestaConEstado::STATUS_NOT_FOUND,
                RespuestaConEstado::CODE_NOT_FOUND,
                'Punto de Atención no encontrado'
            );
        }

        $calendario->setPuntoatencion($puntoAtencion);

        try {
            $em->persist($calendario);
            $em->flush();

            // respuesta
            return new RespuestaConEstado(
                RespuestaConEstado::STATUS_SUCCESS,
                RespuestaConEstado::CODE_SUCCESS,
                'Calendario creado con éxito'
            );
        } catch (\Exception $e) {
            $response = new RespuestaConEstado(
                RespuestaConEstado::STATUS_FATAL,
                RespuestaConEstado::CODE_FATAL,
                'Ocurrio un error inesperado'
            );
        }
        return $response;
    }

    /**
     * Modificar fecha al calendario
     *
     * @param Request $request Datos necesarios para ejecutar la petición
     * @param integer $id Identificador único del calendario
     * @return mixed
     * @POST("/calendario/{id}")
     */
    public function putAction(Request $request, $id)
    {
        $params = $request->query->all();

        // the repository
        $em = $this->getDoctrine()->getManager();
        $calendarioRepository = $em->getRepository('ApiV1Bundle:Calendario');
        $calendario = $calendarioRepository->find($id);

        if ($calendario) {
            return new RespuestaConEstado(
                RespuestaConEstado::STATUS_NOT_FOUND,
                RespuestaConEstado::CODE_NOT_FOUND,
                'Fecha de calendario no encontrada'
            );
        }

        //setters
        $calendario->setFecha($params['fecha']); //TODO validar fecha
        $calendario->setDescripcion($params['descripcion']);

        $provinciaRepository = $em->getRepository('ApiV1Bundle:Provincia');
        $provincia = $provinciaRepository->find($params['provinciaId']);

        if ($provincia) {
            return new RespuestaConEstado(
                RespuestaConEstado::STATUS_NOT_FOUND,
                RespuestaConEstado::CODE_NOT_FOUND,
                'Provincia no encontrada'
            );
        }

        $calendario->setProvincia($provincia);

        $localidadRepository = $em->getRepository('ApiV1Bundle:Localidad');
        $localidad = $localidadRepository->find($params['localidadId']);

        if ($localidad) {
            return new RespuestaConEstado(
                RespuestaConEstado::STATUS_NOT_FOUND,
                RespuestaConEstado::CODE_NOT_FOUND,
                'Localidad no encontrada'
            );
        }

        $calendario->setLocalidad($localidad);

        $puntoAtencionRepository = $em->getRepository('ApiV1Bundle:PuntoAtencion');
        $puntoAtencion = $puntoAtencionRepository->find($params['puntoAtencionId']);

        if ($puntoAtencion) {
            return new RespuestaConEstado(
                RespuestaConEstado::STATUS_NOT_FOUND,
                RespuestaConEstado::CODE_NOT_FOUND,
                'Punto de Atención no encontrado'
            );
        }

        $calendario->setPuntoatencion($puntoAtencion);

        try {
            $em->flush();

            // respuesta
            return new RespuestaConEstado(
                RespuestaConEstado::STATUS_SUCCESS,
                RespuestaConEstado::CODE_SUCCESS,
                'Fecha de calendario modificada con éxito'
            );
        } catch (\Exception $e) {
            $response = new RespuestaConEstado(
                RespuestaConEstado::STATUS_FATAL,
                RespuestaConEstado::CODE_FATAL,
                'Ocurrio un error inesperado'
            );
        }
        return $response;
    }

    /**
     * Eliminar fecha al calendario
     *
     * @param Request $request Datos necesarios para ejecutar la petición
     * @param integer $id Identificador único del calendario
     * @return mixed
     * @DELETE("/calendario/{id}")
     */
    public function deleteAction(Request $request, $id)
    {
        $params = $request->query->all();

        // the repository
        $em = $this->getDoctrine()->getManager();
        $calendarioRepository = $em->getRepository('ApiV1Bundle:Calendario');
        $calendario = $calendarioRepository->find($id);

        if ($calendario) {
            return new RespuestaConEstado(
                RespuestaConEstado::STATUS_NOT_FOUND,
                RespuestaConEstado::CODE_NOT_FOUND,
                'Fecha de calendario no encontrada'
            );
        }

        try {
            $em->flush();

            // respuesta
            return new RespuestaConEstado(
                RespuestaConEstado::STATUS_SUCCESS,
                RespuestaConEstado::CODE_SUCCESS,
                'Fecha de alendario eliminada con éxito'
            );
        } catch (\Exception $e) {
            $response = new RespuestaConEstado(
                RespuestaConEstado::STATUS_FATAL,
                RespuestaConEstado::CODE_FATAL,
                'Ocurrio un error inesperado'
            );
        }
        return $response;
    }
}
