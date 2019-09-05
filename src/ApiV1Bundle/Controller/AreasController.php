<?php
namespace ApiV1Bundle\Controller;

use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Put;
use Symfony\Component\HttpFoundation\Request;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * Class AreasController
 * @package ApiV1Bundle\Controller
 * @author Fausto Carrera <fcarrera@hexacta.com>
 */


class AreasController extends ApiController
{

    /**
     * Listado de areas
     * @ApiDoc(section="Area",
     *   parameters={
     *      {"name"="offset", "dataType"="integer", "required"=false}, 
     *      {"name"="limit", "dataType"="integer", "required"=false}
     *  })
     * @param Request $request Datos necesarios para ejecutar la petición
     * @param integer $organismoId Identificador único del organismo
     * @return mixed
     * @Get("/organismos/{organismoId}/areas")
     */
    public function getListAction(Request $request, $organismoId)
    {
        $authorization = $request->headers->get('Authorization', null);
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 10);
        $areaServices = $this->getAreaServices();
        return $areaServices->findAllPaginate(
            $organismoId,
            (int) $limit,
            (int) $offset,
            $authorization,
            function ($err) {
                return $this->respuestaError($err);
            }
        );
    }

    /**
     * Obtener un area
     * @ApiDoc(section="Area")
     * @param integer $organismoId Identificador único del organismo
     * @param integer $id Identificador único del área
     * @return mixed
     * @Get("/organismos/{organismoId}/areas/{id}")
     */
    public function getItemAction($organismoId, $id)
    {
        $areaServices = $this->getAreaServices();
        return $areaServices->get($organismoId, $id,
            function ($area) {
                return $this->respuestaData([], $area);
            },
            function () {
                return $this->respuestaNotFound("Área no encontrada");
            }
        );
    }

    /**
     * Obtiene los puntos de atención que pertenecen a un área
     * @ApiDoc(section="Area")
     * @param Request $request Datos necesarios para ejecutar la petición
     * @param integer $organismoId Identificador único del organismo
     * @param integer $id Identificador único del área
     * @return mixed
     * @Get("/organismos/{organismoId}/areas/{id}/puntoatencion")
     */
    public function getPuntosAtencionAction(Request $request, $organismoId, $id)
    {
        $authorization = $request->headers->get('Authorization', null);
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 10);
        $areaServices = $this->getAreaServices();
        return $areaServices->findPuntosAtencionPaginate(
            $organismoId,
            $id,
            (int) $limit,
            (int) $offset,
            $authorization,
            function ($err) {
                return $this->respuestaError($err);
            }
        );
    }

    /**
     * Obtiene los trámites que pertenecen a un área
     * @ApiDoc(section="Area")
     * @param Request $request Datos necesarios para ejecutar la petición
     * @param integer $organismoId Identificador único del organismo
     * @param integer $id Identificador único del área
     * @return mixed
     * @Get("/organismos/{organismoId}/areas/{id}/tramites")
     */
    public function getTramitesAction(Request $request, $organismoId, $id)
    {
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 10);
        $areaServices = $this->getAreaServices();

        return $areaServices->findTramitesPaginate($organismoId, $id, (int) $limit, (int) $offset);
    }

    /**
     * Buscar trámites del área
     * @ApiDoc(section="Area")
     * @param Request $request Datos provenientes del request
     * @param integer $organismoId Identificador único del organismo al que pertenece el área
     * @param integer $id Identificador único del área
     * @return mixed
     * @Get("/organismos/{organismoId}/areas/{id}/tramites/buscar")
     */

    public function searchTramiteAction(Request $request, $organismoId, $id)
    {
        $params = $request->query->all();
        return  $this->getAreaServices()->buscarTramites(
            $organismoId,
            $id,
            $params,
            function ($err) {
                return $this->respuestaError($err);
            }
        );
    }

    /**
     * Crea un área
     * @ApiDoc(section="Area",
     *   parameters={
     *      {"name"="nombre", "dataType"="string", "required"=true, "description"="Nombre del area"}, 
     *      {"name"="abreviatura", "dataType"="string", "required"=true, "description"="Abreviatura del area"}
     *  })
     * @param Request $request Datos necesarios para ejecutar la petición
     * @param integer $organismoId Identificador único del organismo
     * @return mixed
     * @Post("/organismos/{organismoId}/areas")
     */
    public function postAction(Request $request, $organismoId)
    {
        $params = $request->request->all();
        $areaServices = $this->getAreaServices();
        return $areaServices->create(
            $params,
            $organismoId,
            function ($area) {
                return $this->respuestaOk('Area creada con éxito', [
                    'id' => $area->getId()
                ]);
            },
            function ($err) {
                return $this->respuestaError($err);
            }
        );
    }

    /**
     * Modificar un area
     * @ApiDoc(section="Area",
     *   parameters={
     *      {"name"="nombre", "dataType"="string", "required"=true, "description"="Nombre del area"}, 
     *      {"name"="abreviatura", "dataType"="string", "required"=true, "description"="Abreviatura del area"}
     *  })
     * @param Request $request Datos necesarios para ejecutar la petición
     * @param integer $organismoId Identificador único del organismo
     * @param integer $id Identificador único del área
     * @return mixed
     * @Put("/organismos/{organismoId}/areas/{id}")
     */
    public function putAction(Request $request, $organismoId, $id)
    {
        $params = $request->request->all();
        $areaServices = $this->getAreaServices();
        return $areaServices->edit(
            $params,
            $organismoId,
            $id,
            function ($area) {
                return $this->respuestaOk('Area modificada con éxito');
            },
            function ($err) {
                return $this->respuestaError($err);
            }
        );
    }

    /**
     * Eliminar un area
     * @ApiDoc(section="Area")
     * @param integer $organismoId Identificador único del organismo
     * @param integer $id Identificador único del área
     * @return mixed
     * @Delete("/organismos/{organismoId}/areas/{id}")
     */
    public function deleteAction($organismoId, $id)
    {
        $areaServices = $this->getAreaServices();
        return $areaServices->delete(
            $organismoId,
            $id,
            function ($area) {
                return $this->respuestaOk('Area eliminada con éxito');
            },
            function ($err) {
                return $this->respuestaError($err);
            }
        );
    }

    /**
     * Buscar áreas por abreviatura y nombre
     * @ApiDoc(section="Area",
     *   parameters={
     *      {"name"="q", "dataType"="string", "required"=true, "description"="Nombre o abreviatura del area"}
     *  })
     * @param Request $request Datos necesarios para ejecutar la petición
     * @param integer $organismoId Ientificador único del organismo
     * @return mixed
     * @Get("/organismos/{organismoId}/area/buscar")
     */
    public function searchAction(Request $request, $organismoId)
    {
        $params = $request->query->all();
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 10);
        return  $this->getAreaServices()->search(
            $params,
            (int) $offset,
            (int) $limit,
            function ($err) {
                return $this->respuestaError($err);
            }
        );
    }
}
