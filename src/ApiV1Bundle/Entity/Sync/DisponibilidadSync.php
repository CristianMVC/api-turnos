<?php

namespace ApiV1Bundle\Entity\Sync;

use ApiV1Bundle\Entity\HorarioAtencion;
use ApiV1Bundle\Entity\ValidateResultado;
use ApiV1Bundle\Entity\Validator\DisponibilidadValidator;
use ApiV1Bundle\Repository\DisponibilidadRepository;
use ApiV1Bundle\Repository\GrupoTramiteRepository;
use ApiV1Bundle\Repository\HorarioAtencionRepository;
use ApiV1Bundle\Repository\PuntoAtencionRepository;

/**
 * Class DisponibilidadSync
 * @package ApiV1Bundle\Entity\Sync
 */
class DisponibilidadSync
{
    /** @var DisponibilidadValidator  */
    private $disponibilidadValidator;
    /** @var DisponibilidadRepository  */
    private $disponibilidadRepository;
    /** @var PuntoAtencionRepository  */
    private $puntoAtencionRepository;
    /** @var GrupoTramiteRepository  */
    private $grupoTramiteRepository;
    /** @var HorarioAtencionRepository  */
    private $horarioAtencionRepository;

    /**
     * DisponibilidadSync constructor.
     * @param DisponibilidadValidator $disponibilidadValidator
     * @param DisponibilidadRepository $disponibilidadRepository
     * @param PuntoAtencionRepository $puntoAtencionRepository
     * @param GrupoTramiteRepository $grupoTramiteRepository
     * @param HorarioAtencionRepository $horarioAtencionRepository
     */
    public function __construct(
        DisponibilidadValidator $disponibilidadValidator,
        DisponibilidadRepository $disponibilidadRepository,
        PuntoAtencionRepository $puntoAtencionRepository,
        GrupoTramiteRepository $grupoTramiteRepository,
        HorarioAtencionRepository $horarioAtencionRepository
    ) {
        $this->disponibilidadValidator = $disponibilidadValidator;
        $this->disponibilidadRepository = $disponibilidadRepository;
        $this->puntoAtencionRepository = $puntoAtencionRepository;
        $this->grupoTramiteRepository = $grupoTramiteRepository;
        $this->horarioAtencionRepository = $horarioAtencionRepository;
    }

    /**
     * Edita la disponibilidad de turnos de un punto de atención, grupo de tramite y horario de atención
     *
     * @param integer $idRow id de la fila del horario
     * @param array $params arreglo con los datos para la edición (puntoAtencion, grupoTramite, cantidadTurnos)
     * @return mixed
     */
    public function edit($idRow, $params)
    {
        $validateResultado = $this->disponibilidadValidator->validarParamsEdit($idRow, $params);

        if (! $validateResultado->hasError()) {
            $puntoAtencion = $this->puntoAtencionRepository->find($params['puntoAtencion']);

            $horariosAtencion = $this->horarioAtencionRepository->findBy([
                'puntoAtencion' => $puntoAtencion->getId(),
                'idRow' => $idRow
            ]);
            $grupoTramite = $this->grupoTramiteRepository->find($params['grupoTramite']);

            $cantidadTurnos = ($params['cantidadTurnos']) ? (int)$params['cantidadTurnos'] : 0;

            foreach ($horariosAtencion as $horarioAtencion) {
                $disponibilidad = $this->disponibilidadRepository->findOneBy([
                    'puntoAtencion' => $puntoAtencion->getId(),
                    'horarioAtencion' => $horarioAtencion->getId(),
                    'grupoTramite' => $grupoTramite->getId()
                ]);

                $disponibilidad->setCantidadTurnos($cantidadTurnos);
            }
        }

        return $validateResultado;
    }

    /**
     * Listar la capacidad de un Punto de atencion agrupada por RowId, horario de atencion y grupo de trámite
     *
     * @param object $puntoAtencion objeto punto de atención
     * @param object $grupoTramite objeto grupo trámite
     * @return mixed
     */
    public function listarCapacidadAgrupadaByHorario($puntoAtencion, $grupoTramite)
    {
        $result = [];
        $horarios = $this->horarioAtencionRepository->getHorariosGroupByRowId($puntoAtencion);

        foreach ($horarios as $horario) {
            $disponibilidad = $this->disponibilidadRepository->findOneBy([
                'puntoAtencion' => $puntoAtencion->getId(),
                'horarioAtencion' => $horario->getId(),
                'grupoTramite' => $grupoTramite->getId()
            ]);
            $result[] = [
                'idRow' => $horario->getIdRow(),
                'horaInicio' => $horario->getHoraInicio()->format('H:i'),
                'horaFin' => $horario->getHoraFin()->format('H:i'),
                'diasSemana' => $this->horarioAtencionRepository->getDiasSemanaByRow(
                    $puntoAtencion->getId(),
                    $horario->getIdRow()
                ),
                'cantidadTurnos' => $disponibilidad->getCantidadTurnos()
            ];
        }
        return $result;
    }
}
