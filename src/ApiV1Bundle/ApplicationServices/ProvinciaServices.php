<?php

namespace ApiV1Bundle\ApplicationServices;

use ApiV1Bundle\Repository\ProvinciaRepository;
use Symfony\Component\DependencyInjection\Container;

/**
 * Class ProvinciaServices
 * @package ApiV1Bundle\ApplicationServices
 */

class ProvinciaServices extends SNTServices
{
    /** @var  ProvinciaRepository */
    private $provinciasRepository;

    /**
     * ProvinciaServices constructor.
     *
     * @param Container $container
     * @param $provinciasRepository
     */
    public function __construct(Container $container, $provinciasRepository)
    {
        parent::__construct($container);
        $this->provinciasRepository = $provinciasRepository;
    }

    /**
     * Obtiene todas las Provincias
     *
     * @param integer $limit Cantidad máxima de registros a retornar
     * @param integer $offset Cantidad de registros a saltar
     * @return mixed
     */
    public function findAllPaginate($offset, $limit)
    {
        $result = $this->provinciasRepository->findAllPaginate($offset, $limit);
        $resultset = [
            'resultset' => [
                'count' => $this->provinciasRepository->getTotal(),
                'offset' => $offset,
                'limit' => $limit
            ]
        ];
        return $this->respuestaData($resultset, $result);
    }
}
