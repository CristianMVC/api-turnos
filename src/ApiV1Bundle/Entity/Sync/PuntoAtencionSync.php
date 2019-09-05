<?php

namespace ApiV1Bundle\Entity\Sync;

use ApiV1Bundle\Entity\DiaNoLaborable;
use ApiV1Bundle\Entity\PuntoTramite;
use ApiV1Bundle\Entity\ValidateResultado;
use ApiV1Bundle\Repository\DiaNoLaborableRepository;
use ApiV1Bundle\Repository\LocalidadRepository;
use ApiV1Bundle\Repository\ProvinciaRepository;
use ApiV1Bundle\Repository\PuntoAtencionRepository;
use ApiV1Bundle\Repository\PuntoTramiteRepository;
use ApiV1Bundle\Repository\TramiteRepository;
use ApiV1Bundle\Repository\AreaRepository;
use ApiV1Bundle\Entity\Validator\PuntoAtencionValidator;

/**
 * Class PuntoAtencionSync
 * @package ApiV1Bundle\Entity\Sync
 */
class PuntoAtencionSync
{
    /** @var PuntoAtencionRepository  */
    private $puntoAtencionRepository;
    /** @var AreaRepository  */
    private $areaRepository;
    /** @var TramiteRepository  */
    private $tramiteRepository;
    /** @var ProvinciaRepository  */
    private $provinciaRepository;
    /** @var LocalidadRepository  */
    private $localidadRepository;
    /** @var PuntoAtencionValidator  */
    private $puntoAtencionValidator;
    /** @var DiaNoLaborableRepository  */
    private $diaNoLaborableRepository;
    /** @var PuntoTramiteRepository  */
    private $puntoTramiteRepository;

    /**
     * PuntoAtencionSync constructor.
     * @param PuntoAtencionRepository $puntoAtencionRepository
     * @param AreaRepository $areaRepository
     * @param TramiteRepository $tramiteRepository
     * @param ProvinciaRepository $provinciaRepository
     * @param LocalidadRepository $localidadRepository
     * @param PuntoAtencionValidator $puntoAtencionValidator
     * @param DiaNoLaborableRepository $diaNoLaborableRepository
     * @param PuntoTramiteRepository $puntoTramiteRepository
     */
    public function __construct(
        PuntoAtencionRepository $puntoAtencionRepository,
        AreaRepository $areaRepository,
        TramiteRepository $tramiteRepository,
        ProvinciaRepository $provinciaRepository,
        LocalidadRepository $localidadRepository,
        PuntoAtencionValidator $puntoAtencionValidator,
        DiaNoLaborableRepository $diaNoLaborableRepository,
        PuntoTramiteRepository $puntoTramiteRepository
    ) {
        $this->puntoAtencionRepository = $puntoAtencionRepository;
        $this->areaRepository = $areaRepository;
        $this->tramiteRepository = $tramiteRepository;
        $this->provinciaRepository = $provinciaRepository;
        $this->localidadRepository = $localidadRepository;
        $this->puntoAtencionValidator = $puntoAtencionValidator;
        $this->diaNoLaborableRepository = $diaNoLaborableRepository;
        $this->puntoTramiteRepository = $puntoTramiteRepository;
    }

    /**
     * Valida que los parametros para chequear la disponibilidad de PA sean correctos
     *
     * @param array $params array con los datos para validar la disponibilidad
     * @return ValidateResultado|bool
     */
    public function validateParamsDisponibilidad($params)
    {
    }

    /**
     * Editar punto de atencion
     *
     * @param integer $id Identificador único para un punto de atención
     * @param array $params array con los datos para editar el punto de atención
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function edit($id, $params)
    {
        $puntoAtencion = $this->puntoAtencionRepository->find($id);

        $validateResultado = $this->puntoAtencionValidator->validarEditar($puntoAtencion, $params);

        $params['latitud'] = isset($params['latitud']) ? (float)$params['latitud'] : null;
        $params['longitud'] = isset($params['longitud']) ? (float)$params['longitud'] : null;

        if (!$validateResultado->hasError()) {
            $provincia = $this->provinciaRepository->find($params['provincia']);
            $localidad = $this->localidadRepository->find($params['localidad']);
            $area = $this->areaRepository->find($params['area']);

            // actualizar punto de atención
            $puntoAtencion->setNombre($params['nombre']);
            $puntoAtencion->setDireccion($params['direccion']);
            $puntoAtencion->setLatitud($params['latitud']);
            $puntoAtencion->setLongitud($params['longitud']);
            $puntoAtencion->setProvincia($provincia);
            $puntoAtencion->setLocalidad($localidad);
            $puntoAtencion->setArea($area);
            $puntoAtencion->setEstado($params['estado']);

            if (isset($params['tramites'])) {
                $tramitesDelPunto = [];
                // los puntos de atención a eliminar
                foreach ($puntoAtencion->getTramites() as $puntoTramite) {
                    $tramite = $puntoTramite->getTramite();
                    // si no está en la lista que mandan, es porque lo quieren eliminar
                    if (!in_array($tramite->getId(), $params['tramites'])) {
                        $this->puntoTramiteRepository->delete($puntoTramite);
                    } else {
                        $tramitesDelPunto[] = $tramite->getId();
                    }
                }
                // los tramites a agregar
                foreach ($params['tramites'] as $tramiteId) {
                    if (!in_array($tramiteId, $tramitesDelPunto)) {
                        $tramite = $this->tramiteRepository->find($tramiteId);
                        $relacion = new PuntoTramite(
                            $puntoAtencion,
                            $tramite
                        );
                        $this->puntoTramiteRepository->save($relacion);
                        $puntoAtencion->addTramite($relacion);
                    }
                }
            }
            return new ValidateResultado($puntoAtencion, []);
        }
        $errors = $validateResultado->getErrors();
        return new ValidateResultado(null, $errors['errors']);
    }

    /**
     * Eliminar punto de atención
     *
     * @param integer $id Identificador único para un punto de atención
     * @return mixed
     */
    public function delete($id)
    {
        $puntoAtencion = $this->puntoAtencionRepository->find($id);
        $validateResultado = $this->puntoAtencionValidator->validarDelete($puntoAtencion);
        if (!$validateResultado->hasError()) {
            return new ValidateResultado($puntoAtencion, []);
        }
        $errors = $validateResultado->getErrors();
        return new ValidateResultado(null, $errors['errors']);
    }

