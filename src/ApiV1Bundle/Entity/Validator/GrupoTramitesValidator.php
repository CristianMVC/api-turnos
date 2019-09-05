<?php
namespace ApiV1Bundle\Entity\Validator;

use ApiV1Bundle\Repository\PuntoAtencionRepository;
use ApiV1Bundle\Repository\GrupoTramiteRepository;
use ApiV1Bundle\Entity\ValidateResultado;
use ApiV1Bundle\Repository\TurnoRepository;

/**
 * Class GrupoTramitesValidator
 * @package ApiV1Bundle\Entity\Validator
 */
class GrupoTramitesValidator extends SNTValidator
{
    /** @var PuntoAtencionRepository  */
    private $puntoAtencionRepository;
    /** @var GrupoTramiteRepository  */
    private $grupoTramitesRepository;
    /** @var TurnoRepository  */
    private $turnoRepository;

    /**
     * GrupoTramitesValidator constructor.
     * @param PuntoAtencionRepository $puntoAtencionRepository
     * @param GrupoTramiteRepository $grupoTramitesRepository
     * @param TurnoRepository $turnoRepository
     */
    public function __construct(
        PuntoAtencionRepository $puntoAtencionRepository,
        GrupoTramiteRepository $grupoTramitesRepository,
        TurnoRepository $turnoRepository
    ) {
        $this->puntoAtencionRepository = $puntoAtencionRepository;
        $this->grupoTramitesRepository = $grupoTramitesRepository;
        $this->turnoRepository = $turnoRepository;
    }

    /**
     * Validar tramites del grupo de tramites
     *
     * @param object $tramite objeto trámite
     * @param integer $grupoTramitesId Identificador único del grupo de trámites
     * @param integer $puntoAtencionId Identificador único del punto de atención
     * @return ValidateResultado
     */
    public function validarTramite($tramite, $grupoTramitesId, $puntoAtencionId)
    {
        $errors = [];
        if (! $tramite) {
            $errors[] = 'Tramite inexistente';
            return new ValidateResultado(null, $errors);
        }

        // verificamos que el tramite lo pueda realizar el punto de atención
        $checkEnabled = $this->puntoAtencionRepository->checkTramiteRelationship($puntoAtencionId, $tramite->getId());
        if ($checkEnabled) {
            // verificamos que el tramite no pertenezca a otro grupo
            $checkOwnership = $this->grupoTramitesRepository->checkRelationship(
                $puntoAtencionId,
                $tramite->getId(),
                $grupoTramitesId
            );
            if ($checkOwnership) {
                $errors[] = "El tramite {$tramite->getNombre()} ({$tramite->getId()}) ya está asociado a un grupo";
            }
        } else {
            $errors[] = "El tramite {$tramite->getNombre()} no puede ser realizado por el punto de atención";
        }
        return new ValidateResultado(null, $errors);
    }

    /**
     * Validar parámetros que se envían
     *
     * @param object $puntoAtencion objeto punto de atención
     * @param array $params arreglo con los datos a validar
     * @return ValidateResultado
     */
    public function validarParams($params, $puntoAtencion)
    {
        $errors = $this->validarCrearEditarBase($params);

        if (! $puntoAtencion) {
            $errors[] = 'Punto de atención inexistente';
            return new ValidateResultado(null, $errors);
        }

        return new ValidateResultado(null, $errors);
    }

    /*
     * Validar si existe el grupo de trámites
     *
     * @param object $grupoTramites objeto grupo-trámites
     * @return ValidateResultado
     */
    public function validarGruposTramites($grupoTramites)
    {
        $errors = [];

        if (! $grupoTramites) {
            $errors[] = 'Grupo de tramites inexistente';
        }
        return new ValidateResultado(null, $errors);
    }

