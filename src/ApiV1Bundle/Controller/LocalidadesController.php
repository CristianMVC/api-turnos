<?php
namespace ApiV1Bundle\Controller;

use ApiV1Bundle\ApplicationServices\LocalidadServices;
use FOS\RestBundle\Controller\Annotations\Route;
use FOS\RestBundle\Controller\Annotations\Get;
use Symfony\Component\HttpFoundation\Request;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * Class LocalidadesController
 * @package ApiV1Bundle\Controller
 *
 * Localidades
 * @author Fausto Carrera <fcarrera@hexacta.com>
 */


class LocalidadesController extends ApiController
{
    /** @var Localidades */
    private $localidadServices;

    /**
     * Búsqueda de localidades en una provincia
     * @param Request $request Datos necesarios para ejecutar la petición
     * @param integer $id Identificador único de la provincia
     * @return mixed
     * @Get("/provincias/{id}/localidades/buscar")
     */
    public function getBusqueda(Request $request, $id) {
        $qry = $request->get('qry', '');
        $this->localidadServices = $this->getLocalidadesServices();
        return $this->localidadServices->busqueda($id, $qry);
    }

    /**
     * Listado de localidades
     * @ApiDoc(section="Localidades",
     * parameters={
     *      {"name"="limit", "dataType"="integer", "required"=false},
     *      {"name"="offset", "dataType"="integer", "required"=false}
     *  })
     * @param Request $request Datos necesarios para ejecutar la petición
     * @param integer $id Identificador único de la provincia
     * @return mixed
     * @Get("/provincias/{id}/localidades")
     */
    public function getListAction(Request $request, $id)
    {
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 10);
        $this->localidadServices = $this->getLocalidadesServices();
        return $this->localidadServices->findAllPaginate($id, (int) $offset, (int) $limit);
    }

}
