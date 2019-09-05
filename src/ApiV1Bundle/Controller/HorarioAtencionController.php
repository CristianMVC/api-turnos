<?php

namespace ApiV1Bundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\Route;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * Class HorarioAtencionController
 *
 * @package ApiV1Bundle\Controller
 *
 * Horario Atencion
 * @author Javier Tibi <jtibi@hexacta.com> *
 */

class HorarioAtencionController extends ApiController
{

    /**
     * listado de rangos horarios
     * @ApiDoc(section="Horario atencion")
     * @param Request $request Datos necesarios para ejecutar la petición
     * @param integer $id identificador único del Punto de Atencion
     * @return mixed
     * @Get("/puntosatencion/{id}/horarioatencion")
     */
    public function getListAction(Request $request, $id)
    {
        $horarioAtencionServices = $this->getHorarioAtencionServices();
        return $horarioAtencionServices->findAll($id);
    }

    /**
     * Obtener todos los rangos de atencion por idRow
     * @ApiDoc(section="Horario atencion")
     * @param integer $puntoAtencionId identificador único del Punto de Atencion
     * @param integer $idRow identificador de fila de horario de atención
     * @return mixed
     * @Get("/puntosatencion/{puntoAtencionId}/horarioatencion/{idRow}")
     */
    public function getItemAction($puntoAtencionId, $idRow)
    {
        $horarioAtencionServices = $this->getHorarioAtencionServices();
        return $horarioAtencionServices->getHorarioAtencionByIdRow(
            $puntoAtencionId,
            $idRow,
            function ($err) {
                return $this->respuestaError($err);
            }
        );
    }

    /**
     * Crear un horario de atención
     * @ApiDoc(section="Horario atencion",
     * parameters={
     *      {"name"="horaInicio", "dataType"="string", "required"=true, "description"="Hora inicio"},
     *      {"name"="horaFin", "dataType"="string", "required"=true, "description"="Hora fin"},
     *      {"name"="diasSemana", "dataType"="array", "required"=true, "description"="[1,2,3,4,5,6,7]"},
     *      {"name"="idRow", "dataType"="integer", "required"=true, "description"="Id row"}
     *  })
     * @param Request $request Datos necesarios para ejecutar la petición
     * @param integer $puntoAtencionId Identificador único del punto de atención
     * @return mixed
     * @Post("/puntosatencion/{puntoAtencionId}/horarioatencion")
     */
    public function postAction(Request $request, $puntoAtencionId)
    {
        $params = $request->request->all();
        $horarioAtencionServices = $this->getHorarioAtencionServices();
        return $horarioAtencionServices->create(
            $params,
            $puntoAtencionId,
            function ($horarioAtencion) {
                return $this->respuestaOk('Horarios de atención creados con éxito', [
                    'id' => $horarioAtencion->getIdRow()
                ]);
            },
            function ($err) {
                return $this->respuestaError($err);
            }
        );
    }

    /**
     * Modificar horario atención
     * @ApiDoc(section="Horario atencion",
     * parameters={
     *      {"name"="horaInicio", "dataType"="string", "required"=true, "description"="Hora inicio"},
     *      {"name"="horaFin", "dataType"="string", "required"=true, "description"="Hora fin"},
     *      {"name"="diasSemana", "dataType"="array", "required"=true, "description"="[1,2,3,4,5,6,7]"},
     *  })
     * @param Request $request Datos necesarios para ejecutar la petición
     * @param integer $puntoAtencionId Identificador único del punto de atención
     * @param integer $rowId Identificador de la fila del horario del punto de atención
     * @return mixed
     * @Put("/puntosatencion/{puntoAtencionId}/horarioatencion/{idRow}")
     */
    public function putAction(Request $request, $puntoAtencionId, $idRow)
    {
        $params = $request->request->all();
        $horarioAtencionServices = $this->getHorarioAtencionServices();
        return $horarioAtencionServices->edit(
            $params,
            $puntoAtencionId,
            $idRow,
            function () {
                return $this->respuestaOk('Horario modificado con éxito');
            },
            function ($err) {
                return $this->respuestaError($err);
            }
        );
    }

    /**
     * Eliminar horario atención
     * @ApiDoc(section="Horario atencion")
     * @param Request $request Datos necesarios para ejecutar la petición
     * @param integer $puntoAtencionId Identificador único del punto de atención
     * @param integer $rowId Identificador único del horario del punto de atención
     * @return mixed
     * @Delete("/puntosatencion/{puntoAtencionId}/horarioatencion/{idRow}")
     */
    public function deleteAction(Request $request, $puntoAtencionId, $idRow)
    {
        $horarioAtencionServices = $this->getHorarioAtencionServices();
        return $horarioAtencionServices->delete(
            $puntoAtencionId,
            $idRow,
            function () {
                return $this->respuestaOk('Horarios de atención eliminados con éxito');
            },
            function ($err) {
                return $this->respuestaError($err);
            }
        );
    }

    /**
     * Obtener los intervalos horarios
     * @ApiDoc(section="Horario atencion")
     * @param integer $puntoAtencionId Identificador único del punto de atención
     * @return mixed
     * @GET("/horariosatencion/intervalos/{puntoAtencionId}")
     */
    public function intervalosAction($puntoAtencionId)
    {
        $horarioAtencionDisponible = $this->getHorarioAtencionServices();
        return  $horarioAtencionDisponible->getHoursAvailable(
            $puntoAtencionId,
            function ($err) {
                return $this->respuestaError($err);
            }
        );
    }

    /**
     * Obtener listado de horarios
     * @ApiDoc(section="Horario atencion")
     * @return mixed
     * @GET("/horariosatencion/listarhorarios")
     */
    public function listadoHorariosAction()
    {
        $listadoHorarios = $this->getHorarioAtencionServices();
        return $listadoHorarios->getListHours();
    }
}