    /**
     * Validar datos para editar el grupo de trámites
     *
     * @param array $params arreglo con los datos a validar
     * @param integer $grupoTramitesId Identificador único del grupo de trámites
     * @param integer $puntoAtencionId Identificador único del punto de atención
     * @return ValidateResultado
     */
    public function validarEditar($params, $puntoAtencionId, $grupoTramitesId)
    {
        $errors = $this->validarCrearEditarBase($params);

        $puntoAtencion = $this->puntoAtencionRepository->find($puntoAtencionId);
        if (! $puntoAtencion) {
            $errors[] = 'Punto de atención inexistente';
        }

        $grupoTramites = $this->grupoTramitesRepository->find($grupoTramitesId);
        if (! $grupoTramites) {
            $errors[] = 'Grupo de tramites inexistente';
        }

        if ($grupoTramites && isset($params['tramites'])) {
            foreach ($grupoTramites->getTramites() as $tramite) {
                // si no está en el array de tramites, es porque se tiene que eliminar
                if (! in_array($tramite->getId(), $params['tramites'])) {
                    // valido si el trámite tiene turnos asociados
                    $ultimoTurno = $this->turnoRepository->findUltimoTurno($tramite->getId(), $puntoAtencion->getId());
                    if ($ultimoTurno) {
                        $errors[] = "El tramite {$tramite->getNombre()} tiene turnos asociados. " .
                            "Último turno el {$ultimoTurno['fecha']->format('Y-m-d')}.";
                    }
                }
            }
        }
        return new ValidateResultado(null, $errors);
    }

    /**
     * Validaciones básicas de crear y editar
     *
     * @param array $params arreglo con los datos a validar
     * @return mixed
     */
    private function validarCrearEditarBase($params)
    {
        return $this->validar($params, [
            'nombre' => 'required',
            'horizonte' => 'required:horizonte',
            'intervalo' => 'required:intervalo',
            'tramites' => 'required:matriz'
        ]);
    }

    /**
     * Validar si el tramite está asociado al grupo de atención
     *
     * @param integer $grupoPuntoAtencionId identificador único del grupo tramite
     * @param integer $puntoAtencionId identificador único del punto de atención
     * @return ValidateResultado
     */
    public function validarGrupoPuntoAtencion($grupoPuntoAtencionId, $puntoAtencionId)
    {
        $errors = [];
        if ($grupoPuntoAtencionId != $puntoAtencionId) {
            $errors[] = 'El grupo de tramites no está asociado al punto de atención';
        }
        return new ValidateResultado(null, $errors);
    }

    /**
     * Validar si un tramite pertenece a un grupo de tramites
     *
     * @param integer $grupoTramitesId identificador único del grupo tramite
     * @param integer $tramiteId identificador único del tramite
     * @return ValidateResultado
     */
    public function validarGrupoTramite($grupoTramitesId, $tramiteId)
    {
        $errors = [];
        $checkPuntoAtencion = $this->grupoTramitesRepository->checkTramiteGrupoTramite($grupoTramitesId, $tramiteId);
        if ($checkPuntoAtencion) {
            $errors[] = 'El tramite y el grupo de tramites están asociados';
        }
        return new ValidateResultado(null, $errors);
    }

    /**
     * Validar si se puede eliminar el grupo de trámites
     *
     * @param integer $grupoTramitesId identificador único del grupo tramite
     * @param integer $puntoAtencionId identificador único del punto de atención
     * @return ValidateResultado
     */
    public function validarEliminar($grupoTramitesId, $puntoAtencionId)
    {
        $errors = [];

        $grupoTramites = $this->grupoTramitesRepository->find($grupoTramitesId);
        if (! $grupoTramites || $grupoTramites->getPuntoAtencion()->getId() != $puntoAtencionId) {
            return new ValidateResultado(null, ['Grupo de tramites inexistente']);
        }

        $ultimoTurno = $this->turnoRepository->findUltimoTurnoByGrupoTramite($grupoTramitesId);
        if ($ultimoTurno) {
            $errors[] = "El grupo tiene turnos asociados. Último turno el {$ultimoTurno['fecha']->format('Y-m-d')}";
        }
        return new ValidateResultado(null, $errors);
    }
}
