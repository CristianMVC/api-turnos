<?php
namespace ApiV1Bundle\Controller;

use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Route;
use Symfony\Component\HttpFoundation\Request;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * Class ProvinciasController
 * @package ApiV1Bundle\Controller
 * Provincias
 * @author Fausto Carrera <fcarrera@hexacta.com>
 */


class ProvinciasController extends ApiController
{

    /**
     * Listado de provincias
     * @ApiDoc(section="Provincias")
     * @param Request $request Datos necesarios para ejecutar la petición
     * @return mixed
     * @Get("/provincias")
     */
    public function getListAction(Request $request)
    {
        // request values
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 10);
        $provinciaServices = $this->getProvinciaServices();
        return $provinciaServices->findAllPaginate((int) $offset, (int) $limit);
    }
}
