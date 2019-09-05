<?php
/**
 * Created by PhpStorm.
 * User: cristian
 * Date: 10/06/19
 * Time: 15:36
 */

namespace ApiV1Bundle\Controller;

use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\Route;
use Symfony\Component\HttpFoundation\Request;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class EtiquetaController  extends ApiController
{


    /**
     * Crear Etiqueta
     * @ApiDoc(section="Etiqueta",
     * parameters={
     *      {"name"="nombre", "dataType"="string", "required"=true, "description"="Nombre de la etiqueta"}
     *  })
     * @param Request $request Datos necesarios para ejecutar la petición
     * @return mixed
     * @Post("crear/etiqueta")
     */

    public function crearEtiquetaAction(Request $request) {

        $params = $request->request->all();
        $etiquetaServices = $this-> getEtiquetaServices();
        return     $etiquetaServices-> crearEtiqueta($params);

    }


    /**
     * Listar etiquetas
     * @ApiDoc(section="Etiqueta",
     * parameters={
     *      {"name"="offset", "dataType"="integer", "required"=false},
     *      {"name"="limit", "dataType"="integer", "required"=false}
     *  })
     * @return mixed
     * @Get("listar/etiquetas")
     * @param Request $request
     */

    public function listarEtiquetas(Request $request) {
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 10);
        $etiquetaServices = $this-> getEtiquetaServices();
        return $etiquetaServices->listarEtiquetas($limit,$offset);

    }


    /**
     * Eliminar etiqueta
     * @ApiDoc(section="Etiqueta")
     * @param integer $id identificador de categoria
     * @return mixed
     * @Delete("eliminar/etiqueta/{id}")
     */

    public function eliminarEtiqueta($id) {
        $etiquetaServices = $this-> getEtiquetaServices();
        return $etiquetaServices->eliminarEtiqueta($id);

    }



    /**
     * Editar etiqueta
     * @ApiDoc(section="Etiqueta",
     * parameters={
     *      {"name"="nombre", "dataType"="string", "required"=true, "description"="Nombre de la etiqueta"}
     *  })
     * @param Request $request Datos necesarios para ejecutar la petición
     * @param integer $id identificador de etiqueta
     * @return mixed
     * @Put("modificar/etiqueta/{id}")
     */

    public function modificarEtiqueta(Request $request, $id) {
        $params = $request->request->all();
        $etiquetaServices = $this-> getEtiquetaServices();
        return $etiquetaServices->modificarEtiqueta($id,$params);
    }



    /**
     * Asignar etiquetas
     * @ApiDoc(section="Etiqueta",
     * parameters={
     *      {"name"="etiquetas", "dataType"="array", "required"=true, "description"="['et_1','et_2']"}
     *  })
     * @param Request $request Datos necesarios para ejecutar la petición
     * @param integer $id identificador de tramite
     * @return mixed
     * @Put("asignar/etiquetas/tramite/{id}")
     */
    public function asignarEtiqueta(Request $request, $id) {
        $params = $request->request->all();
        $etiquetaServices = $this-> getEtiquetaServices();
        return $etiquetaServices->asignarEtiqueta($params,$id);

    }


    /**
     * Listar etiquetas por tramite
     * @ApiDoc(section="Etiqueta",
     * parameters={
     *      {"name"="offset", "dataType"="integer", "required"=false},
     *      {"name"="limit", "dataType"="integer", "required"=false}
     *  })
     * @return mixed
     * @Get("listar/etiquetas/tramite/{id}")
     * @param Request $request Datos necesarios para ejecutar la petición
     * @param integer $id identificador de tramite
     */
    public function etiquetasPorTramite(Request $request, $id) {
        $etiquetaServices = $this-> getEtiquetaServices();
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 10);
        return $etiquetaServices-> listarEtiquetasPorTramite($id, $offset, $limit);

    }

    /**
     * Filtrar tramite por etiqueta
     * @ApiDoc(section="Etiqueta",
     * tags={"miArgentina"},
     * parameters={
     *      {"name"="etiqueta", "dataType"="array", "required"=true, "description"="Ej: etiqueta[1] = et_1, etiqueta[2] = et_2"},
     *      {"name"="limit", "dataType"="integer", "required"=false}
     *  })
     * @Get("filtrar/tramite/etiqueta")
     * @return mixed
     * @param Request $request Datos necesarios para ejecutar la petición
     */

    public function filtrarTramitePorEtiqueta(Request $request) {
        $params = $request->query->get('etiqueta');
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 10);
        $etiquetaServices = $this-> getEtiquetaServices();
        return $etiquetaServices->filtrarPorEtiqueta($params, $offset, $limit);

    }
        

}
