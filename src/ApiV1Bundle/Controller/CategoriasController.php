<?php

namespace ApiV1Bundle\Controller;

use ApiV1Bundle\Entity\Categoria;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Put;
use Symfony\Component\HttpFoundation\Request;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class CategoriasController extends ApiController
{
    /**
     * Obtener todos los trámites sin categoría de un punto de atención
     * @ApiDoc(section="Categoria",
     * parameters={
     *      {"name"="offset", "dataType"="integer", "required"=false},
     *      {"name"="limit", "dataType"="integer", "required"=false},
     *  })
     * @param Request $request Datos necesarios para ejecutar la petición
     * @param integer $puntoAtencionId Identificador único del punto de atención
     * @return mixed
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @Get("/puntosatencion/{puntoAtencionId}/categorias/tramitesdisponibles")
     */
    public function getTramitesSinCategoriaAction(Request $request, $puntoAtencionId)
    {
        $categoriaServices = $this->getCategoriaServices();
        return $categoriaServices->getTramitesSinCategoriaPaginated(
            $puntoAtencionId,
            function ($err) {
                return $this->respuestaError($err);
            }
        );
    }

    /**
     * Lista las categorías de un punto de atención
     * @ApiDoc(section="Categoria",
     * parameters={
     *      {"name"="offset", "dataType"="integer", "required"=false},
     *      {"name"="limit", "dataType"="integer", "required"=false},
     *  })
     * @param Request $request Datos necesarios para ejecutar la petición
     * @param integer $puntoAtencionId Identificador único del punto de atención
     * @return mixed
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @Get("/puntosatencion/{puntoAtencionId}/categorias")
     */
    public function getListAction(Request $request, $puntoAtencionId)
    {
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 10);
        $categoriaServices = $this->getCategoriaServices();
        return $categoriaServices->getAllPaginated(
            (int)$offset,
            (int)$limit,
            $puntoAtencionId,
            function ($err) {
                return $this->respuestaError($err);
            });
    }

    /**
     * Obtiene la información de una categoría
     * @ApiDoc(section="Categoria")
     * @param Request $request Datos necesarios para ejecutar la petición
     * @param integer $puntoAtencionId Identificador único del punto de atención
     * @param integer $categoriaId Identificador único de la categoría
     * @return mixed
     * @Get("/puntosatencion/{puntoAtencionId}/categorias/{categoriaId}")
     */
    public function getAction(Request $request, $puntoAtencionId, $categoriaId)
    {
        $categoriaServices = $this->getCategoriaServices();
        return $categoriaServices->get(
            $puntoAtencionId,
            $categoriaId,
            function ($categoria) {
                return $this->respuestaData([], $categoria);
            },
            function () {
                return $this->respuestaNotFound("Categoría no encontrada");
            }
        );
    }

    /**
     * Crea una categoría
     * @ApiDoc(section="Categoria",
     * parameters={
     *      {"name"="nombre", "dataType"="string", "required"=true, "description"="Nombre de la categoria"},
     *      {"name"="tramites", "dataType"="array", "required"=true, "description"="[1,2]"},
     *  })
     * @param Request $request Datos necesarios para ejecutar la petición
     * @param integer $puntoAtencionId Identificador único del punto de atención
     * @return mixed
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @Post("/puntosatencion/{puntoAtencionId}/categorias")
     */
    public function postAction(Request $request, $puntoAtencionId)
    {
        $params = $request->request->all();
        $categoriaServices = $this->getCategoriaServices();
        return $categoriaServices->create(
            $params,
            $puntoAtencionId,
            function (Categoria $categoria) {
                return $this->respuestaOk('Categoría creada con éxito', [
                    'id' => $categoria->getId()
                ]);
            },
            function ($err) {
                return $this->respuestaError($err);
            });
    }

    /**
     * Edita una categoría
     * @ApiDoc(section="Categoria",
     * parameters={
     *      {"name"="nombre", "dataType"="string", "required"=true, "description"="Nombre de la categoria"},
     *      {"name"="tramites", "dataType"="array", "required"=true, "description"="[1,2]"},
     *  })
     * @param Request $request Datos necesarios para ejecutar la petición
     * @param integer $puntoAtencionId Identificador único del punto de atención
     * @param integer $categoriaId Identificador único de la categoría
     * @return mixed
     * @Put("/puntosatencion/{puntoAtencionId}/categorias/{categoriaId}")
     */
    public function putAction(Request $request, $puntoAtencionId, $categoriaId)
    {
        $params = $request->request->all();
        $categoriaServices = $this->getCategoriaServices();
        return $categoriaServices->edit(
            $params,
            $puntoAtencionId,
            $categoriaId,
            function () {
                return $this->respuestaOk("Categoría editada con éxito.");
            },
            function ($err) {
                return $this->respuestaError($err);
            });
    }

    /**
     * Elmina una categoría
     * @ApiDoc(section="Categoria")
     * @param Request $request Datos necesarios para ejecutar la petición
     * @param integer $puntoAtencionId Identificador único del punto de atención
     * @param integer $categoriaId Identificador único de la categoría
     * @return mixed
     * @Delete("/puntosatencion/{puntoAtencionId}/categorias/{categoriaId}")
     */
    public function deleteAction(Request $request, $puntoAtencionId, $categoriaId)
    {
        $categoriaServices = $this->getCategoriaServices();
        return $categoriaServices->delete(
            $puntoAtencionId,
            $categoriaId,
            function () {
                return $this->respuestaOk("Categoría eliminada con éxito.");
            },
            function ($err) {
                return $this->respuestaError($err);
            });
    }
}
