<?php

namespace TotemV1Bundle\Controller;

use FOS\RestBundle\Controller\Annotations\Get;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CategoriasController
 * @package TotemV1Bundle\Controller
 */
class CategoriasController extends ApiController
{
    /**
     * Lista las categorías de un punto de atención
     *
     * @param Request $request Espera el resultado de una petición como parámetro
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
            $puntoAtencionId,
            (int) $offset,
            (int) $limit,
            function ($err) {
                return $this->respuestaError($err);
            }
        );
    }
}
