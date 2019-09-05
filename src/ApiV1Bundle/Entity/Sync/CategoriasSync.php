<?php

namespace ApiV1Bundle\Entity\Sync;

use ApiV1Bundle\Entity\PuntoAtencion;
use ApiV1Bundle\Entity\PuntoTramite;
use ApiV1Bundle\Entity\ValidateResultado;
use ApiV1Bundle\Entity\Validator\CategoriaValidator;
use ApiV1Bundle\Repository\CategoriaRepository;
use ApiV1Bundle\Repository\PuntoAtencionRepository;
use ApiV1Bundle\Repository\TramiteRepository;

/**
 * Class CategoriasSync
 * @package ApiV1Bundle\Entity\Sync
 */
class CategoriasSync
{
    /** @var PuntoAtencionRepository  */
    private $puntoAtencionRepository;
    /** @var TramiteRepository  */
    private $tramiteRepository;
    /** @var CategoriaValidator  */
    private $categoriaValidator;
    /** @var CategoriaRepository  */
    private $categoriaRepository;

    /**
     * CategoriasSync constructor.
     * @param PuntoAtencionRepository $puntoAtencionRepository
     * @param TramiteRepository $tramiteRepository
     * @param CategoriaValidator $categoriaValidator
     * @param CategoriaRepository $categoriaRepository
     */
    public function __construct(
        PuntoAtencionRepository $puntoAtencionRepository,
        TramiteRepository $tramiteRepository,
        CategoriaValidator $categoriaValidator,
        CategoriaRepository $categoriaRepository
    ) {
        $this->puntoAtencionRepository = $puntoAtencionRepository;
        $this->tramiteRepository = $tramiteRepository;
        $this->categoriaValidator = $categoriaValidator;
        $this->categoriaRepository = $categoriaRepository;
    }

    /**
     * Editar una categoría
     *
     * @param integer $puntoAtencionId Identificador único del punto de atención
     * @param integer $categoriaId Identificador único de la categoría
     * @param array $params array con datos de la categoría
     * @return mixed
     */
    public function edit($params, $puntoAtencionId, $categoriaId)
    {
        $puntoAtencion = $this->puntoAtencionRepository->find($puntoAtencionId);
        $categoria = $this->categoriaRepository->find($categoriaId);

        $validateResultado = $this->categoriaValidator->validarEdit($params, $puntoAtencion, $categoria);
        if (! $validateResultado->hasError()) {
            $validarPDA = $this->categoriaValidator->validarCategoriaPuntoAtencion(
                $puntoAtencion,
                $categoria
            );
            if (! $validarPDA->hasError()) {
                // Editamos
                $categoria->setNombre($params['nombre']);
                $tramitesCategoria = [];

                foreach ($categoria->getTramites() as $tramite) {
                    if (! in_array($tramite->getId(), $params['tramites'])) {
                        $categoria->removeTramite($tramite);
                    } else {
                        $tramitesCategoria[] = $tramite->getId();
                    }
                }

                foreach ($params['tramites'] as $tramiteId) {
                    if (! in_array($tramiteId, $tramitesCategoria)) {
                        $tramite = $this->tramiteRepository->find($tramiteId);
                        if ($tramite) {
                            $categoria->addTramite($tramite);
                        }
                    }
                }

                return new ValidateResultado($categoria, []);
            }
            return $validarPDA;
        }

        return $validateResultado;
    }

    /**
     * Eliminar una categoría
     *
     * @param integer $puntoAtencionId Identificador único del punto de atención
     * @param integer $categoriaId Identificador único de la categoría
     * @return mixed
     */
    public function delete($puntoAtencionId, $categoriaId)
    {
        $puntoAtencion = $this->puntoAtencionRepository->find($puntoAtencionId);
        $categoria = $this->categoriaRepository->find($categoriaId);
        $validateResultado = $this->categoriaValidator->validarDelete($puntoAtencion, $categoria);
        if (!$validateResultado->hasError()) {
            $validarPDA = $this->categoriaValidator->validarCategoriaPuntoAtencion(
                $puntoAtencion,
                $categoria
            );
            if (! $validarPDA->hasError()) {
                return new ValidateResultado($categoria, []);
            }

            return $validarPDA;
        }

        return $validateResultado;
    }

    /**
     * @param object $puntoAtencion PuntoAtencion
     * @return array
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getTramitesDisponibles(PuntoAtencion $puntoAtencion)
    {
        $response = [];
        $tramites = $puntoAtencion->getTramites();
        foreach ($tramites as $puntoTramite) {
            /** @var PuntoTramite $puntoTramite */
            $tramite = $puntoTramite->getTramite();
            $check = $this->categoriaRepository->checkRelationship($puntoAtencion->getId(), $tramite->getId());
            if (!$check) {
                $response[] = [
                    'id' => $tramite->getId(),
                    'nombre' => $tramite->getNombre()
                ];
            }
        }
        return $response;
    }
}
