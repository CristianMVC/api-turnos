<?php
/**
 * Created by PhpStorm.
 * User: cristian
 * Date: 26/08/19
 * Time: 10:04
 */

namespace ApiV1Bundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use ApiV1Bundle\Entity\Response\RespuestaConEstado;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\Delete;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use ApiV1Bundle\ApplicationServices\DisponibilidadServices;


class MiArgentinaController  extends ApiController
{

    /** @var ApiV1Bundle\ApplicationServices\DisponibilidadServices $disponibilidadServices */
    protected $disponibilidadServices;

    /**
     * Obtiene tramites de mi argentina
     * @ApiDoc(section="Mi Argentina")
     * @return mixed
     * @Get("/miargentina/tramites")
     */

    public function getTramitesMiArgentina(Request $request) {

        $limit = $request->query->get('limit',10);
        $offset = $request->query->get('offset',0);

        $argentinaServices = $this->getMiArgentinaServices();

        return $argentinaServices->tramitesMiArgentina($limit,  $offset);

    }


    /**
     * Obtiene los trámites que pertenecen a un área
     * @ApiDoc(section="Mi Argentina")
     * @param Request $request Datos necesarios para ejecutar la petición
     * @param integer $organismoId Identificador único del organismo
     * @param integer $id Identificador único del área
     * @return mixed
     * @Get("/miargentina/organismos/{organismoId}/areas/{id}/tramites")
     */
    public function getTramitesAction(Request $request, $organismoId, $id=null) {
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 10);
        $argentinaServices = $this->getMiArgentinaServices();

