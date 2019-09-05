<?php

namespace ApiV1Bundle\Entity\Factory;

use ApiV1Bundle\Entity\Categoria;
use ApiV1Bundle\Entity\ValidateResultado;
use ApiV1Bundle\Entity\Validator\CategoriaValidator;
use ApiV1Bundle\Repository\PuntoAtencionRepository;
use ApiV1Bundle\Repository\TramiteRepository;
use ApiV1Bundle\Repository\TurnoRepository;

/**
 * Class CategoriasFactory
 *
 * @package ApiV1Bundle\Entity\Factory
 */
class CategoriasFactory
{
    /** @var PuntoAtencionRepository  */
    private $puntoAtencionRepository;
    /** @var CategoriaValidator  */
    private $categoriaValidator;
    /** @var TramiteRepository  */
    private $tramiteRepository;

    /**
     * CategoriasFactory constructor.
     * @param PuntoAtencionRepository $puntoAtencionRepository
     * @param TramiteRepository $tramiteRepository
     * @param CategoriaValidator $categoriaValidator
     */
    public function __construct(
        PuntoAtencionRepository $puntoAtencionRepository,
        TramiteRepository $tramiteRepository,
        CategoriaValidator $categoriaValidator
    )
    {
        $this->puntoAtencionRepository = $puntoAtencionRepository;
        $this->tramiteRepository = $tramiteRepository;
        $this->categoriaValidator = $categoriaValidator;
    }

    /**
     * Crea un objeto categoría
     *
     * @param array $params arreglo con los datos de la categoría
     * @param integer $puntoAtencionId identificador único del punto de atención
     * @return mixed
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function create($params, $puntoAtencionId)
    {
        $puntoAtencion = $this->puntoAtencionRepository->find($puntoAtencionId);
        $validateResult = $this->categoriaValidator->validarCreate(
            $params,
            $puntoAtencion);

        if (!$validateResult->hasError()) {
            $categoria = new Categoria($params['nombre'], $puntoAtencion);
            foreach ($params["tramites"] as $tramiteId) {
                $tramite = $this->tramiteRepository->find($tramiteId);
                $tramiteValidate = $this->categoriaValidator->validarTramite(
                    $tramite,
                    $puntoAtencionId,
                    $categoria->getId());
                if ($tramiteValidate->hasError()) {
                    return $tramiteValidate;
                }

                $categoria->addTramite($tramite);
            }

            return new ValidateResultado($categoria, []);
        }

        return $validateResult;
    }
}