    /**
     * Validar tramites a asignar
     *
     * @param int $id identificador único del punto de atención
     * @param mixed $params srreglo con los trámites
     * @return mixed
     */
    public function setTramites($id, $params)
    {
        $validateResultado = $this->puntoAtencionValidator->validarTramites($id, $params);
        if (!$validateResultado->hasError()) {
            $puntoAtencion = $this->puntoAtencionRepository->find($id);
            $tramites = $params['tramites'];
            foreach ($tramites as $tramiteId) {
                $tramite = $this->tramiteRepository->find($tramiteId);
                $puntoTramite = new PuntoTramite($puntoAtencion, $tramite);
                $this->puntoTramiteRepository->save($puntoTramite);
                $puntoAtencion->addTramite($puntoTramite);
            }
            return new ValidateResultado($puntoAtencion, []);
        }

        return $validateResultado;
    }

    /**
     * Busca tramites disponibles asociados a un punto de atencion y que no estén asociados a un grupo de tramites
     *
     * @param object $puntoAtencion objeto punto de atención
     * @param integer $puntoAtencionId Identificador único para un punto de atención
     * @return mixed
     */
    public function findTramitesDisponibles($puntoAtencion, $puntoAtencionId)
    {
        $listaTramites = [];
        $puntoTramites = $puntoAtencion->getTramites();
        foreach ($puntoTramites as $puntoTramite) {
            $tramite = $puntoTramite->getTramite();
            $tramiteId = $tramite->getId();
            $tramiteLibre = $this->tramiteRepository->checkTramiteGrupoTramite($puntoAtencionId, $tramiteId);
            if ($tramiteLibre == 0) {
                $listaTramites[] = [
                    'id' => $tramite->getId(),
                    'nombre' => $tramite->getNombre()
                ];
            }
        }
        return $listaTramites;
    }

    /**
     * Habilitar una fecha como día habil
     *
     * @param integer $id Identificador único para un punto de atención
     * @param array $params arreglo con la fecha
     * @return mixed
     */
    public function habilitarFecha($id, $params)
    {
        $validateResultado = $this->puntoAtencionValidator->validarFecha($params);

        if ($validateResultado->hasError()) {
            $errors = $validateResultado->getErrors();
            return new ValidateResultado(null, $errors['errors']);
        }

        $puntoAtencion = $this->puntoAtencionRepository->find($id);
        $validatePuntoAtencion = $this->puntoAtencionValidator->verificaPuntoAtencion($puntoAtencion);
        if ($validatePuntoAtencion->hasError()) {
            $errors = $validatePuntoAtencion->getErrors();
            return new ValidateResultado(null, $errors['errors']);
        }

        $fecha = new \DateTime($params['fecha']);

        //get dia no laborable
        $diaNoLaborable = $this->diaNoLaborableRepository->findOneBy([
            'fecha' => $fecha,
            'puntoAtencion' => $puntoAtencion
        ]);

        if ($diaNoLaborable) {
            //remove dia no laborable al punto de atencion
            $this->diaNoLaborableRepository->remove($diaNoLaborable);
        }
        return new ValidateResultado($puntoAtencion, []);
    }

    /**
     * Agrega una fecha como día no habíl para un punto de atención
     *
     * @param integer $id Identificador único para un punto de atención
     * @param array $params arreglo con la fecha
     * @return mixed
     */
    public function agregarDiaNoHabil($id, $params)
    {
        $puntoAtencion = $this->puntoAtencionRepository->find($id);

        $validateResultado = $this->puntoAtencionValidator->validarNuevoDiaNoHabil($params, $puntoAtencion);

        if ($validateResultado->hasError()) {
            $errors = $validateResultado->getErrors();
            return new ValidateResultado(null, $errors['errors']);
        }

        $fecha = new \DateTime($params['fecha']);

        $diaNoLaborable = new DiaNoLaborable($fecha, $puntoAtencion);

        $this->diaNoLaborableRepository->save($diaNoLaborable);

        return new ValidateResultado($puntoAtencion, []);
    }

    /**
     * Inhabilitar día
     *
     * @param object $puntoAtencion PuntoAtencion
     * @param array $params arreglo con la fecha
     * @return ValidateResultado
     */
    public function inhabilitarDia($puntoAtencion, $params)
    {
        $validateResultado = $this->puntoAtencionValidator->validarInhabilitarDia($puntoAtencion, $params);

        if (!$validateResultado->hasError()) {
            $fecha = new \DateTime($params['fecha']);
            $diaNoLaborable = new DiaNoLaborable($fecha, $puntoAtencion);
            $this->diaNoLaborableRepository->save($diaNoLaborable);

            $validateResultado = new ValidateResultado($puntoAtencion, []);
        }

        return $validateResultado;
    }
}