        return $argentinaServices->findTramitesPaginate($organismoId, $id, (int) $limit, (int) $offset);
    }

      /**
     * Obtiene los trámites que pertenecen a un organismo
     * @ApiDoc(section="Mi Argentina")
     * @param Request $request Datos necesarios para ejecutar la petición
     * @param integer $organismoId Identificador único del organismo
     * @return mixed
     * @Get("/miargentina/organismos/{organismoId}/areas/tramites")
     */
    public function getTramitesOrgAction(Request $request, $organismoId) {
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 10);
        $argentinaServices = $this->getMiArgentinaServices();

        return $argentinaServices->findTramitesPaginate($organismoId, null, (int) $limit, (int) $offset);
    }


    /**
     * Obtiene tramite
     * @ApiDoc(section="Mi Argentina")
     * @param integer $id Identificador único del trámite
     * @return mixed
     * @Get("/miargentina/tramites/{id}")
     */
    public function getItemAction($id) {
        $argentinaServices = $this->getMiArgentinaServices();

        return $argentinaServices->get($id);
    }


    /**
     * Filtrar categoria
     * @ApiDoc(section="Mi Argentina",
     * requirements={
     *      {
     *          "name"="categoria",
     *          "dataType"="array",
     *          "requirement"="HEADER",
     *          "description"="Ej: categoria[0] = categoria_1"
     *      }
     *  })
     * @param Request $request
     * @return mixed
     * @Get("/miargentina/filtrar/tramite/categoria")
     */
    public function filtrarTramitePorCategoria(Request $request) {
        $categoria = $request->query->get('categoria');
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 10);
        $argentinaServices = $this->getMiArgentinaServices();
        return  $argentinaServices->findFiltrarPorCategoriaMiArgentina($categoria, $limit, $offset);

    }

    /**
     * Obtener listado de fechas disponibles
     *  Ejemplo: GET /miargentina/disponibilidad/tramites?_format=json&tramiteId=180&fecha=2019-08-26&horizonte=10&where=punto_atencion_id&punto_atencion_id=10
     * @ApiDoc(section="Mi Argentina",
     *  tags={"miArgentina"},
     *  parameters={
     *      {"name"="tramiteId", "dataType"="integer", "required"=true, "description"="Id de Tramite"},
     *      {"name"="fecha", "dataType"="date (Y-m-d)", "required"=true, "description"="Fecha de Inicio " },
     *      {"name"="horizonte", "dataType"="integer", "required"=true, "description"="Horizonte en días"},
     *      {"name"="groupby", "dataType"="string", "required"=false, "description"="{fecha, punto_atencion_id, turno, provincia_id, localidad_id}"},
     *      {"name"="punto_atencion_id", "dataType"="string", "required"=false, "description"="depende del parametro where"},
     *      {"name"="multiturno", "dataType"="integer", "required"=false, "description"="0,1"},
     *      {"name"="multiturno_cantidad", "dataType"="integer", "required"=false, "description"="con multiturno_cantidad mayor o igual al parametro "},
     *      {"name"="provincia_id", "dataType"="integer", "required"=false, "description"="id provincia"},
     *      {"name"="localidad_id", "dataType"="integer", "required"=false, "description"="id localidad"},
     *      {"name"="offset", "dataType"="integer", "required"=true, "description"="inicio del paginado "},
     *      {"name"="limit", "dataType"="integer", "required"=true, "description"="numero de objetos a recuperar"}
     * })
     * @param Request $request Datos necesarios para ejecutar la petición
     * @return mixed
     * @Get("/miargentina/disponibilidad/tramites")
     */
    public function getTramitesFechasDisponibles(Request $request)
    {
        $params = $request->query->all();
        $params['offset'] = $request->get('offset', 0);
        $params['limit'] = $request->get('limit', 10);
        $this->disponibilidadServices = $this->getDisponibilidadServices();

        return  $this->disponibilidadServices->getTramitesFechasDisponibles(
            $params,
            function ($err) {
                return $this->respuestaError($err);
            }
        );
    }

    /**
     * Filtrar tramite por etiqueta (busqueda texto)
     * @ApiDoc(section="Mi Argentina",
     * tags={"miArgentina"},
     * parameters={
     *      {"name"="etiqueta", "dataType"="array", "required"=true, "description"="Ej: etiqueta[1] = et_1, etiqueta[2] = et_2"},
     *      {"name"="limit", "dataType"="integer", "required"=false}
     *  })
     * @Get("/miargentina/filtrar/tramite/etiqueta/buscar")
     * @return mixed
     * @param Request $request Datos necesarios para ejecutar la petición
     */

    public function filtrarTramitePorEtiquetaBuscar(Request $request) {
        $params = $request->query->get('etiqueta');
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 10);
        $argentinaServices = $this->getMiArgentinaServices();
        return $argentinaServices->filtrarPorEtiquetaBuscar($params, $offset, $limit);

    }


    /**
     * Filtrar tramite por etiqueta
     * @ApiDoc(section="Mi Argentina",
     * parameters={
     *      {"name"="etiqueta", "dataType"="array", "required"=true, "description"="Ej: etiqueta[1] = et_1, etiqueta[2] = et_2"},
     *      {"name"="limit", "dataType"="integer", "required"=false}
     *  })
     * @Get("/miargentina/filtrar/tramite/etiqueta")
     * @return mixed
     * @param Request $request Datos necesarios para ejecutar la petición
     */

    public function filtrarTramitePorEtiqueta(Request $request) {
        $params = $request->query->get('etiqueta');
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 10);
        $argentinaServices = $this->getMiArgentinaServices();
        return $argentinaServices->filtrarPorEtiquetaMiArgentina($params, $offset, $limit);

    }


    /**
     * Listar categorias
     * @ApiDoc(section="Mi Argentina")
     * @return mixed
     * @Get("/miargentina/categoria/tramites")
     * @param Request $request
     */

    public function listarCategorias(Request $request) {
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 10);
        $argentinaServices = $this->getMiArgentinaServices();
        return  $argentinaServices->listarCategorias($limit,$offset);

    }



    /**
     * Listado turnos por cuil
     * @ApiDoc(section="Turno",
     * tags={"miArgentina"},
     * parameters={
     *      {"name"="cuil", "dataType"="integer", "required"=true, "description"="cuil"},
     *      {"name"="fecha desde(Ej: 21-08-2019)", "dataType"="string", "required"=true},
     *      {"name"="fecha hasta(Ej: 25-08-2019)", "dataType"="string", "required"=true}
     *  })
     * @param Request $request Datos necesarios para ejecutar la petición
     * @return mixed
     * @Get("/miargentina/solicitante/turnos")
     */
    public function getListSolicitanteAction(Request $request)
    {
        $argentinaServices = $this->getMiArgentinaServices();
        $cuil = $request->get('cuil', null);

        $desde= $request->get('desde', 0);
        $hasta = $request->get('hasta', 10);

        return  $argentinaServices->findAllSolicitante( $desde, $hasta, $cuil);
    }

     /**
     * Listado turnos por cuil
     * @ApiDoc(section="Mi Argentina",
     * tags={"miArgentina"},
     * parameters={
     *      {"name"="cuil", "dataType"="integer", "required"=true, "description"="cuil"},
     *      {"name"="fecha", "dataType"="string", "required"=true, "description"="Y-m-d"},
     *      {"name"="idTramite", "dataType"="integer", "required"=true, "description"="idTramite"},
     *      {"name"="idPuntoAtención", "dataType"="integer", "required"=true, "description"="idPuntoAtención"},
     *  })
     * @param Request $request Datos necesarios para ejecutar la petición
     * @return mixed
     * @Get("/miargentina/turnos/disponibleporcuil")
     */
    public function puedeSacarTurnoAction(Request $request)
    {
        $argentinaServices = $this->getMiArgentinaServices();
        $cuil = $request->get('cuil', null);
        $params = $request->query->all();
        return  $argentinaServices->puedeSacarTurnoAction( $params, $cuil);
    }

}