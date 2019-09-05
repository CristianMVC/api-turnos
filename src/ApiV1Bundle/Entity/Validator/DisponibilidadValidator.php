<?php
namespace ApiV1Bundle\Entity\Validator;

use ApiV1Bundle\Entity\HorarioAtencion;
use ApiV1Bundle\Entity\ValidateResultado;
use ApiV1Bundle\Repository\DiaNoLaborableRepository;
use ApiV1Bundle\Repository\DisponibilidadRepository;
use ApiV1Bundle\Repository\GrupoTramiteRepository;
use ApiV1Bundle\Repository\HorarioAtencionRepository;
use ApiV1Bundle\Repository\PuntoAtencionRepository;
use ApiV1Bundle\Repository\TramiteRepository;
use ApiV1Bundle\Repository\ProvinciaRepository;
use ApiV1Bundle\Repository\LocalidadRepository;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class DisponibilidadValidator
 * @package ApiV1Bundle\Entity\Validator
 */
class DisponibilidadValidator extends SNTValidator
{
    /** @var TramiteRepository  */
    private $tramiteRepository;
    /** @var ProvinciaRepository  */
    private $provinciaRepository;
    /** @var LocalidadRepository  */
    private $localidadRepository;
    /** @var PuntoAtencionRepository  */
    private $puntoAtencionRepository;
    /** @var HorarioAtencionRepository  */
    private $horarioAtencionRepository;
    /** @var GrupoTramiteRepository  */
    private $grupoTramiteRepository;
    /** @var DisponibilidadRepository  */
    private $disponibilidadRepository;
    /** @var DiaNoLaborableRepository  */
    private $diaNoLaborableRepository;

    /**
     * DisponibilidadValidator constructor.
     * @param TramiteRepository $tramiteRepository
     * @param ProvinciaRepository $provinciaRepository
     * @param LocalidadRepository $localidadRepository
     * @param PuntoAtencionRepository $puntoAtencionRepository
     * @param HorarioAtencionRepository $horarioAtencionRepository
     * @param GrupoTramiteRepository $grupoTramiteRepository
     * @param DisponibilidadRepository $disponibilidadRepository
     * @param DiaNoLaborableRepository $diaNoLaborableRepository
     */
    public function __construct(
        TramiteRepository $tramiteRepository,
        ProvinciaRepository $provinciaRepository,
        LocalidadRepository $localidadRepository,
        PuntoAtencionRepository $puntoAtencionRepository,
        HorarioAtencionRepository $horarioAtencionRepository,
        GrupoTramiteRepository $grupoTramiteRepository,
        DisponibilidadRepository $disponibilidadRepository,
        DiaNoLaborableRepository $diaNoLaborableRepository
    ) {
        $this->tramiteRepository = $tramiteRepository;
        $this->provinciaRepository = $provinciaRepository;
        $this->localidadRepository = $localidadRepository;
        $this->puntoAtencionRepository = $puntoAtencionRepository;
        $this->horarioAtencionRepository = $horarioAtencionRepository;
        $this->grupoTramiteRepository = $grupoTramiteRepository;
        $this->disponibilidadRepository = $disponibilidadRepository;
        $this->diaNoLaborableRepository = $diaNoLaborableRepository;
    }

    /**
     * Valida los parametros para obtener un punto de atención
     *
     * @param array $params array con los datos para obtener un punto atención y validarlo
     * @return ValidateResultado
     */
    public function validarParamsToPuntoAtencion($params)
    {

        $errors = $this->validar($params, [
            'tramiteId' => 'required:integer',
            'provincia' => 'required:integer',
            'localidad' => 'required:integer'
        ]);

        if (count($errors) > 0) {
            return new ValidateResultado(null, $errors);
        }

        $errors = $this->checkExist($params);

        if (isset($params['fecha'])) {
            $fecha = new \DateTime($params['fecha']);
            $hoy = new \DateTime("now");

            if (! $fecha) {
                $errors[] = 'Formato de fecha invalido.';
            }

            if ($fecha < $hoy) {
                $errors[] = 'La fecha tiene que ser posterior al día de hoy.';
            }
        }

        return new ValidateResultado(null, $errors);
    }

