<?php
/**
 * Created by PhpStorm.
 * User: cristian
 * Date: 31/05/19
 * Time: 11:59
 */

namespace ApiV1Bundle\Controller;

use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\Route;
use Symfony\Component\HttpFoundation\Request;
use  ApiV1Bundle\ApplicationServices\CategoriaTramiteServices;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class CategoriaTramiteController  extends ApiController
{


  /**
   * Crear Categoria
   * @ApiDoc(section="Categoria del tramite",
   * requirements={
   *      {
   *          "name"="categoria",
   *          "dataType"="string",
   *          "requirement"="BODY",
   *          "description"="Nombre de la categoría"
   *      }
   *  })
   * @param Request $request Datos necesarios para ejecutar la petición
   * @return mixed
   * @Post("crear/categoria/tramites")
   */

   public function crearCategoria(Request $request) {

       $params = $request->request->all();
       $CategoriaTramite = $this-> getCategoriaTramiteServices();
       return  $CategoriaTramite->crearCategoria($params);

   }

    /**
     * Listar categorias
     * @ApiDoc(section="Categoria del tramite")
     * @return mixed
     * @Get("categoria/tramites")
     * @param Request $request
     */

   public function listarCategorias(Request $request) {
       $offset = $request->get('offset', 0);
       $limit = $request->get('limit', 10);
       $CategoriaTramite = $this-> getCategoriaTramiteServices();
       return $CategoriaTramite->listarCategorias($limit,$offset);

   }

    /**
     * Eliminar categoria
     * @ApiDoc(section="Categoria del tramite")
     * @param integer $id identificador de categoria
     * @return mixed
     * @Delete("eliminar/categoria/tramites/{id}")
     */

    public function eliminarCategoria($id) {
        $CategoriaTramite = $this-> getCategoriaTramiteServices();
        return $CategoriaTramite->eliminarCategoria($id);

    }


    /**
     * Editar categoria
     * @ApiDoc(section="Categoria del tramite",
     * requirements={
     *    {
     *        "name"="categoria",
     *        "dataType"="string",
     *        "requirement"="BODY",
     *        "description"="Nombre de la categoría"
     *    }
     * })
     * @param integer $id identificador de categoria
     * @param Request $request
     * @return mixed
     * @Post("modificar/categoria/{id}/tramites")
     */

    public function modificarCategoria($id,Request $request) {
        $params = $request->request->all();
        $CategoriaTramite = $this-> getCategoriaTramiteServices();
        return $CategoriaTramite->modificarCategoria($id,$params);
    }


    /**
     * Asignar categoria
     * @ApiDoc(section="Categoria del tramite")
     * @param integer $idCat identificador de categoria
     * @param integer  $idTram identificador de tramite
     * @return mixed
     * @Put("asignar/categoria/{idCat}/tramite/{idTram}")
     */
    public function asignarCategoria($idCat, $idTram) {
        $CategoriaTramite = $this-> getCategoriaTramiteServices();
        return $CategoriaTramite->asignarCategoria($idCat, $idTram);

    }
    
    /**
     * Filtrar categoria
     * @ApiDoc(section="Categoria del tramite",
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
     * @Get("filtrar/tramite/categoria")
     */
    public function filtrarTramitePorCategoria(Request $request) { 
       $params = $request->query->get('categoria');
       $offset = $request->get('offset', 0);
       $limit = $request->get('limit', 10);
       $CategoriaTramite = $this-> getCategoriaTramiteServices();
        return $CategoriaTramite->filtrarPorCategoria($params, $limit, $offset);
        
    }



}
