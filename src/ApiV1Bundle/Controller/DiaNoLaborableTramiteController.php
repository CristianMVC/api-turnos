<?php
namespace ApiV1Bundle\Controller;

use ApiV1Bundle\ApplicationServices\UsuarioServices;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\Delete;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class DiasNoLaborableesController
 * @package ApiV1Bundle\Controller
 */
class DiaNoLaborableTramiteController extends ApiController
{
 
  
     /**
     * Agrega una fecha como día no laborable de un punto de atención.
     *
     * @param Request $request Datos necesarios para ejecutar la petición
     * @param integer $id Identificador único del punto de atención
     * @return mixed
     * @Post("/puntosatencion/{id}/diaNoHabil/{tramite_id}")
     */
    public function addDateTramiteAction(Request $request, $id, $tramite_id) {
        $params = $request->request->all();
        $services = $this->getDiaNoLaborableTramiteServices();
        return $services->create(
                        $params, $id, $tramite_id, function ($services) {
                    return $this->respuestaOk('La fecha fue ingresada como día no laborable.');
                }, function ($err) {
                    return $this->respuestaError($err);
                }
        );
    }
    
    /**
     * Listado de días no habiles incluyendo feriados de un punto de atención
     *
     * @param integer $id Identificador único del punto de atención
     * @return mixed
     * @Get("/puntoAtencion/{id}/diasnolaborales/{tramite_id}")
     */
    public function getDiasNoLaborables($id, $tramite_id) {
        $services = $this->getDiaNoLaborableTramiteServices();
        return $services->getDiasNoLaborables(
                        $id, $tramite_id, function ($err) {
                    return $this->respuestaError($err);
                }
        );
    }

    /**
     * Habilita una fecha marcada como Feriado Nacional.
     *
     * @param Request $request Datos necesarios para ejecutar la petición
     * @param integer $id Identificador único del punto de atención
     * @return mixed
     * @Post("/puntosatencion/{id}/habilitarFecha/tramite/{idTramite}")
     */
    public function enableDateAction(Request $request, $id, $idTramite) {
        $params = $request->request->all();
        $services = $this->getDiaNoLaborableTramiteServices();
        return $services->habilitarFechaTramite(
                        $params, $id, $idTramite, function () {
                    return $this->respuestaOk('La fecha fue habilitada con éxito.');
                }, function ($err) {
                    return $this->respuestaError($err);
                }
        );
    }
    
    
    /**
     * Endpoint para deshabilitar un día hábil sin verificar turnos
     *
     * @param Request $request
     * @Post("/puntosatencion/{puntoAtencionId}/inhabilitarfecha/tramite/{idTramite}")
     * @return mixed
     */
    public function inhabilitarDia(Request $request, $puntoAtencionId, $idTramite)
    {
        $params = $request->request->all();
        $services = $this->getDiaNoLaborableTramiteServices();
        return $services->inhabilitarDia(
            $puntoAtencionId,
            $idTramite,
            $params,
            function () {
                return $this->respuestaOk('día inhabilitado con exito');
            },
            function ($err) {
                return $this->respuestaError($err);
            }
        );
    }

}
