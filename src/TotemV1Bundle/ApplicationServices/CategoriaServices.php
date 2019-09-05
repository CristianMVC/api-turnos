<?php

namespace TotemV1Bundle\ApplicationServices;

use Symfony\Component\DependencyInjection\Container;
use ApiV1Bundle\Repository\CategoriaRepository;

/**
 * Class CategoriaServices
 * @package TotemV1Bundle\ApplicationServices
 */
class CategoriaServices extends TotemServices
{
    /** @var CategoriaRepository  */
    private $categoriaRepository;

    /**
     * CategoriaServices constructor.
     * @param Container $container
     * @param CategoriaRepository $categoriaRepository
     */
    public function __construct(
        Container $container,
        CategoriaRepository $categoriaRepository
    ) {
        parent::__construct($container);
        $this->categoriaRepository = $categoriaRepository;
    }

    /**
     * Listar categorías
     *
     * @param integer $limit Cantidad máxima de registros a retornar
     * @param integer $offset Cantidad de registros a saltar
     * * @param integer $puntoAtencionId identificador único de punto de Atención
     * @param callback $error Callback para devolver respuesta fallida
     * @return mixed
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getAllPaginated($puntoAtencionId, $offset, $limit, $error)
    {
        $result = $this->categoriaRepository->findAllPaginatedTotem(
            $puntoAtencionId,
            $offset,
            $limit
        );
        $resultset = [
            'resultset' => [
                'count' => $this->categoriaRepository->getTotalTotem($puntoAtencionId),
                'offset' => $offset,
                'limit' => $limit
            ]
        ];

        return $this->respuestaData($resultset, $result);
    }
}
