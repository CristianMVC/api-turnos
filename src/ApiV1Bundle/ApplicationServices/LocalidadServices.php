<?php


namespace ApiV1Bundle\ApplicationServices;

use ApiV1Bundle\Entity\Response\RespuestaConEstado;
use ApiV1Bundle\Entity\Validator\LocalidadValidator;
use ApiV1Bundle\Repository\LocalidadRepository;
use Symfony\Component\DependencyInjection\Container;

/**
 * Class LocalidadServices
 * @package ApiV1Bundle\ApplicationServices
 */

class LocalidadServices extends SNTServices
{
    /** @var LocalidadRepository  */
    private $localidadesRepository;
    /** @var LocalidadValidator  */
    private $localidadValidator;

    /**
     * LocalidadServices constructor.
     *
     * @param Container $container
     * @param $localidadesRepository
     * @param $localidadValidator
     */
    public function __construct(Container $container,
                                LocalidadRepository $localidadesRepository,
                                LocalidadValidator $localidadValidator)
    {
        parent::__construct($container);
        $this->localidadesRepository = $localidadesRepository;
        $this->localidadValidator = $localidadValidator;
    }

    /**
     * Obtiene todas las localidad de una Provincia por ID
     *
     * @param integer $id Identificador único de la provincia
     * @param integer $limit Cantidad máxima de registros a retornar
     * @param integer $offset Cantidad de registros a saltar
     * @return mixed
     */
    public function findAllPaginate($id, $offset, $limit)
    {
        $result = $this->localidadesRepository->findAllPaginated($id, $offset, $limit);
        $resultset = [
            'resultset' => [
                'count' => $this->localidadesRepository->getTotal($id),
                'offset' => $offset,
                'limit' => $limit
            ]
        ];
        return $this->respuestaData($resultset, $result);
    }

    /**
     * Obtiene todas las localidades de una provincia que empiecen con la cadena
     * de caracteres enviada por parametros
     *
     * @param integer $id Identificador único de la provincia
     * @param string $qry La cadena a comparar
     * @return mixed
     */
    public function busqueda($id, $qry) {
        $validateResultado = $this->localidadValidator->validarBusqueda($id, $qry);
        if (!$validateResultado->hasError()) {
            $data = $this->localidadesRepository->busqueda($id, $qry);
            return $this->respuestaData([], $data);
        }

        return $this->respuestaData([], []);
    }
}
