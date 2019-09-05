<?php
namespace ApiV1Bundle\Controller;

use ApiV1Bundle\ApplicationServices\PuntoAtencionServices;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\Route;
use Symfony\Component\HttpFoundation\Request;
use ApiV1Bundle\Entity\Response\Respuesta;
use ApiV1Bundle\Entity\Response\RespuestaConEstado;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * Class PuntosAtencionController
 * @package ApiV1Bundle\Controller
 *
 * Puntos de atención
 * @author Fausto Carrera <fcarrera@hexacta.com>
 *
 */

class PuntosAtencionController extends ApiController
{
    /** @var PuntoAtencionServices */
    private $puntoAtencionServices;

    /**
     * Listado de puntos atención
     * @ApiDoc(section="Punto atencion",
     * parameters={
     *      {"name"="offset", "dataType"="integer", "required"=false},
     *      {"name"="limit", "dataType"="integer", "required"=false}
     *  })
     * @param Request $request Datos necesarios para ejecutar la petición
     * @return mixed
     * @Get("/puntosatencion")
     */
    public function getListAction(Request $request)
    {
        $authorization = $request->headers->get('Authorization', null);
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 10);
        $puntoAtencionServices = $this->getPuntoAtencionServices();
        return $puntoAtencionServices->findAllPaginate(
            (int) $offset,
            (int) $limit,
            $authorization,
            function ($err) {
                return $this->respuestaError($err);
            }
        );
    }

    /**
     * Buscar puntos atención
     * @ApiDoc(section="Punto atencion",
     * parameters={
     *      {"name"="q", "dataType"="string", "required"=false, "description"="Nombre o Abreviatura"}
     *  })
     * @param Request $request Datos necesarios para ejecutar la petición
     * @return mixed
     * @Get("/puntosatencion/buscar")
     */
    public function searchAction(Request $request)
    {
        $params = $request->query->all();
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 10);
        return  $this->getPuntoAtencionServices()->search(
            $params,
            (int) $offset,
            (int) $limit,
            function ($err) {
                return $this->respuestaError($err);
            }
        );
    }

    /**
     * Obtener punto de atención
     * @ApiDoc(section="Punto atencion")
     * @param integer $id Identificador único del punto de atención a obtener
     * @return mixed
     * @Get("/puntosatencion/{id}")
     */
    public function getItemAction($id)
    {
        $puntoAtencionServices = $this->getPuntoAtencionServices();
        return $puntoAtencionServices->get(
            $id,
            function ($pda) {
                return $this->respuestaData([], $pda);
            },
            function () {
                return $this->respuestaNotFound("Punto de atención inexistente");
            }
        );
    }

    /**
     * Asignar visibilidad a un trámite
     * @ApiDoc(section="Punto atencion",
     * parameters={
     *      {"name"="estado", "dataType"="integer", "required"=true, "description"=" 0 o 1"}
     *  })
     * @param Request $request Datos necesarios para ejecutar la petición
     * @param integer $puntoAtencionId Identificador único del punto de atención
     * @param integer $tramiteId identificador único del trámite
     * @return mixed
     * @Put("/puntosatencion/{puntoAtencionId}/tramites/{tramiteId}/visibilidad")
     */
    public function setVisibilidadTramite(Request $request, $puntoAtencionId, $tramiteId)
    {
        $puntoAtencionServices = $this->getPuntoAtencionServices();
        $params = $request->request->all();
        return $puntoAtencionServices->setVisibilidad(
            $puntoAtencionId,
            $tramiteId,
            $params,
            function () {
                return $this->respuestaOk("Visibilidad editada con éxito");
            },
            function ($err) {
                return $this->respuestaError($err);
            }
        );
    }

    /**
     * Obtener el listado de tramites del punto de atención
     * @ApiDoc(section="Punto atencion")
     * @param Request $request Datos necesarios para ejecutar la petición
     * @param integer $id Identificador único del punto de atención
     * @return mixed
     * @Get("/puntosatencion/{id}/tramites")
     */
    public function getTramitesAction(Request $request, $id)
    {
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 10);
        $puntoAtencionServices = $this->getPuntoAtencionServices();
        return $puntoAtencionServices->findTramitesPaginate(
            $id,
            (int) $offset,
            (int) $limit,
            function ($err) {
                return $this->respuestaError($err);
            }
        );
    }

    /**
     * Crear punto de atención
     * @ApiDoc(section="Punto atencion",
     * parameters={
     *      {"name"="area", "dataType"="integer", "required"=true, "description"="Id area"},
     *      {"name"="direccion", "dataType"="string", "required"=true, "description"="Direccion"},
     *      {"name"="estado", "dataType"="integer", "required"=true, "description"="0 o 1"},
     *      {"name"="Id", "dataType"="integer", "required"=true, "description"="Id punto"},
     *      {"name"="localidad", "dataType"="integer", "required"=true, "description"="Id localidad"},
     *      {"name"="nombre", "dataType"="string", "required"=true, "description"="Nombre punto"},
     *      {"name"="provincia", "dataType"="integer", "required"=true, "description"="Id provincia"},
     *      {"name"="tramites", "dataType"="array", "required"=true, "description"="Id tramites"}
     *  })
     * @param Request $request Datos necesarios para ejecutar la petición
     * @return mixed
     * @Post("/puntosatencion")
     */
    public function postAction(Request $request)
    {
        $params = $request->request->all();
        $puntoAtencionServices = $this->getPuntoAtencionServices();
        return $puntoAtencionServices->create(
            $params,
            function ($puntoAtencion) {
                return $this->respuestaOk('Punto de atención creado con éxito', [
                    'id' => $puntoAtencion->getId()
                ]);
            },
            function ($err) {
                return $this->respuestaError($err);
            }
        );
    }

    /**
     * Modificar punto de atención
     * @ApiDoc(section="Punto atencion")
     * @param Request $request Datos necesarios para ejecutar la petición
     * @param integer $id Identificador único del punto de atención a modificar
     * @return mixed
     * @Put("/puntosatencion/{id}")
     */
    public function putAction(Request $request, $id)
    {
        $params = $request->request->all();
        $puntoAtencionServices = $this->getPuntoAtencionServices();
        return $puntoAtencionServices->edit(
            $params,
            $id,
            function ($puntoAtencion) {
                return $this->respuestaOk('Punto de atención modificado con éxito');
            },
            function ($err) {
                return $this->respuestaError($err);
            }
        );
    }

    /**
     * Eliminar punto de atención
     * @ApiDoc(section="Punto atencion")
     * @param integer $id Identificador único del punto de atención
     * @return mixed
     * @Delete("/puntosatencion/{id}")
     */
    public function deleteAction($id)
    {
        $puntoAtencionServices = $this->getPuntoAtencionServices();
        return $puntoAtencionServices->delete(
            $id,
            function ($puntoAtencion) {
                return $this->respuestaOk('Punto de atención eliminado con éxito');
            },
            function ($err) {
                return $this->respuestaError($err);
            }
        );
    }


    /**
     * Asigna un listado de tramites a un punto de atención
     * @ApiDoc(section="Punto atencion",
     * parameters={
     *      {"name"="tramites", "dataType"="array", "required"=true, "description"="Id tramites"}
     *  })
     * @param Request $request Datos necesarios para ejecutar la petición
     * @param integer $id Identificador único del punto de atención
     * @return  mixed
     * @Post("/puntosatencion/{id}/tramites")
     */
    public function setTramitesAction(Request $request, $id)
    {
        $params = $request->request->all();
        $puntoAtencionServices = $this->getPuntoAtencionServices();
        return $puntoAtencionServices->setTramites(
            $params,
            $id,
            function ($puntoAtencion) {
                return $this->respuestaOk('Punto de atención modificado con éxito');
            },
            function ($err) {
                return $this->respuestaError($err);
            }
        );
    }


    /**
     * Listado de trámites disponibles de un punto de atención y que no fueron asignados a un grupo de trámites
     * @ApiDoc(section="Punto atencion",
     * parameters={
     *      {"name"="offset", "dataType"="integer", "required"=false },
     *      {"name"="limit", "dataType"="integer", "required"=false }
     *  })
     * @param Request $request Datos necesarios para ejecutar la petición
     * @param integer $id Identificador único del punto de atención
     * @return mixed
     * @Get("/puntosatencion/{id}/tramitesdisponibles")
     */
    public function getTramitesDisponibles(Request $request, $id)
    {
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 10);
        return  $this->getPuntoAtencionServices()->findTramitesDisponiblesPaginate(
            $id,
            (int) $offset,
            (int) $limit,
            function ($err) {
                return $this->respuestaError($err);
            }
        );
    }

    /**
     * Cantidad de turnos de un punto de atención
     * @ApiDoc(section="Punto atencion")
     * @param integer $id Identificador único del punto de atención
     * @param date $fecha fecha a buscar
     * @return mixed
     * @Get("/puntosatencion/{id}/tieneturnos/{fecha}")
     */
    public function getCantidadTurnos($id, $fecha)
    {
        $puntoAtencionServices = $this->getPuntoAtencionServices();
        return $puntoAtencionServices->getCantidadTurnos(
            $id,
            $fecha,
            function ($err) {
                return $this->respuestaError($err);
            }
        );
    }

    /**
     * Habilita una fecha marcada como Feriado Nacional.
     * @ApiDoc(section="Punto atencion")
     * @param Request $request Datos necesarios para ejecutar la petición
     * @param integer $id Identificador único del punto de atención
     * @return mixed
     * @Post("/puntosatencion/{id}/habilitarFecha")
     */
    public function enableDateAction(Request $request, $id)
    {
        $params = $request->request->all();
        $puntoAtencionServices = $this->getPuntoAtencionServices();
        return $puntoAtencionServices->habilitarFecha(
            $params,
            $id,
            function ($puntoAtencion) {
                return $this->respuestaOk('La fecha fue habilitada con éxito.');
            },
            function ($err) {
                return $this->respuestaError($err);
            }
        );
    }

    /**
     * Agrega una fecha como día no laborable de un punto de atención.
     * @ApiDoc(section="Punto atencion")
     * @param Request $request Datos necesarios para ejecutar la petición
     * @param integer $id Identificador único del punto de atención
     * @return mixed
     * @Post("/puntosatencion/{id}/diaNoHabil")
     */
    public function addDateAction(Request $request, $id)
    {
        $params = $request->request->all();
        $puntoAtencionServices = $this->getPuntoAtencionServices();
        return $puntoAtencionServices->agregarDiaNoHabil(
            $params,
            $id,
            function ($puntoAtencion) {
                return $this->respuestaOk('La fecha fue ingresada como día no laborable.');
            },
            function ($err) {
                return $this->respuestaError($err);
            }
        );
    }

    /**
     * Listado de días no habiles incluyendo feriados de un punto de atención
     * @ApiDoc(section="Punto atencion")
     * @param integer $id Identificador único del punto de atención
     * @return mixed
     * @Get("/puntoAtencion/{id}/diasnolaborales")
     */
    public function getDiasNoLaborables($id)
    {
        $puntoAtencionServices = $this->getPuntoAtencionServices();
        return $puntoAtencionServices->getDiasNoLaborables(
            $id,
            function ($err) {
                return $this->respuestaError($err);
            }
        );
    }

    /**
     * Obtiene los ids de los puntos de atención
     *
     * @param Request $request Datos necesarios para ejecutar la petición
     * @return mixed
     * @Get("/integration/puntosatencion/ids")
     */
    public function getIdsPuntosAtencion(Request $request)
    {
        $authorization = $request->headers->get('authorization');
        $puntoAtencionServices = $this->getPuntoAtencionServices();
        return $puntoAtencionServices->getIdsPuntoAtencion(
            $authorization,
            function ($err) {
                return $this->respuestaError($err);
            });
    }

    /**
     * Endpoint para deshabilitar un día hábil sin verificar turnos
     *
     * @param Request $request
     * @Post("/puntosatencion/{puntoAtencionId}/inhabilitarfecha")
     * @return mixed
     */
    public function inhabilitarDia(Request $request, $puntoAtencionId)
    {
        $params = $request->request->all();
        $puntoAtencionServices = $this->getPuntoAtencionServices();
        return $puntoAtencionServices->inhabilitarDia(
            $puntoAtencionId,
            $params,
            function () {
                return $this->respuestaOk('día inhabilitado con exito');
            },
            function ($err) {
                return $this->respuestaError($err);
            }
        );
    }
    
    /**
     * actualizar datos del trámite para el punto de atención
     * @ApiDoc(section="Punto atencion")
     * @param Request $request Datos necesarios para ejecutar la petición
     * @param integer $puntoAtencionId Identificador único del punto de atención
     * @param integer $tramiteId identificador único del trámite
     * @return mixed
     * @Put("/puntosatencion/{puntoAtencionId}/tramites/{tramiteId}/update")
     */
    public function updatePDATramite(Request $request, $puntoAtencionId, $tramiteId)
    {
        
        $puntoAtencionServices = $this->getPuntoAtencionServices();
        $params = $request->request->all();
        return $puntoAtencionServices->editarPuntoTramite(
            $puntoAtencionId,
            $tramiteId,
            $params,
            function () {
                return $this->respuestaOk("PuntoTramite editado con éxito");
            },
            function ($err) {
                return $this->respuestaError($err);
            }
        );
    }
     /**
     * Obtiene un tramite
     * @ApiDoc(section="Punto atencion")
     * @param integer $id Identificador único del trámite
     * @return RespuestaConEstado
     * @Get("/puntotramite/puntosatencion/{puntoAtencionId}/tramites/{tramiteId}")
     */
    public function getPuntoTramiteItemAction($puntoAtencionId, $tramiteId)
    {
        $puntoAtencionServices = $this->getPuntoAtencionServices();
        $response = $puntoAtencionServices->getPuntoTramiteItem($puntoAtencionId, $tramiteId);
        if ($response->getResult()) {
            return $response;
        } else {
            return $this->respuestaNotFound(['errors' => ['Tramite no encontrado']]);
        }
    }
   
}
