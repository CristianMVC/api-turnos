<?php
namespace ApiV1Bundle\Controller;

use ApiV1Bundle\Entity\PuntoAtencion;
use ApiV1Bundle\Entity\Response\RespuestaConEstado;
use ApiV1Bundle\Entity\Turno;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Put;
use Symfony\Component\HttpFoundation\Request;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;


/**
 * Class TurnoController
 * @package ApiV1Bundle\Controller
 *
 * Turnos
 * @author Fausto Carrera <fcarrera@hexacta.com>
 */
class TurnoController extends ApiController
{
    
    /** @var \ApiV1Bundle\ApplicationServices\SecurityServices $securityServices */
    private $securityServices;
    /**
     * Listado de turnos
     * @ApiDoc(section="Turno",
     * parameters={
     *      {"name"="puntoatencion", "dataType"="integer", "required"=true, "description"="Id punto"},
     *      {"name"="tramite", "dataType"="integer", "required"=true, "description"="Id tramite"},
     *      {"name"="offset", "dataType"="integer", "required"=true},
     *      {"name"="limit", "dataType"="integer", "required"=true}
     *  })
     * @param Request $request Datos necesarios para ejecutar la petición
     * @return mixed
     * @Get("/turnos")
     */
    public function getListAction(Request $request)
    {

        $turnoServices = $this->getTurnoServices();

        $where = [];
        $where['areaId'] = $request->get('area', null);
        $where['puntoAtencionId'] = $request->get('puntoatencion', null);
        $where['tramiteId'] = $request->get('tramite', null);
        $where['estado'] = $request->get('estado', null);
        $where['fechaDesde'] = $request->get('fechaDesde', null);
        $where['fechaHasta'] = $request->get('fechaHasta', null);

        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 10);

