<?php

namespace ApiV1Bundle\Entity\Factory;


use ApiV1Bundle\Entity\Disponibilidad;
use ApiV1Bundle\Entity\ValidateResultado;
use ApiV1Bundle\Entity\Validator\DisponibilidadValidator;
use ApiV1Bundle\Repository\GrupoTramiteRepository;
use ApiV1Bundle\Repository\HorarioAtencionRepository;
use ApiV1Bundle\Repository\PuntoAtencionRepository;

/**
 * Class DisponibilidadFactory
 * @package ApiV1Bundle\Entity\Factory
 */
class DisponibilidadFactory
{
    /** @var DisponibilidadValidator  */
    private $disponibilidadValidator;
    /** @var PuntoAtencionRepository  */
    private $puntoAtencionRepository;
    /** @var GrupoTramiteRepository  */
    private $grupoTramiteRepository;
    /** @var HorarioAtencionRepository  */
    private $horarioAtencionRepository;

    /**
     * DisponibilidadFactory constructor.
     * @param PuntoAtencionRepository $puntoAtencionRepository
     * @param GrupoTramiteRepository $grupoTramiteRepository
     * @param HorarioAtencionRepository $horarioAtencionRepository
     * @param DisponibilidadValidator $disponibilidadValidator
     */
    public function __construct(
        PuntoAtencionRepository $puntoAtencionRepository,
        GrupoTramiteRepository $grupoTramiteRepository,
        HorarioAtencionRepository $horarioAtencionRepository,
        DisponibilidadValidator $disponibilidadValidator
    ) {
        $this->puntoAtencionRepository = $puntoAtencionRepository;
        $this->grupoTramiteRepository = $grupoTramiteRepository;
        $this->horarioAtencionRepository = $horarioAtencionRepository;
        $this->disponibilidadValidator = $disponibilidadValidator;
    }

    /**
     * Creación de la Disponibilidad. El Disponibilidad Factory valida los datos que vienen por params y crea una nueva disponibilidad
     * para un Punto de Atención por horario de atención y grupo de tramite.
     *
     * @param array $params array con los datos del trámite a crear
     * @return mixed
     */
    public function create($params)
    {
        $validateResultado = $this->disponibilidadValidator->validarParamsCreate($params);

        if (! $validateResultado->hasError()) {

            $puntoAtencion = $this->puntoAtencionRepository->find($params['puntoAtencion']);
            $grupoTramite = $this->grupoTramiteRepository->find($params['grupoTramite']);
            $horarioAtencion = $this->horarioAtencionRepository->find($params['rangoHorario']);
            $cantidadTurnos = ($params['cantidadTurnos']) ? (int) $params['cantidadTurnos'] : 0;

            $disponibilidad = new Disponibilidad($puntoAtencion, $grupoTramite, $horarioAtencion, $cantidadTurnos);

            return new ValidateResultado($disponibilidad, []);
        }

        return $validateResultado;
    }
}