    /**
     * Valida un punto de atención por fecha
     *
     * @param array $params array con datos para validar un punto de atención por fecha
     * @return ValidateResultado
     */
    public function validarParamsToFechas($params)
    {
        $errors = $this->validarRequisitos($params);

        if (count($errors) > 0) {
            return new ValidateResultado(null, $errors);
        }

        $errors = $this->checkExist($params);

        if (isset($params['puntoAtencion'])) {
            $puntoAtencion = $this->puntoAtencionRepository->find($params['puntoAtencion']);

            if (! $puntoAtencion) {
                $errors[] = 'Punto de atención inexistente';
            }
        }

        return new ValidateResultado(null, $errors);
    }

    /**
     * Valida los datos de un punto de atención para los horarios
     *
     * @param integer $idPuntoAtencion identificador único de punto de atención
     * @param array $params array con datos a validar
     * @return ValidateResultado
     */
    public function validarParamsToHorarios($idPuntoAtencion, $params)
    {
        $puntoAtencion = null;
        $errors = $this->validar($params, [
            'tramiteId' => 'required:integer',
            'fecha' => 'required:date'
        ]);

        if (! count($errors)) {
            $tramite = $this->tramiteRepository->find($params['tramiteId']);
            if (! $tramite) {
                $errors[] = 'Tramite inexistente';
            }

            $puntoAtencion = $this->puntoAtencionRepository->find($idPuntoAtencion);

            if (! $puntoAtencion) {
                $errors[] = 'Punto de Atención inexistente';
            }

            $fecha = new \DateTime(date('Y-m-d', strtotime($params['fecha'])));
            $hoy = new \DateTime('today');
            if ($fecha < $hoy) {
                $errors[] = 'La fecha tiene que ser igual o posterior al día de hoy.';
            }

            if ($this->diaNoLaborableRepository->isDiaNoLaborable($fecha, $puntoAtencion)) {
                $errors[] = 'La fecha es un día no laborable.';
            }
        }

        return new ValidateResultado($puntoAtencion, $errors);
    }

    /**
     * Validar requisitos
     *
     * @param array $params array con datos a validar
     * @return ValidateResultado
     */
    private function validarRequisitos($params)
    {
        return $this->validar($params, [
            'tramiteId' => 'required:integer',
            'provincia' => 'required:integer',
            'localidad' => 'required:integer',
            'puntoAtencionId' => 'required:integer'
        ]);
    }

    /**
     * Validar si existe
     *
     * @param array $params array con datos a validar
     * @return array
     */
    private function checkExist($params)
    {
        $errors = [];

        $tramite = $this->tramiteRepository->find($params['tramiteId']);

        if (! $tramite) {
            $errors[] = 'Tramite inexistente';
        }

        $provincia = $this->provinciaRepository->find($params['provincia']);

        if (! $provincia) {
            $errors[] = 'Provincia inexistente';
        }

        $localidad = $this->localidadRepository->find($params['localidad']);
        if (! $localidad) {
            $errors[] = 'Localidad inexistente';
        }

        return $errors;
    }

    /**
     * Validar parametros para crear la disponiblidad de turnos de un punto de atención
     *
     * @param array $params array con datos a validar
     * @return ValidateResultado
     */
    private function validarParamsDisponibilidad($params)
    {
        $gruposTramites = new ArrayCollection();

        $errors = $this->validar($params, [
            'grupoTramite' => 'required:integer',
            'puntoAtencion' => 'required:integer',
            'cantidadTurno' => 'integer'
        ]);

        if (! count($errors)) {
            if (isset($params['puntoAtencion'])) {
                $puntoAtencion = $this->puntoAtencionRepository->find($params['puntoAtencion']);

                if (! $puntoAtencion) {
                    $errors[] = 'Punto de Atención inexistente';
                }

                $gruposTramites = $puntoAtencion->getGrupoTramites();
            }

            if (isset($params['grupoTramite'])) {
                $grupoTramite = $this->grupoTramiteRepository->find($params['grupoTramite']);

                if (! $grupoTramite) {
                    $errors[] = 'Grupo Tramite inexistente';
                }

                if (! $gruposTramites->contains($grupoTramite)) {
                    $errors[] = 'El Grupo Tramite no pertenece al Punto de Atencion';
                }
            }
        }

        return new ValidateResultado(null, $errors);
    }