        return $turnoServices->findAllPaginate((int) $limit, (int) $offset, $where);
    }

    /**
     * Obtener turno desde el SNC
     *
     * @param Request $request Datos necesarios para ejecutar la petición
     * @return mixed
     * @Post("/integracion/turnos")
     */
    public function getTurnoSncAction(Request $request)
    {
        $params = $request->request->all();
        $turnoServices = $this->getTurnoServices();
        $response = $turnoServices->getTurnoSnc($params);
        if ($response->getResult()) {
            return $response;
        } else {
            return $this->respuestaNotFound('Turno no encontrado');
        }
    }

    /**
     * Buscar un turno por código y cuil - endpoint de integración
     * @ApiDoc(section="Turno",
     * parameters={
     *      {"name"="cuil", "dataType"="string", "required"=true, "description"="cuil"},
     *      {"name"="codigo", "dataType"="string", "required"=true, "description"="codigo"}
     *  })
     * @param Request $request Datos necesarios para ejecutar la petición
     * @return mixed
     * @Post("/integracion/turnos/buscar")
     */
    public function searchByCodigoAction(Request $request)
    {
        $params = $request->request->all();
        $turnoServices = $this->getTurnoServices();
        return  $turnoServices->searchByCodigoCuil(
            $params,
            function ($err) {
                return $this->respuestaError($err);
            }
        );
    }

    /**
     * Obtener listado de Turnos - endpoint de integración
     * @ApiDoc(section="Turno",
     * parameters={
     *      {"name"="puntoatencion", "dataType"="integer", "required"=true, "description"="Id punto"},
     *      {"name"="fecha", "dataType"="date", "required"=true, "description"="Fecha"},
     *      {"name"="offset", "dataType"="integer", "required"=true},
     *      {"name"="codigo", "dataType"="integer", "required"=true}
     *  })
     * @param Request $request Datos necesarios para ejecutar la petición
     * @return mixed
     * @Post("/integracion/turnos/fecha")
     */
    public function getListByFechaAction(Request $request)
    {
        $params = $request->request->all();
        $puntoAtencionId = $request->get('puntoatencion', null);
        $fecha = $request->get('fecha', null);
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 10);
        $codigosTurnos = $request->get('codigos', []);
        $turnoServices = $this->getTurnoServices();
        return $turnoServices->findTurnosBySnc(
            $puntoAtencionId,
            $fecha,
            $offset,
            $limit,
            $codigosTurnos,
            $params,
            function ($err) {
                return $this->respuestaError($err);
            }
        );
    }
    /**
     * Buscar un turno por código
     * @ApiDoc(section="Turno",
     * parameters={
     *      {"name"="cuil", "dataType"="string", "required"=true, "description"="cuil"},
     *      {"name"="codigo", "dataType"="string", "required"=true, "description"="Ej: fd7d4930"}
     *  })
     * @param Request $request Datos necesarios para ejecutar la petición
     * @return mixed
     * @Get("/turnos/buscar")
     */
    public function searchAction(Request $request)
    {
        $params = $request->query->all();
        return  $this->getTurnoServices()->search(
            $params,
            function ($err) {
                return $this->respuestaError($err);
            }
        );
    }

    /**
     * Obtiene el último turno del punto de atención
     * @ApiDoc(section="Turno",
     * parameters={
     *      {"name"="puntoatencion", "dataType"="integer", "required"=true, "description"="Id punto"},
     *      {"name"="tramite", "dataType"="integer", "required"=true, "description"="Id tramite"},
     *      {"name"="grupotramites", "dataType"="integer", "required"=true, "description"="Id grupo"}
     *  })
     * @param Request $request Datos necesarios para ejecutar la petición
     * @return mixed
     * @Get("/turnos/ultimoturno")
     */
    public function getUltimoTurno(Request $request)
    {
        $puntoAtencionId = $request->get('puntoatencion', null);
        $tramiteId = $request->get('tramite', null);
        $grupoTramitesId = $request->get('grupotramites', null);
        $turnoServices = $this->getTurnoServices();
        return $turnoServices->getUltimoTurno(
            $puntoAtencionId,
            $grupoTramitesId,
            $tramiteId,
            function ($err) {
                return $this->respuestaError($err);
            }
        );
    }

    /**
     * Obtener listado de Turnos por ciudadano
     * @ApiDoc(section="Turno",
     * parameters={
     *      {"name"="dni", "dataType"="integer", "required"=true, "description"="dni"},
     *      {"name"="offset", "dataType"="integer", "required"=true},
     *      {"name"="limit", "dataType"="integer", "required"=true}
     *  })
     * @param Request $request Datos necesarios para ejecutar la petición
     * @return mixed
     * @Get("/turnos/ciudadano/dni")
     */
    public function getListByCiudadanoDni(Request $request)
    {
        $turnoServices = $this->getTurnoServices();
        $params = $request->query->all();

        return $turnoServices->findAllCiudadanoPorDni(
            $params,
            function ($err) {
                return $this->respuestaError($err);
            }
        );
    }

    /**
     * Obtener listado de Turnos por ciudadano
     * @ApiDoc(section="Turno",
     * parameters={
     *      {"name"="cuil", "dataType"="integer", "required"=true, "description"="cuil"},
     *      {"name"="offset", "dataType"="integer", "required"=true},
     *      {"name"="limit", "dataType"="integer", "required"=true}
     *  })
     * @param Request $request Datos necesarios para ejecutar la petición
     * @return mixed
     * @Get("/turnos/ciudadano")
     */
    public function getListByCiudadano(Request $request)
    {
        $turnoServices = $this->getTurnoServices();
        $params = $request->query->all();

        return $turnoServices->findAllCiudadano(
            $params,
            function ($err) {
                return $this->respuestaError($err);
            }
        );
    }

    /**
     * Cancelar turnos por fecha y punto de atención
     * @ApiDoc(section="Turno",
     * parameters={
     *      {"name"="fecha", "dataType"="date", "required"=true, "description"="Fecha"},
     *      {"name"="puntoatencion", "dataType"="integer", "required"=true, "description"="Id punto"},
     *      {"name"="motivo", "dataType"="string", "required"=true, "description"="Sin nungun motivo aparente"}
     *  })
     * @param Request $request Datos necesarios para ejecutar la petición
     * @return mixed
     * @Post("/turnos/batch/cancelar")
     */
    public function cancelarTurnos(Request $request)
    {
        $params = $request->request->all();
        $turnoServices = $this->getTurnoServices();
        return $turnoServices->cancelByDate(
            $params,
            function ($response, $notificacion) {
                return $this->respuestaOk('Turnos cancelados con éxito', [
                    'notificacion' => $notificacion
                ]);
            },
            function ($error) {
                return $this->respuestaError($error);
            }
        );
    }

    /**
     * Obtener turno
     * @ApiDoc(section="Turno")
     * @param integer $id Identificador único del turno a obtener
     * @return mixed
     * @Get("/turnos/{id}")
     */
    public function getTurnoAction($id)
    {
        $turnoServices = $this->getTurnoServices();
        $response = $turnoServices->get($id);
        if ($response->getResult()) {
            return $response;
        } else {
            return $this->respuestaNotFound('Turno no encontrado');
        }
    }

    /**
     * Modificar turno
     * @ApiDoc(section="Turno",
     * parameters={
     *      {"name"="codigo", "dataType"="string", "required"=true, "description"="Ej: fd7d4930"},
     *      {"name"="fecha", "dataType"="date", "required"=true, "description"="Nueva fecha"},
     *  })
     * @param Request $request Datos necesarios para ejecutar la petición
     * @return mixed
     * @Put("/turnos/modificar")
     */
    public function modificarAction(Request $request)
    {
        $params = $request->request->all();
        $turnoServices = $this->getTurnoServices();
        return $turnoServices->modificar(
            $params,
            function ($response, $notification) {
                return $this->respuestaOk(
                    'Turno modificado con éxito',
                    ['notification' => $notification]
                );
            },
            function ($err) {
                return $this->respuestaError($err);
            }
        );
    }

    /**
     * Crear un turno
     * @ApiDoc(section="Turno",
     * parameters={
     *      {"name"="puntoatencion", "dataType"="integer", "required"=true, "description"="Id punto"},
     *      {"name"="tramite", "dataType"="integer", "required"=true, "description"="Id tramite"},
     *      {"name"="fecha", "dataType"="date", "required"=true, "description"="Nueva fecha"},
     *      {"name"="hora", "dataType"="string", "required"=true, "description"="Ej: 09:00"},
     *      {"name"="alerta", "dataType"="integer", "required"=true, "description"="0 o 1"}
     *  })
     * @param Request $request Datos necesarios para ejecutar la petición
     * @return mixed
     * @Post("/turnos")
     */
    public function postAction(Request $request)
    {

        $params = $request->request->all();
        $token = $request->headers->get('authorization', null);
        $user = $this->geLoggedUser($token);

        $turnoServices = $this->getTurnoServices();
        return $turnoServices->create(
            $params, $user,
            function ($turno) {
                return $this->respuestaOk(
                    'Turno creado con éxito',
                    ['id' => $turno->getId(), 'codigo' => $turno->getCodigo()]
                );
            },
            function ($err) {
                return $this->respuestaError($err);
            }
        );
    }

    /**
     * Confirmar turno
     * @ApiDoc(section="Turno",
     * parameters={
     *      {"name"="alerta", "dataType"="integer", "required"=true, "description"="Numero alerta"},
     *      {"name"="cuil", "dataType"="integer", "required"=false, "description"="Numero cuil"},
     *      {"name"="campos", "dataType"="string", "required"=true, "description"="Campos"}
     *  })
     * @param Request $request Datos necesarios para ejecutar la petición
     * @param integer $id Identificador único del turno
     * @return mixed
     * @Put("/turnos/{id}/confirmar")
     */
    public function putAction(Request $request, $id)
    {

        $params = $request->request->all();
        $params['cuilSolicitante'] = $request->get('cuilSolicitante', null);
        $turnoServices = $this->getTurnoServices();
        return $turnoServices->edit(
            $params,
            $id,
            function ($response, $notification) {
                return $this->respuestaOk(
                    'Turno confirmado con éxito',
                    ['notification' => $notification]
                );
            },
            function ($err) {
                return $this->respuestaError($err);
            }
        );
    }

    /**
     * Eliminar un turno
     * @ApiDoc(section="Turno")
     * @param integer $id Identificador único del turno
     * @return mixed
     * @Delete("/turnos/{id}")
     */
    public function deleteAction($id)
    {
        $turnoServices = $this->getTurnoServices();
        return $turnoServices->delete(
            $id,
            function ($turno) {
                return $this->respuestaOk('Turno eliminado con éxito');
            },
            function ($err) {
                return $this->respuestaError($err);
            }
        );
    }

    /**
     * Cancelar un turno
     * @ApiDoc(section="Turno")
     * @param string $codigo código del turno a cancelar
     * @return mixed
     * @Put("/turnos/{codigo}/cancelar")
     */
    public function cancelarAction($codigo)
    {
        $turnoServices = $this->getTurnoServices();
        return $turnoServices->cancel(
            $codigo,
            function ($turno) {
                return $this->respuestaOk('Turno cancelado con éxito');
            },
            function ($err) {
                return $this->respuestaError($err);
            }
        );
    }

    /**
     * Obtener listado grupo tramites de los turnos a reasignar
     * @ApiDoc(section="Turno",
     * parameters={
     *      {"name"="fecha", "dataType"="date", "required"=true, "description"="Fecha a reasignar"}
     *  })
     * @param Request $request Datos necesarios para ejecutar la petición
     * @return mixed
     * @Get("/turnos/{puntoAtencionId}/reasignacion")
     */
    public function getReasignacionGrupoTramites(Request $request, $puntoAtencionId)
    {
        $turnoServices = $this->getTurnoServices();
        $params = $request->query->all();

        return $turnoServices->findTurnosReasignacion(
            $puntoAtencionId,
            $params,
            function ($err) {
                return $this->respuestaError($err);
            }
        );
    }

    /**
     * 
     * @ApiDoc(section="Turno")
     * Obtener fechas de reasignación agrupadas por grupo de trámites
     * @param Request $request Datos necesarios para ejecutar la petición
     * @param integer $puntoAtencionId identificador único de punto de atención
     * @param integer $grupoTramiteId identificador único de grupo trámite
     * @return mixed
     * @Get("/turnos/{puntoAtencionId}/reasignacion/{grupoTramiteId}")
     */
    public function getFechasReasignacionGrupoTramites(Request $request, $puntoAtencionId, $grupoTramiteId)
    {
        $turnoServices = $this->getTurnoServices();
        $params = $request->query->all();

        return $turnoServices->findFechasReasignacion(
            $puntoAtencionId,
            $grupoTramiteId,
            null,
            $params,
            function ($err) {
                return $this->respuestaError($err);
            }
        );
    }

    /**
     * 
     * @ApiDoc()
     * Obtener fechas de reasignación agrupadas por grupo de trámites
     * @param Request $request Datos necesarios para ejecutar la petición
     * @param integer $puntoAtencionId identificador único de punto de atención
     * @param integer $grupoTramiteId identificador único de grupo trámite
     * @return mixed
     * @Get("/turnos/{puntoAtencionId}/reasignacion/{grupoTramiteId}/tramite/{tramiteId}")
     */
    public function getFechasReasignacionGrupoTramitesPorTramite(Request $request, $puntoAtencionId, $grupoTramiteId, $tramiteId)
    {
        $turnoServices = $this->getTurnoServices();
        $params = $request->query->all();

        return $turnoServices->findFechasReasignacion(
            $puntoAtencionId,
            $grupoTramiteId,
            $tramiteId, 
            $params,
            function ($err) {
                return $this->respuestaError($err);
            }
        );
    }
    /**

     * Reasignar turnos por grupo de trámites
     * @ApiDoc(section="Turno",
     * parameters={
     *      {"name"="fecha", "dataType"="date", "required"=true, "description"="Fecha a reasignar"},
     *      {"name"="puntoatencion", "dataType"="integer", "required"=true, "description"="Id punto"}
     *  })
     * @param Request $request Datos necesarios para ejecutar la petición
     * @param integer $puntoAtencionId identificador único de punto de atención
     * @return mixed
     * @Put("/turnos/{puntoAtencionId}/reasignacion")
     */
    public function reasignarTurnos(Request $request, $puntoAtencionId)
    {
        $turnoServices = $this->getTurnoServices();
        $params = $request->request->all();

        return $turnoServices->reasignarTurnos(
            $puntoAtencionId,
            $params,
            function () {
                return $this->respuestaOk('Turnos reasignados con éxito');
            },
            function ($err) {
                return $this->respuestaError($err);
            }
        );
    }
    

    
        /**
     * @ApiDoc(section="Turno")
     * @param type $token
     * @return mixed
     */
    private function geLoggedUser($token) {
        if($token){
            $securityServices = $this->getSecurityServices();
            $user = $securityServices->validToken(
                    $token);
            return $user;
        }
        return null;
    }
    
    
    /**
     * Crear Multiples turnos <br>
     * Example: <code>{  "puntoatencion": "6",  "tramite": "12",  "fecha": "2019-08-22",  "horas": ["11:00:00","11:15:00"]}</code>
     * @ApiDoc(section="Turno",
     * parameters={
     *      {"name"="puntoatencion", "dataType"="integer", "required"=true, "description"="Id punto"},
     *      {"name"="tramite", "dataType"="integer", "required"=true, "description"="Id tramite"},
     *      {"name"="fecha", "dataType"="date(Y-m-d)", "required"=true, "description"="fecha"},
     *      {"name"="horas", "dataType"="array", "required"=true, "description"="Coleccion de Horas   'horas': ['11:00:00','11:15:00']"}
     *  })
     * @param Request $request Datos necesarios para ejecutar la petición
     * @return mixed
     * @Post("/turnos/multiples")
     */
    public function postMultiplesAction(Request $request){
        $params = $request->request->all();
        $token = $request->headers->get('authorization', null);
        $user = $this->geLoggedUser($token);
        $turnoServices = $this->getTurnoServices();
        return $turnoServices->createMultiple(
            $params, $user,
            function ($turnos) {
            $resp = [];
                foreach ($turnos as $turno) {
                    $resp[] =  ['id' => $turno->getId(), 'codigo' => $turno->getCodigo()];
                }
                return $this->respuestaOk(
                    'Turnos creados con éxito',
                    $resp
                );
            },
            function ($err) {
                return $this->respuestaError($err);
            }
        );
    }
    
    
    /**
     * Confirmar Multiples turnos <br>
     *  ejemplo : <code>{ "turnos": 
     * [  
     *    {"alerta": 1, "id": 96,"codigo": "bcb8414a-c806-4331-b846-c430229ebf27","campos":{"email":"juan.perez@gmail.com","cuil":"20342114335","nombre":"Juan","apellido":"Perez",
     *    	"multiturnos":[
     *		{"email":"juan.perez@gmail.com","cuil":"20342114335","nombre":"Juan","apellido":"Perez"},
     *		{"email":"juan.perez@gmail.com","cuil":"20342114335","nombre":"Juan","apellido":"Perez"},
     *		{"email":"juan.perez@gmail.com","cuil":"20342114335","nombre":"Juan","apellido":"Perez"}
     *		]
     *	 }
     *     }},
     *    {"alerta": 1,"id": 97,"codigo": "2bddfb48-d690-4f81-a0ed-637b7f753449", "campos":{"email":"jose.perez@gmail.com","cuil":"20222978867","nombre":"jose","apellido":"Perez" }}
     * ],
     * "cuilSolicitante":"20342114335",
     * "email":"juan.perez@gmail.com"}</code>
     * @ApiDoc(section="Turno",
     * parameters={
     *      {"name"="cuilSolicitante", "dataType"="string", "required"=true, "description"="cuil para enviar el mail"},
     *      {"name"="email", "dataType"="string", "required"=true, "description"="email para enviar el mail"},
     *      {"name"="turnos", "dataType"="array", "required"=true, "description"="Turnos {id,alerta,codigo,campos{email,cuil,nombre,apellido,turnos:[{}]}}"}
     *  })
     * @param Request $request Datos necesarios para ejecutar la petición
     * @param integer $id Identificador único del turno
     * @return mixed
     * @Put("/turnos/multiples")
     */
    public function putMultipleAction(Request $request)
    {

        $params = $request->request->all();
        $turnoServices = $this->getTurnoServices();
        return $turnoServices->editMultiple(
            $params,            
            function ($turnos, $notification) {
            $resp = [];
                foreach ($turnos as $turno) {
                    $resp[] =  ['id' => $turno->getId(), 'codigo' => $turno->getCodigo()];
                }
                return $this->respuestaOk(
                    'Turnos confirmado con éxito',
                    $resp,
                    ['notification' => $notification]
                );
            },
            
            function ($err) {
                return $this->respuestaError($err);
            }
        );
    }

}
