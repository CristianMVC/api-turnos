<?php
namespace ApiV1Bundle\Entity\Factory;

use ApiV1Bundle\Entity\ValidateResultado;
use ApiV1Bundle\Repository\AreaRepository;
use ApiV1Bundle\Entity\PuntoAtencion;
use ApiV1Bundle\Repository\ProvinciaRepository;
use ApiV1Bundle\Repository\LocalidadRepository;
use ApiV1Bundle\Entity\Validator\PuntoAtencionValidator;
use ApiV1Bundle\Repository\PuntoAtencionRepository;
use ApiV1Bundle\Repository\TramiteRepository;

/**
 * Class PuntoAtencionFactory
 * @package ApiV1Bundle\Entity\Factory
 */
class PuntoAtencionFactory
{
    /** @var ProvinciaRepository  */
    private $provinciaRepository;
    /** @var LocalidadRepository  */
    private $localidadRepository;
    /** @var AreaRepository  */
    private $areaRepository;
    /** @var PuntoAtencionValidator  */
    private $puntoAtencionValidator;
    /** @var TramiteRepository  */
    private $tramiteRepository;

    /**
     * PuntoAtencionFactory constructor.
     * @param ProvinciaRepository $provinciaRepository
     * @param LocalidadRepository $localidadRepository
     * @param AreaRepository $areaRepository
     * @param PuntoAtencionValidator $puntoAtencionValidator
     * @param TramiteRepository $tramiteRepository
     */
    public function __construct(
        ProvinciaRepository $provinciaRepository,
        LocalidadRepository $localidadRepository,
        AreaRepository $areaRepository,
        PuntoAtencionValidator $puntoAtencionValidator,
        TramiteRepository $tramiteRepository
    ) {
        $this->provinciaRepository = $provinciaRepository;
        $this->localidadRepository = $localidadRepository;
        $this->areaRepository = $areaRepository;
        $this->puntoAtencionValidator = $puntoAtencionValidator;
        $this->tramiteRepository = $tramiteRepository;
    }

    /**
     * crear un objeto punto de atención
     * @param array $params array con los datos para crear un punto de atención
     * @return mixed
     */
    public function create($params)
    {
        $validateResultados = $this->puntoAtencionValidator->validarCreate($params);

        $params['latitud'] = isset($params['latitud']) ? (float)$params['latitud'] : null;
        $params['longitud'] = isset($params['longitud']) ? (float)$params['longitud'] : null;
        $estado = isset($params['estado']) ? $params['estado'] : 1;

        if (!$validateResultados->hasError()) {
            $provincia = $this->provinciaRepository->find($params['provincia']);
            $localidad = $this->localidadRepository->find($params['localidad']);
            $area = $this->areaRepository->find($params['area']);

            $puntoAtencion = new PuntoAtencion(
                $params['nombre'],
                $params['direccion']
            );
            $puntoAtencion->setProvincia($provincia);
            $puntoAtencion->setLocalidad($localidad);
            $puntoAtencion->setArea($area);
            $puntoAtencion->setLatitud($params['latitud']);
            $puntoAtencion->setLongitud($params['longitud']);
            $puntoAtencion->setEstado($estado);

            return new ValidateResultado($puntoAtencion, []);
        }

        return $validateResultados;
    }
}
