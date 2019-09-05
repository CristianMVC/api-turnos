<?php
namespace ApiV1Bundle\Entity\Factory;

use ApiV1Bundle\Entity\Disponibilidad;
use ApiV1Bundle\Entity\HorarioAtencion;
use ApiV1Bundle\Entity\Sync\GrupoTramiteIntervaloSync;
use ApiV1Bundle\Entity\ValidateResultado;
use ApiV1Bundle\Repository\DisponibilidadRepository;
use ApiV1Bundle\Repository\GrupoTramiteRepository;
use ApiV1Bundle\Repository\PuntoAtencionRepository;
use ApiV1Bundle\Repository\HorarioAtencionRepository;
use ApiV1Bundle\Entity\Validator\HorarioAtencionValidator;

/**
 * Class HorarioAtencionFactory
 * @package ApiV1Bundle\Entity\Factory
 */

class HorarioAtencionFactory extends HorarioAtencionIntervalo
{
    /** @var PuntoAtencionRepository  */
    private $puntoAtencionRepository;
    /** @var HorarioAtencionRepository  */
    private $horarioAtencionRepository;
    /** @var HorarioAtencionValidator  */
    private $horarioAtencionValidator;
    /** @var DisponibilidadRepository  */
    private $disponibilidadRepository;
    /** @var GrupoTramiteRepository  */
    private $grupoTramiteRepository;

    /**
     * HorarioAtencionFactory constructor.
     * @param PuntoAtencionRepository $puntoAtencionRepository
     * @param HorarioAtencionRepository $horarioAtencionRepository
     * @param HorarioAtencionValidator $horarioAtencionValidator
     * @param DisponibilidadRepository $disponibilidadRepository
     * @param GrupoTramiteRepository $grupoTramiteRepository
     */
    public function __construct(
        PuntoAtencionRepository $puntoAtencionRepository,
        HorarioAtencionRepository $horarioAtencionRepository,
        HorarioAtencionValidator $horarioAtencionValidator,
        DisponibilidadRepository $disponibilidadRepository,
        GrupoTramiteRepository $grupoTramiteRepository
    ) {
        $this->puntoAtencionRepository = $puntoAtencionRepository;
        $this->horarioAtencionRepository = $horarioAtencionRepository;
        $this->horarioAtencionValidator = $horarioAtencionValidator;
        $this->disponibilidadRepository = $disponibilidadRepository;
        $this->grupoTramiteRepository = $grupoTramiteRepository;
    }

    /**
     * Crear un nuevo horario de atención
     *
     * @param array $params array con los datos para crear un nuevo horario de atención
     * @param integer $puntoAtencionId Identificador único del punto de antención
     * @return mixed
     */
    public function create($params, $puntoAtencionId)
    {
        $puntoAtencion = $this->puntoAtencionRepository->find($puntoAtencionId);
        $validateParams = $this->horarioAtencionValidator->validarParams($puntoAtencion, $params);
        if (! $validateParams->hasError()) {
            $validateParams = $this->horarioAtencionValidator->validarHorarios($params);
            if (! $validateParams->hasError()) {
                $diasDeLaSemana = $params['diasSemana'];
                $horariosAtencion = $puntoAtencion->getHorariosAtencion();
                $grupoTramiteIntervalo = new GrupoTramiteIntervaloSync(
                    $this->grupoTramiteRepository
                );
                $grupoTramiteIntervalo->resetIntervalos($puntoAtencion, $this->getIntervalo($params, $horariosAtencion));
                foreach ($diasDeLaSemana as $diaSemana) {
                    $horarioAtencion = new HorarioAtencion(
                        $puntoAtencion,
                        $diaSemana,
                        new \DateTime($params['horaInicio']),
                        new \DateTime($params['horaFin']),
                        $this->horarioAtencionRepository->getNextRowId()
                    );
                    $this->horarioAtencionRepository->persist($horarioAtencion);
                    $this->saveDisponibilidad($puntoAtencion, $horarioAtencion);
                }
                return new ValidateResultado($horarioAtencion, []);
            }
        }
        return $validateParams;
    }

    /**
     * Guardar disponibilidad
     *
     * @param object $puntoAtencion objeto puntoAtencion
     * @param object $horarioAtencion objeto HorarioAtencion
     * @return mixed
     */
    private function saveDisponibilidad($puntoAtencion, $horarioAtencion)
    {
        $gruposTramites = $puntoAtencion->getGrupoTramites();

        foreach ($gruposTramites as $grupoTramite) {
            $disponibilidad = new Disponibilidad($puntoAtencion, $grupoTramite, $horarioAtencion, 0);

            $this->disponibilidadRepository->persist($disponibilidad);
        }
    }
}
