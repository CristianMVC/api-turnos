<?php
namespace ApiV1Bundle\Controller;

use ApiV1Bundle\Entity\Response\RespuestaConEstado;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\Route;
use Symfony\Component\HttpFoundation\Request;
use TotemV1Bundle\ApplicationServices\TramiteServices;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * Class TramiteController
 * @package ApiV1Bundle\Controller
 *
 * Tramites
 *
 * @author Javier Ignacio Tibi <jtibi@hexacta.com>
 */

class TramiteController extends ApiController
{
    /** @var TramiteServices */
    private $tramiteServices;

    /**
     * Obtiene el listado de tramites
     * @ApiDoc(section="Tramite",
     * tags={"miArgentina"},
     * parameters={
     *      {"name"="limit", "dataType"="integer", "required"=false},
     *      {"name"="offset", "dataType"="integer", "required"=false}
     *  })
     * @param Request $request Datos necesarios para ejecutar la petición
     * @return mixed
     * @Get("/tramites")
     */
    public function getTramitesAction(Request $request)
    {
        $query = $request->query->get('q');
        $limit = $request->query->get('limit',10);
        $offset = $request->query->get('offset',0);
        $this->tramiteServices = $this->getTramiteServices();
        return $this->tramiteServices->findAllPaginate($query, (int) $limit, (int) $offset);
    }

    /**
     * Obtiene tramite
     * @ApiDoc(section="Tramite")
     * @param integer $id Identificador único del trámite
     * @return mixed
     * @Get("/tramites/{id}")
     */
    public function getItemAction($id)
    {
        $this->tramiteServices = $this->getTramiteServices();
        return $this->tramiteServices->get($id,
            function ($tramite) {
                return $this->respuestaData([], $tramite);
            },
            function () {
                return $this->respuestaNotFound("Trámite inexistente");
            });
    }

    /**
     * Obtener los campos del formulario
     * @ApiDoc(section="Tramite")
     * @param integer $id Identificador único del trámite
     * @return mixed
     * @Get("/tramites/{id}/formulario")
     */
    public function getCamposAction($id)
    {
        $this->tramiteServices = $this->getTramiteServices();
        return $this->tramiteServices->getCampos($id,
            function ($tramite) {
                return $this->respuestaData([], $tramite);
            },
            function ($err) {
                return $this->respuestaError($err);
            });
    }

    /**
     * Obtener los requisitos del tamite
     * @ApiDoc(section="Tramite")
     * @param integer $id Identificador único del trámite
     * @return mixed
     * @Get("/tramites/{id}/requisitos")
     */
    public function getRequisitosAction($id)
    {
        $this->tramiteServices = $this->getTramiteServices();
        return $this->tramiteServices->getRequisitos($id,
            function ($tramite) {
                return $this->respuestaData([], $tramite);
            },
            function ($err) {
                return $this->respuestaError($err);
            });
    }

    /**
     * Obtener el horizonte del tamite
     * @ApiDoc(section="Tramite")
     * @param integer $id Identificador único del trámite
     * @return mixed
     * @Get("/tramites/{id}/horizonte")
     */
    public function getHorizonteAction($id)
    {
        $this->tramiteServices = $this->getTramiteServices();
        return $this->tramiteServices->getHorizonte(
            $id,
            function ($tramite) {
                return $this->respuestaData([], $tramite);
            },
            function ($err) {
                return $this->respuestaError($err);
            }
        );
    }

