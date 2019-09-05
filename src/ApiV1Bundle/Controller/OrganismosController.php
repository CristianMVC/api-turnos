<?php
namespace ApiV1Bundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use ApiV1Bundle\Entity\Response\RespuestaConEstado;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\Delete;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;


/**
 * Class OrganismosController
 * @package ApiV1Bundle\Controller
 *
 * Organismos
 * @author Fausto Carrera <fcarrera@hexacta.com>
 */

class OrganismosController extends ApiController
{

    /**
     * Listado de organismos
     * @ApiDoc(section="Organismo")
     * @param Request $request Datos necesarios para ejecutar la petición
     * @return mixed
     * @Get("/organismos")
     */
    public function getListAction(Request $request)
    {
        $authorization = $request->headers->get('Authorization', null);
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 10);
        $organismoServices = $this->getOrganismoServices();
        return $organismoServices->findAllPaginate(
            (int) $offset,
            (int) $limit,
            $authorization,
            function ($err) {
                return $this->respuestaError($err);
            }
        );
    }

    /**
     * Buscar un organismo por nombre y abreviatura
     * @ApiDoc(section="Organismo",
     * parameters={
     *      {"name"="q", "dataType"="string", "required"=true, "description"="Nombre a buscar"}
     *  })
     * @param Request $request Array con los datos necesarios para bucar el organismo
     * @return mixed
     * @GET("/organismos/buscar")
     */
    public function searchAction(Request $request)
    {
        $params = $request->query->all();
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 10);
        return  $this->getOrganismoServices()->search(
            $params,
            (int) $offset,
            (int) $limit,
            function ($err) {
                return $this->respuestaError($err);
            }
        );
    }

    /**
     * Obtener un organismo
     * @ApiDoc(section="Organismo")
     * @param integer $id Identificador único del organismo
     * @return mixed
     * @Get("/organismos/{id}")
     */
    public function getItemAction($id)
    {
        $organismoServices = $this->getOrganismoServices();
        return $organismoServices->get(
            $id,
            function ($organismo) {
                return $this->respuestaData([], $organismo);
            },
            function () {
                return $this->respuestaNotFound("Organismo no encontrado");
            }
        );
    }

    /**
     * Crear un organismo
     * @ApiDoc(section="Organismo",
     * parameters={
     *      {"name"="nombre", "dataType"="string", "required"=true, "description"="Nombre del organismo"},
     *      {"name"="abreviatura", "dataType"="string", "required"=true, "description"="Abreviatura"}
     *  })
     * @param Request $request Datos necesarios para ejecutar la petición
     * @return mixed
     * @Post("/organismos")
     */
    public function postAction(Request $request)
    {
        $params = $request->request->all();
        $organismoServices = $this->getOrganismoServices();
        return $organismoServices->create(
            $params,
            function ($organismo) {
                return $this->respuestaOk('Organismo creado con éxito', [
                    'id' => $organismo->getId()
                ]);
            },
            function ($err) {
                return $this->respuestaError($err);
            }
        );
    }

    /**
     * Modificar un organismo
     * @ApiDoc(section="Organismo",
     * parameters={
     *      {"name"="nombre", "dataType"="string", "required"=true, "description"="Nombre del organismo"},
     *      {"name"="abreviatura", "dataType"="string", "required"=true, "description"="Abreviatura"}
     *  })
     * @param Request $request Datos necesarios para ejecutar la petición
     * @param integer $id Identificador único del organismo a modificar
     * @return mixed
     * @Put("/organismos/{id}")
     */
    public function putAction(Request $request, $id)
    {
        $params = $request->request->all();
        $organismoServices = $this->getOrganismoServices();
        return $organismoServices->edit(
            $params,
            $id,
            function ($organismo) {
                return $this->respuestaOk('Organismo modificado con éxito');
            },
            function ($err) {
                return $this->respuestaError($err);
            }
        );
    }

    /**
     * Eliminar un organismo
     * @ApiDoc(section="Organismo")
     * @param integer $id Identificador único del organismo a eliminar
     * @return mixed
     * @Delete("/organismos/{id}")
     */
    public function deleteAction($id)
    {
        $organismoServices = $this->getOrganismoServices();
        return $organismoServices->delete(
            $id,
            function ($organismo) {
                return $this->respuestaOk('Organismo eliminado con éxito');
            },
            function ($err) {
                return $this->respuestaError($err);
            }
        );
    }
}
