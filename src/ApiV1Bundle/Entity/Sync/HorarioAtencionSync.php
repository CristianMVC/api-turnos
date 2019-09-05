<?php
namespace ApiV1Bundle\Entity\Sync;

use ApiV1Bundle\Entity\Factory\HorarioAtencionFactory;
use ApiV1Bundle\Entity\Factory\HorarioAtencionIntervalo;
use ApiV1Bundle\Entity\HorarioAtencion;
use ApiV1Bundle\Entity\ValidateResultado;
use ApiV1Bundle\Entity\Validator\HorarioAtencionValidator;
use ApiV1Bundle\Repository\DisponibilidadRepository;
use ApiV1Bundle\Repository\GrupoTramiteRepository;
use ApiV1Bundle\Repository\HorarioAtencionRepository;
use ApiV1Bundle\Repository\PuntoAtencionRepository;

/**
 * Class HorarioAtencionSync
 * @package ApiV1Bundle\Entity\Sync
 */

class HorarioAtencionSync extends HorarioAtencionIntervalo
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
     * HorarioAtencionSync constructor.
     * @param PuntoAtencionRepository $puntoAtencionRepository
     * @param HorarioAtencionRepository $horarioAtencionRepository
     * @param HorarioAtencionValidator $horarioAtencionValidator
     * @param DisponibilidadRepository $disponibilidadRepository
     * @param GrupoTramiteRepository $grupoTramiteRepository
     */
    public function __construct(
        PuntoAtencionRepository  $puntoAtencionRepository,
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
     * Modificar horario de atención
     *
     * @param array $params arreglo con datos del horario de atención
     * @param integer $puntoAtencionId Identificador único del punto de atención
     * @param integer $idRow Identificador de fila del horario a modificar
     * @return mixed
     */
    public function edit($params, $puntoAtencionId, $idRow)
    {
        $errors = [];

        $haPersisted = $this->horarioAtencionRepository->findBy([
            'idRow' => $idRow,
            'puntoAtencion' => $puntoAtencionId
        ]);
        $puntoAtencion = $this->puntoAtencionRepository->find($puntoAtencionId);
        $validateResultado = $this->horarioAtencionValidator->validarParams($puntoAtencion, $params);

        if (! $validateResultado->hasError()) {
            if (! $haPersisted) {
                return new ValidateResultado(null, ['Horarios de atencion inexistentes']);
            }

            if ($this->esIgual($haPersisted, $params)) {
                return new ValidateResultado($haPersisted[0], []);
            }

            //Elimina los Horarios de Atencion existentos y los crea de nuevo con disponibilidad en 0
            if (! count($errors)) {
                $this->horarioAtencionRepository->beginTransaction();
                try {
                    $validateResultado = $this->delete($puntoAtencionId, $idRow);
                    if (! $validateResultado->hasError()) {

                        $horarioAtencionFactory = new HorarioAtencionFactory(
                            $this->puntoAtencionRepository,
                            $this->horarioAtencionRepository,
                            $this->horarioAtencionValidator,
                            $this->disponibilidadRepository,
                            $this->grupoTramiteRepository
                        );

                        $validateResultado = $horarioAtencionFactory->create($params, $puntoAtencionId);

                        if (!$validateResultado->hasError()) {
                            $this->horarioAtencionRepository->commit();
                            return new ValidateResultado($validateResultado->getEntity(), []);
                        }
                    }
                } catch (\Exception $e) {
                    $this->horarioAtencionRepository->rollBack();
                }
                return $validateResultado;
            }
            return new ValidateResultado(null, $errors);
        }
        return $validateResultado;
    }

    /**
     * Eliminar horario de atencion
     *
     * @param integer $puntoAtencionId Identificador único del punto de atención
     * @param integer $idRow Identificador de fila del horario a quitar
     * @return mixed
     */
    public function delete($puntoAtencionId, $idRow)
    {
        $errors = [];

        $haPersisted = $this->horarioAtencionRepository->findBy([
            'idRow' => $idRow,
            'puntoAtencion' => $puntoAtencionId
        ]);

        if (! $haPersisted) {
            $errors[] = 'Horarios de atención inexistentes';
        }

        if (! count($errors)) {
            try {
                $this->horarioAtencionRepository->beginTransaction();
                foreach ($haPersisted as $horario) {
                    //elimina el horario de atención
                    $this->horarioAtencionRepository->remove($horario);
                    //elimina la disponibilidad para el horario de atención
                    $colDisponibilidad = $this->disponibilidadRepository->findBy([
                        'puntoAtencion' => $puntoAtencionId,
                        'horarioAtencion' => $horario->getId()
                    ]);

                    foreach ($colDisponibilidad as $disponibilidad) {
                        $this->disponibilidadRepository->remove($disponibilidad);
                    }
                }
                $this->horarioAtencionRepository->commit();
            } catch (\Exception $exception) {
                $this->horarioAtencionRepository->rollback();
            }
        }
        return new ValidateResultado(null, $errors);
    }

    /**
     * Lista los horarios de atencion agrupados por fila
     *
     * @param object $puntoAtencion objeto punto de atención
     * @return mixed
     */
    public function listarHorariosAtencionAgrupados($puntoAtencion)
    {
        $result = [];
        $rowsController = [];
        $horariosAtencion = $puntoAtencion->getHorariosAtencion();
        foreach ($horariosAtencion as $horarioAtencion) {
            if (! in_array($horarioAtencion->getIdRow(), $rowsController)) {
                $rowsController[] = $horarioAtencion->getIdRow();
                $result[] = [
                    'idRow' => $horarioAtencion->getIdRow(),
                    'horaInicio' => $horarioAtencion->getHoraInicio()->format('H:i'),
                    'horaFin' => $horarioAtencion->getHoraFin()->format('H:i'),
                    'diasSemana' => $this->horarioAtencionRepository->getDiasSemanaByRow(
                        $puntoAtencion->getId(),
                        $horarioAtencion->getIdRow()
                    )
                ];
            }
        }
        return $result;
    }

    /**
     * Obtener los rangos de atencón por idRow y el Punto de Atención
     *
     * @param array $horarioAtencion array con los horarios
     * @param integer $idRow identificador de fila del horario
     * @return ValidateResultado|array
     */
    public function listarHorariosAtencionAgrupadosByidRow($horarioAtencion, $idRow)
    {
        $diasSemana = [];
        $horaInicio = '';
        $horaFin = '';

        foreach ($horarioAtencion as $horarios) {
            $diasSemana[] = $horarios->getDiaSemana();
            $horaInicio = $horarios->getHoraInicio()->format('H:i');
            $horaFin = $horarios->getHoraFin()->format('H:i');
        }

        $result = [
            'idRow' => $idRow,
            'horaInicio' => $horaInicio,
            'horaFin' => $horaFin,
            'diasSemana' => $diasSemana
        ];
        return $result;
    }

    /**
     * Verifica si un horario de atención existente es igual al pasado por
     * parámetros
     *
     * @param array $horariosAtencion arreglo con horario de atención existente
     * @param array $params  arreglo con horario de atención a comparar
     * @return bool
     */
    private function esIgual($horariosAtencion, $params)
    {
        if (count($horariosAtencion) == 0) {
            return false;
        }

        $result = true;
        $diasSemana = [];

        foreach ($horariosAtencion as $horarioAtencion) {
            /** @var HorarioAtencion $horarioAtencion */
            $result = $result &&
                ($horarioAtencion->getHoraInicio()->format('H:i') == $params['horaInicio']) &&
                ($horarioAtencion->getHoraFin()->format('H:i') == $params['horaFin']);

            $diasSemana[] = $horarioAtencion->getDiaSemana();
        }

        $mismosDias = (count(array_diff($diasSemana, $params['diasSemana'])) == 0) &&
            (count($diasSemana) == count($params['diasSemana']));
        return $result && $mismosDias;
    }
}
