<?php
/**
 * Created by
 * User: jtibi
 * Date: 1/8/2017
 * Time: 3:22 PM
 */

namespace ApiV1Bundle\Controller;

use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\Route;
use Symfony\Component\HttpFoundation\Request;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
/**
 * Class GrupoTramiteController
 * @package ApiV1Bundle\Controller
 */

class GrupoTramiteController extends ApiController
{

    /**
     * Obtener listado de grupo de tramites
     * @ApiDoc(section="Grupo de tramites",
     *   parameters={
     *      {"name"="offset", "dataType"="integer", "required"=false},
     *      {"name"="limit", "dataType"="integer", "required"=false}
     *  })
     * @param Request $request Datos necesarios para ejecutar la petición
     * @param integer $puntoAtencionId Identificador único del punto de atención a obtener
     * @return mixed
     * @Get("/puntosatencion/{puntoAtencionId}/grupostramites")
     */
    public function getListAction(Request $request, $puntoAtencionId)
    {
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 10);
        $grupoTramiteServices = $this->getGrupoTramiteServices();
        return $grupoTramiteServices->findAllPaginate($puntoAtencionId, (int) $limit, (int) $offset);
    }

    /**
     * Obtener grupo de tramites
     * @ApiDoc(section="Grupo de tramites")
     * @param integer $puntoAtencionId Identificador único del punto de atención a obtener
     * @param integer $id Identificador único del grupo de tramites
     * @return mixed
     * @Get("/puntosatencion/{puntoAtencionId}/grupostramites/{id}")
     */
    public function getItemAction($puntoAtencionId, $id)
    {
        $grupoTramiteServices = $this->getGrupoTramiteServices();
        return $grupoTramiteServices->get(
            $puntoAtencionId,
            $id,
            function ($gdt) {
                return $this->respuestaData([], $gdt);
            },
            function () {
                return $this->respuestaNotFound("Grupo de trámite inexistente");
            }
        );
    }

    /**
     * Crea un grupo de tramites
     * @ApiDoc(section="Grupo de tramites",
     *   parameters={
     *      {"name"="nombre", "dataType"="string", "required"=true, "description"="Nombre grupo"},
     *      {"name"="horizonte", "dataType"="integer", "required"=true},
     *      {"name"="tramites", "dataType"="array", "required"=true, "description"="[45,13,17,46]"},
     *      {"name"="intervalo", "dataType"="integer", "required"=true }
     *  })
     * @param Request $request Datos necesarios para ejecutar la petición
     * @param integer $puntoAtencionId Identificador único del punto de atención
     * @return mixed
     * @Post("/puntosatencion/{puntoAtencionId}/grupostramites")
     */
    public function postAction(Request $request, $puntoAtencionId)
    {
        $params = $request->request->all();
        $grupoTramiteServices = $this->getGrupoTramiteServices();
        return $grupoTramiteServices->create(
            $params,
            $puntoAtencionId,
            function ($grupoTramites) use ($grupoTramiteServices) {
                return $this->respuestaOk('Grupo de trámites creado con éxito', [
                    'id' => $grupoTramites->getId()
                ]);
            },
            function ($err) {
                return $this->respuestaError($err);
            }
        );
    }

    /**
     * Modificar un grupo de tramites
     * @ApiDoc(section="Grupo de tramites",
     *   parameters={
     *      {"name"="nombre", "dataType"="string", "required"=true, "description"="Nombre grupo"},
     *      {"name"="horizonte", "dataType"="integer", "required"=true},
     *      {"name"="tramites", "dataType"="array", "required"=true, "description"="[45,13,17,46]"},
     *      {"name"="intervalo", "dataType"="integer", "required"=true }
     *  })
     * @param Request $request Datos necesarios para ejecutar la petición
     * @param integer $puntoAtencionId Identificador único del punto de atención
     * @param integer $id Identificador único del grupo de trámites
     * @return mixed
     * @Put("/puntosatencion/{puntoAtencionId}/grupostramites/{id}")
     */
    public function putAction(Request $request, $puntoAtencionId, $id)
    {
        $params = $request->request->all();
        $grupoTramiteServices = $this->getGrupoTramiteServices();
        return $grupoTramiteServices->edit(
            $params,
            $puntoAtencionId,
            $id,
            function () {
                return $this->respuestaOk('Grupo de trámites modificado con éxito');
            },
            function ($err) {
                return $this->respuestaError($err);
            }
        );
    }

    /**
     * Asignar tramites al grupo de tramites
     * @ApiDoc(section="Grupo de tramites",
     *   parameters={
     *      {"name"="tramites", "dataType"="array", "required"=true, "description"="[45,13,17,46]"}
     *  })
     * @param integer $puntoAtencionId Identificador único del punto de atención a obtener
     * @param integer $id Identificador único del grupo de tramites
     * @param Request $request Datos necesarios para ejecutar la petición
     * @return mixed
     * @Post("/puntosatencion/{puntoAtencionId}/grupostramites/{id}/tramites")
     */
    public function tramitesAction(Request $request, $puntoAtencionId, $id)
    {
        $params = $request->request->all();
        $grupoTramiteServices = $this->getGrupoTramiteServices();
        return $grupoTramiteServices->addTramites(
            $params,
            $puntoAtencionId,
            $id,
            function () {
                return $this->respuestaOk('Tramites asignados con éxito');
            },
            function ($err) {
                return $this->respuestaError($err);
            }
        );
    }

    /**
     * Eliminar grupo tramite
     *
     * @param integer $puntoAtencionId ID del punto de atención
     * @param integer $id Identificador único del grupo de trámite
     * @return mixed
     * @Delete("/puntosatencion/{puntoAtencionId}/grupostramites/{id}")
     */
    public function deleteAction($puntoAtencionId, $id)
    {
        $grupoTramiteServices = $this->getGrupoTramiteServices();
        return $grupoTramiteServices->delete(
            $puntoAtencionId,
            $id,
            function () {
                return $this->respuestaOk('Grupo de trámites eliminado con éxito');
            },
            function ($err) {
                return $this->respuestaError($err);
            }
        );
    }
}