    /**
     * Valida que no existe un punto de atención con ese horario y ese grupo de atención
     *
     * @param array $params array con datos a validar
     * @return ValidateResultado
     */
    private function existDisponiblidad($params)
    {
        $errors = [];

        if (isset($params['puntoAtencion']) && isset($params['rangoHorario']) && isset($params['grupoTramite'])) {
            $disponiblidad = $this->disponibilidadRepository->findBy([
                'puntoAtencion' => $params['puntoAtencion'],
                'horarioAtencion' => $params['rangoHorario'],
                'grupoTramite' => $params['grupoTramite']
            ]);

            if ($disponiblidad) {
                $errors[] = 'Ya existe disponibilidad para eso punto de atención, grupo de tramite y rango horario';
            }
        }

        return new ValidateResultado(null, $errors);
    }

    /**
     * Validar parameters para crear una disponibilidad
     *
     * @param array $params array con datos a validar
     * @return ValidateResultado
     */
    public function validarParamsCreate($params)
    {
        $validateResultado = $this->validarParamsDisponibilidad($params);

        if (! $validateResultado->hasError()) {
            $validateResultado = $this->existDisponiblidad($params);
        }

        return $validateResultado;
    }

    /**
     * Validar parameters para editar una disponibilidad
     *
     * @param array $params array con datos a validar
     * @return ValidateResultado
     */
    public function validarParamsEdit($idRow, $params)
    {
        $validateResultado =  $this->validarParamsDisponibilidad($params);

        return $validateResultado;
    }

    /**
     * Validar relación grupo trámite - punto de atención
     *
     * @param object $puntoAtencion objeto punbto de atención
     * @param  object $grupoTramite objeto grupo trámite
     * @return ValidateResultado
     */

    public function validarParamsGet($puntoAtencion, $grupoTramite)
    {
        $errors = [];

        if (! $puntoAtencion) {
            $errors[] = 'El punto de atención no existe';

            return new ValidateResultado(null, $errors);
        }

        if (! $grupoTramite) {
            $errors[] = 'El grupo del tramite no existe';
            return new ValidateResultado(null, $errors);
        }

        $gruposTramites = $puntoAtencion->getGrupoTramites();

        if (! $gruposTramites->contains($grupoTramite)) {
            $errors[] = 'El Grupo Tramite no pertenece al Punto de Atencion';

            return new ValidateResultado(null, $errors);
        }

        return new ValidateResultado($puntoAtencion, []);
    }
    
    
     /**
     * Valida un punto de atención por fecha
     *
     * @param array $params array con datos para validar un punto de atención por fecha
     * @return ValidateResultado
     */
    public function validarParamsDisponibilidadTramites($params)
    {
        
        $errors = $this->validar($params, [
            'tramiteId' => 'required:integer',
            'fecha' => 'required:date',
            'horizonte' => 'required:horizonte'
        ]);
        
        if (count($errors) > 0) {
            return new ValidateResultado(null, $errors);
        }
        
        if ($params['limit'] > 10000) {
                $errors[] = 'Limite excedido';
        }

        
        if (isset($params['tramiteId'])) {
            $tramite = $this->tramiteRepository->find($params['tramiteId']);
            if (! $tramite) {
                $errors[] = 'Trámite inexistente';
            }
        }

        return new ValidateResultado(null, $errors);
    }
}