    /**
     * Obtener las provincias en las que se puede hacer el trámite
     * @ApiDoc(section="Tramite",
     * parameters={
     *      {"name"="limit", "dataType"="integer", "required"=false},
     *      {"name"="offset", "dataType"="integer", "required"=false}
     *  })
     * @param Request $request Datos necesarios para ejecutar la petición
     * @param integer $id Identificador único del trámite
     * @return mixed
     * @Get("/tramites/{id}/provincias")
     */
    public function getProvincias(Request $request, $id)
    {
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 10);
        $this->tramiteServices = $this->getTramiteServices();
        return $this->tramiteServices->findProvinciasPaginate((int) $offset, (int) $limit, $id);
    }

    /**
     * Obtener las provincias en las que se puede hacer el trámite
     * @ApiDoc(section="Tramite",
     * parameters={
     *      {"name"="limit", "dataType"="integer", "required"=false},
     *      {"name"="offset", "dataType"="integer", "required"=false}
     *  })
     * @param Request $request Datos necesarios para ejecutar la petición
     * @param integer $id Identificador único del trámite
     * @return mixed
     * @Get("/tramites/{id}/pda/{pdaId}/provincias/")
     */
    public function getProvinciasByPda(Request $request, $id, $pdaId )
    {
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 10);
        $this->tramiteServices = $this->getTramiteServices();
        return $this->tramiteServices->findProvinciasPaginate((int) $offset, (int) $limit, $id, $pdaId);
    }
    /**
     * Obtener el listado de localidades donde se puede realizar el trámite
     * @ApiDoc(section="Tramite",
     * parameters={
     *      {"name"="limit", "dataType"="integer", "required"=false},
     *      {"name"="offset", "dataType"="integer", "required"=false}
     *  })
     * @param Request $request Datos necesarios para ejecutar la petición
     * @param integer $id Identificador único del trámite
     * @param integer $provinciaId Identificador único de provincia
     * @return mixed
     * @Get("/tramites/{id}/provincias/{provinciaId}")
     */
    public function getLocalidades(Request $request, $id, $provinciaId)
    {
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 10);
        $this->tramiteServices = $this->getTramiteServices();
        return $this->tramiteServices->findLocalidadesPaginate((int) $offset, (int) $limit, $id, $provinciaId);
    }

    /**
     * Obtener los campos genericos para todos los formularios
     * @ApiDoc(section="Tramite")
     * @return mixed
     * @Get("/tramites/formulario/campos")
     */
    public function getCamposGenericosAction()
    {
        $this->tramiteServices = $this->getTramiteServices();
        $response = $this->tramiteServices->getCamposGenericos();
        if ($response->getResult()) {
            return $response;
        } else {
            return $this->respuestaNotFound(['errors' => ['Campos no encontrados']]);
        }
    }

    /**
     * Crear Tramite
     * @ApiDoc(section="Tramite",
     * parameters={
     *      {"name"="area", "dataType"="array", "required"=true, "description"="[area_1,area_2]"},
     *      {"name"="campos", "dataType"="array", "required"=true},
     *      {"name"= "duracion", "dataType"="integer", "required"=true},
     *      {"name"="idArgentinaGobAr", "dataType"="integer", "required"=true, "description"="Default null" },
     *      {"name"="nombre", "dataType"="string", "required"=true, "description"="Nombre del tramite" },
     *      {"name"="requisitos", "dataType"="array", "required"=true, "description"="Requsitos del tramite" },
     *      {"name"="visibilidad", "dataType"="integer", "required"=true },
     *      {"name"="excepcional", "dataType"="integer", "required"=true },
     *      {"name"="org", "dataType"="integer", "required"=true, "description"="Tramite por organismo" }
     *
     * })
     * @param Request $request Datos necesarios para ejecutar la petición
     * @return mixed
     * @Post("/tramites")
     */
    public function postAction(Request $request)
    {
        $params = $request->request->all();
        $this->tramiteServices = $this->getTramiteServices();
        return $this->tramiteServices->create(
            $params,
            function ($tramite) {
                return $this->respuestaOk('Tramite creado con éxito', [
                    'id' => $tramite->getId()
                ]);
            },
            function ($err) {
                return $this->respuestaError($err);
            }
        );
    }

    /**
     * Modificar tramite
     * @ApiDoc(section="Tramite",
     * parameters={
     *      {"name"="area", "dataType"="array", "required"=true, "description"="[area_1,area_2]"},
     *      {"name"="campos", "dataType"="array", "required"=true},
     *      {"name"= "duracion", "dataType"="integer", "required"=true},
     *      {"name"="idArgentinaGobAr", "dataType"="integer", "required"=true, "description"="Default null" },
     *      {"name"="nombre", "dataType"="string", "required"=true, "description"="Nombre del tramite" },
     *      {"name"="requisitos", "dataType"="array", "required"=true, "description"="Requsitos del tramite" },
     *      {"name"="visibilidad", "dataType"="integer", "required"=true },
     *      {"name"="excepcional", "dataType"="integer", "required"=true },
     *      {"name"="org", "dataType"="integer", "required"=true, "description"="Tramite por organismo" }
     *
     * })
     * @param Request $request Datos necesarios para ejecutar la petición
     * @param integer $id Identificador único del trámite a modificar
     * @return mixed
     * @Put("/tramites/{id}")
     */
    public function putAction(Request $request, $id)
    {

        // request values
        $params = $request->request->all();
        $this->tramiteServices = $this->getTramiteServices();

        return $this->tramiteServices->edit(
            $params,
            $id,
            function ($tramite) {
                return $this->respuestaOk('Tramite modificado con éxito');
            },
            function ($err) {
                return $this->respuestaError($err);
            }
        );
    }

    /**
     * Eliminar tramite
     * @ApiDoc(section="Tramite")
     * @param integer $id Identificador único del tramite a eliminar
     * @param integer $id Identificador único del tramite a eliminar
     * @Delete("/tramites/{id}/area/{idArea}")
     * @Delete("/tramites/{id}")
     */
    public function deleteAction($id, $idArea = null)
    {
        $this->tramiteServices = $this->getTramiteServices();

        return $this->tramiteServices->delete(
            $id, $idArea,
            function ($tramite) {
                return $this->respuestaOk('Tramite eliminado con éxito');
            },
            function ($err) {
                return $this->respuestaError($err);
            }
        );
    }

    /**
     * Obtener areas por tramite
     * @ApiDoc(section="Tramite")
     * @param integer $id Identificador único del tramite
     * @return mixed
     * @Get("/areas/tramites/{id}")
     */
    public function getAreasTramite($id) {

        $this->tramiteServices = $this->getTramiteServices();
        return $this->tramiteServices->findAreasTramite($id);

    }

  /**
     * Obtiene un tramite
     * @ApiDoc(section="Tramite")
     * @param integer $id Identificador único del trámite
     * @return RespuestaConEstado
     * @Get("/puntosatencion/{puntoAtencionId}/tramites/{tramiteId}")
     */
    public function getPDATramiteItemAction($puntoAtencionId, $tramiteId)
    {
        $this->tramiteServices = $this->getTramiteServices();
        $response = $this->tramiteServices->getPDATramiteItemAction($puntoAtencionId, $tramiteId);
        if ($response->getResult()) {
            return $response;
        } else {
            return $this->respuestaNotFound(['errors' => ['Tramite no encontrado']]);
        }
    }





}
