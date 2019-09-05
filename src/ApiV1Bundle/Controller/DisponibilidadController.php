<?php

namespace ApiV1Bundle\Controller;

use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Route;
use Symfony\Component\HttpFoundation\Request;
use ApiV1Bundle\Entity\Response\RespuestaConEstado;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class DisponibilidadController extends ApiController
{
    private $disponibilidadServices;

    /**
     * Obtiene un listado de puntos de atención por tramite, provincia, localidad y fecha (opcional)
     * @ApiDoc(section="Disponibilidad",
     *  parameters={
     *      {"name"="tramiteId", "dataType"="integer", "required"=true, "description"="Id tramite"},
     *      {"name"="provincia", "dataType"="integer", "required"=true, "description"="Id provincia"},
     *      {"name"="localidad", "dataType"="integer", "required"=true, "description"="Id localidad"}
     *    
     *  })
     * @param Request $request Datos necesarios para ejecutar la petición
     * @return mixed
     * @Get("/disponibilidad/puntosatencion")
     */
    public function getListPuntosAtencionDisponibles(Request $request)
    {
        $params = $request->query->all();
        $this->disponibilidadServices = $this->getDisponibilidadServices();
        return  $this->disponibilidadServices->getPuntosAtencionDisponibles(
            $params,
            function ($err) {
                return $this->respuestaError($err);
            }
        );
    }

    /**
     * Obtener listado de fechas disponibles
     * @ApiDoc(section="Disponibilidad",
     * parameters={
     *     {"name"="tramiteId", "dataType"="integer", "required"=true, "description"="Id tramite"},
     *     {"name"="provincia", "dataType"="integer", "required"=true, "description"="Id provincia"},
     *     {"name"="localidad", "dataType"="integer", "required"=true, "description"="Id localidad"},
     *     {"name"="puntoAtencionId", "dataType"="integer", "required"=true, "description"="Id punto de atencion"}
     * })
     * @param Request $request Datos necesarios para ejecutar la petición
     * @return mixed
     * @Get("/disponibilidad/fechas")
     */
    public function getListFechasDisponibles(Request $request)
    {
        $params = $request->query->all();
        $this->disponibilidadServices = $this->getDisponibilidadServices();

        return  $this->disponibilidadServices->getFechasDisponibles(
            $params,
            function ($err) {
                return $this->respuestaError($err);
            }
        );
    }

    /**
     * Obtener la lista de horarios disponibles
     * @ApiDoc(section="Disponibilidad",
     * parameters={
     *     {"name"="tramiteId", "dataType"="integer", "required"=true, "description"="Id tramite"},
     *     {"name"="fecha", "dataType"="date", "required"=true, "description"="2017-11-27T10:30:00.398Z"},
     * })
     * @param Request $request Datos necesarios para ejecutar la petición
     * @param integer $puntoAtencionId Identificador único del punto de atención
     * @return mixed
     * @Get("/disponibilidad/horarios/{puntoAtencionId}")
     */
    public function getListHorariosDisponibles(Request $request, $puntoAtencionId)
    {
        $params = $request->query->all();
        $disponibilidadServices = $this->getDisponibilidadServices();

        return $disponibilidadServices->getHorariosDisponibles(
            $params,
            $puntoAtencionId,
            function ($err) {
                return $this->respuestaError($err);
            }
        );
    }

    /**
     * Crea la disponibilidad de turnos de un punto de atención
     * @ApiDoc(section="Disponibilidad",
     * parameters={
     *     {"name"="puntoAtencion", "dataType"="integer", "required"=true, "description"="Id punto de atencion"},
     *     {"name"="grupoTramite", "dataType"="integer", "required"=true, "description"="Id grupo tramite"},
     *     {"name"="rangoHorario", "dataType"="integer", "required"=true, "description"="Id rango horario"},
     *     {"name"="cantidadTurnos", "dataType"="integer", "required"=true, "description"="Cantidad de turnos "}
     * })
     * @param Request $request Datos necesarios para ejecutar la petición
     * @return mixed
     * @Post("/disponibilidad")
     */
    public function postAction(Request $request)
    {
        $params = $request->request->all();

        $disponibilidadServices = $this->getDisponibilidadServices();
        return $disponibilidadServices->create(
            $params,
            function ($disponibilidad) {
                return $this->respuestaOk('Disponibilidad creada con éxito', [
                    'id' => $disponibilidad->getId()
                ]);
            },
            function ($err) {
                return $this->respuestaError($err);
            }
        );
    }

    /**
     * Editar la disponibilidad de turnos de un punto de atención
     * @ApiDoc(section="Disponibilidad",
     * parameters={
     *     {"name"="puntoAtencion", "dataType"="integer", "required"=true, "description"="Id punto de atencion"},
     *     {"name"="grupoTramite", "dataType"="integer", "required"=true, "description"="Id grupo tramite"},
     *     {"name"="cantidadTurnos", "dataType"="integer", "required"=true, "description"="Cantidad de turnos "}
     * })
     * @param Request $request Datos necesarios para ejecutar la petición
     * @param integer $idRow Identificador de fila del horario de atención
     * @return mixed
     * @Put("/disponibilidad/{idRow}")
     */
    public function putAction(Request $request, $idRow)
    {
        $params = $request->request->all();
        $disponibilidadServices = $this->getDisponibilidadServices();
        return $disponibilidadServices->edit(
            $params,
            $idRow,
            function () {
                return $this->respuestaOk('Disponibilidad modificada con éxito');
            },
            function ($err) {
                return $this->respuestaError($err);
            }
        );
    }

    /**
     * Obtener la disponibilidad de turnos de un Punto de Atención por grupo de trámite agrupado por horario de atencion
     * @ApiDoc(section="Disponibilidad")
     * @param integer $id Identificador único del punto de atención
     * @param integer $grupoTramiteId Identificador único del grupo de trámites
     * @return mixed
     * @Get("/disponibilidad/puntosatencion/{id}/grupotramite/{grupoTramiteId}")
     */
    public function getAllTurnosByPuntoAtencionGrupoTramites($id, $grupoTramiteId)
    {
        return  $this->getDisponibilidadServices()->getAllTurnosByPuntoAtencionGrupoTramite(
            $id, $grupoTramiteId,
            function ($err) {
                return $this->respuestaError($err);
            }
        );
    }
}