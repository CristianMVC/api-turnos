<?php
namespace TotemV1Bundle\Controller;

use TotemV1Bundle\Entity\Response\RespuestaConEstado;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\Route;
use Symfony\Component\HttpFoundation\Request;


/**
 * Class TramiteController
 * @package TotemV1Bundle\Controller
 */
class TramiteController extends ApiController
{
    /** @var \TotemV1Bundle\ApplicationServices\TramiteServices */
    private $tramiteServices;

    /**
     * Obtiene los campos para el formulario de acuerdo al trámite
     *
     * @param Request $request Espera el resultado de una petición como parámetro
     * @param integer $tramiteId Identificador único del tramite
     * @return mixed
     * @Get("/puntosatencion/{puntoAtencionId}/tramites/{tramiteId}/formulario")
     */
    public function getTramiteCampos(Request $request, $tramiteId)
    {
        $this->tramiteServices = $this->getTramiteServices();
        return $this->tramiteServices->getCampos(
            $tramiteId,
            function ($res) {
                return $this->respuestaData([], $res);
            },
            function ($err) {
                return $this->respuestaError($err);
            });
    }

    /**
     * Obtiene el listado de tramites por nombre y punto de atención
     *
     * @param Request $request Espera el resultado de una petición como parámetro
     * * @param integer $puntoAtencionId Identificador único del punto de atención
     * @return mixed
     * @Get("/puntosatencion/{puntoAtencionId}/tramites/buscar")
     */
    public function getTramitesAction(Request $request, $puntoAtencionId)
    {
        $query = $request->query->get('q');
        $offset = $request->query->get('offset', 0);
        $limit = $request->query->get('limit', 10);
        $this->tramiteServices = $this->getTramiteServices();
        return $this->tramiteServices->findTramitesByPuntoNombrePaginate($puntoAtencionId, $query, (int) $limit, (int) $offset);
    }

    /**
     * Obtiene el listado de tramites por categoria
     *
     * @param Request $request Espera el resultado de una petición como parámetro
     * @param integer $categoriaId Id de la categoría
     * @return mixed
     * @Get("/puntosatencion/{puntoAtencionId}/categorias/{categoriaId}/tramites")
     */
    public function getTramitesCategoriaAction(Request $request, $puntoAtencionId, $categoriaId)
    {
        $limit = $request->query->get('limit',10);
        $offset = $request->query->get('offset',00);
        $this->tramiteServices = $this->getTramiteServices();
        return $this->tramiteServices->findTramitesByCategoriaPaginate($puntoAtencionId, $categoriaId, (int) $limit, (int) $offset);
    }

    /**
     * Obtiene la cantidad de turnos en espera para el tramite
     *
     * @param integer $id Identificador único del trámite
     * @return RespuestaConEstado
     * @Get("/tramites/delante")
     */
    public function getDelanteAction(Request $request)
    {
        $this->tramiteServices = $this->getTramiteServices();
        $params = $request->query->all();
        return $this->tramiteServices->getCantidadDelante(
            $params,
            function ($err) {
                return $this->respuestaError($err);
            }
        );
    }

    /**
     * Obtiene un tramite
     *
     * @param integer $id Identificador único del trámite
     * @return RespuestaConEstado
     * @Get("/puntosatencion/{puntoAtencionId}/tramites/{tramiteId}")
     */
    public function getItemAction($puntoAtencionId, $tramiteId)
    {
        $this->tramiteServices = $this->getTramiteServices();
        $response = $this->tramiteServices->get($puntoAtencionId, $tramiteId);
        if ($response->getResult()) {
            return $response;
        } else {
            return $this->respuestaNotFound(['errors' => ['Tramite no encontrado']]);
        }
    }
}