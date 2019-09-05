<?php
namespace ApiV1Bundle\Entity\Validator;

use ApiV1Bundle\ApplicationServices\DisponibilidadServices;
use ApiV1Bundle\Entity\PuntoAtencion;
use ApiV1Bundle\Entity\ValidateResultado;
use ApiV1Bundle\Repository\PuntoAtencionRepository;
use ApiV1Bundle\Repository\TramiteRepository;
use ApiV1Bundle\Repository\TurnoRepository;
use ApiV1Bundle\Repository\PuntoTramiteRepository;
use ApiV1Bundle\Repository\DiaNoLaborableRepository;
use ApiV1Bundle\Helper\ServicesHelper;

/**
 * Class TurnoValidator
 * @package ApiV1Bundle\Entity\Validator
 */
class TurnoValidator extends SNTValidator
{
    /** @var TurnoRepository  */
    private $turnoRepository;
    /** @var TramiteRepository  */
    private $tramiteRepository;
    /** @var PuntoAtencionRepository  */
    private $puntoAtencionRepository;
    /** @var DiaNoLaborableRepository  */
    private $diaNoLaborableRepository;
    /** @var DisponibilidadServices  */
    private $disponibilidadServices;
    /** @var PuntoTramiteRepository  */
    private $puntoTramiteRepository;

    /**
     * TurnoValidator constructor.
     * @param TurnoRepository $turnoRepository
     * @param TramiteRepository $tramiteRepository
     * @param PuntoAtencionRepository $puntoAtencionRepository
     * @param DiaNoLaborableRepository $diaNoLaborableRepository
     * @param DisponibilidadServices $disponibilidadServices
     */
    public function __construct(
        TurnoRepository $turnoRepository,
        TramiteRepository $tramiteRepository,
        PuntoAtencionRepository $puntoAtencionRepository,
        DiaNoLaborableRepository $diaNoLaborableRepository,
        DisponibilidadServices $disponibilidadServices,
        PuntoTramiteRepository $puntoTramiteRepository
    ) {
        $this->turnoRepository = $turnoRepository;
        $this->tramiteRepository = $tramiteRepository;
        $this->puntoAtencionRepository = $puntoAtencionRepository;
        $this->diaNoLaborableRepository = $diaNoLaborableRepository;
        $this->disponibilidadServices = $disponibilidadServices;
        $this->puntoTramiteRepository = $puntoTramiteRepository;
    }

    /**
     * Vaidar datos del turno
     *
     * @param object $turno objeto turno
     * @param array $params array con datos a validar
     * @return ValidateResultado
     */
    public function validaTurnoConfirma($turno, $params)
    {
        $errors = [];


       if( isset($params['cuilSolicitante']) && $params['cuilSolicitante'] != null) {
            $errors[] =$this->validar($params , [
                'cuilSolicitante' => 'required:cuil']);
            if(count( $errors) > 0) {
                return new ValidateResultado(null, $errors);
            }
        }


        if (! $turno) {
            $errors[] = 'Turno no encontrado';
            return new ValidateResultado(null, $errors);
        }

        if (! isset($params['campos'])) {
            $errors[] = 'No hay campos para validar';
            return new ValidateResultado(null, $errors);
        }

        $cuilValidation = ($turno->getTramite()->getExcepcional()) ? '' : ':cuil';

        $camposForm = ServicesHelper::toArray($params['campos']);
        $errors = $this->validar($camposForm, [
            'nombre' => 'required:letters',
            'apellido' => 'required:letters',
            'cuil'  => 'required' . $cuilValidation,
            'email' => 'required:email'
        ]);

        // verificamos que el ciudadano no tenga un turno ya dada para ese tramite y punto de atención
        if (! count($errors)) {
            $verificacionTurno = $this->turnoRepository->findTurnosByPuntoTramiteCuil(
                $turno->getPuntoAtencion()->getId(),
                $turno->getTramite()->getId(),
                $turno->getFecha(),
                ServicesHelper::buildValidDocument($params['campos']['cuil'])
            );
            
            if(count($verificacionTurno)){ 
            //tiene turnos duplicados
                if(!$this->validarTurnosPorCuil($params, $turno) ){
                    $errors[] = 'Ya existe un turno para el ciudadano para esa fecha y tramite';
                }
               
            }
            
        }

        return new ValidateResultado(null, $errors);
    }

    /**
     * Validar datos de la busqueda
     *
     * @param array $params array con datos a validar
     * @return ValidateResultado
     */
    public function validarSearch($params)
    {
        $errors = $this->validar($params, [
            'cuil' => 'required',
            'codigo' => 'required'
        ]);

        if (! count($errors)) {
            $turno = $this->turnoRepository->search(
                ServicesHelper::buildValidDocument($params['cuil']),
                $params['codigo']
            );
            if (! $turno) {
                $errors[] = 'Turno no encontrado.';
                return new ValidateResultado(null, $errors);
            }
            return new ValidateResultado($turno, []);
        }
        return new ValidateResultado(null, $errors);
    }

    /**
     * Validar datos de la busqueda por código
     *
     * @param array $params array con datos a validar (código y punto de atención)
     * @return ValidateResultado
     */
    public function validarSearchByCodigo($params)
    {
        $errors = $this->validar($params, [
            'codigo' => 'required',
            'puntoatencionid' => 'required:integer'
        ]);

        return new ValidateResultado(null, $errors);
    }

    /**
     * Validar creación de turno
     *
     * @param array $params array con datos a validar (puntoatención, trámite, fecha, hora)
     * @return ValidateResultado
     */
    public function validarCreate($params)
    {
        $errors = $this->validar($params, [
            'puntoatencion' => 'required',
            'tramite' => 'required',
            'fecha' => 'required:date',
            'hora' => 'required:timeHis'
        ]);

        if (! count($errors)) {
            $puntoAtencion = $this->puntoAtencionRepository->find($params['puntoatencion']);
            if (! $puntoAtencion) {
                $errors[] = 'Punto de atención inexistente';
            }

            $tramite = $this->tramiteRepository->find($params['tramite']);
            if (! $tramite) {
                $errors[] = 'Tramite inexistente';
            }

            $fecha = new \DateTime($params['fecha']);
            $isDiaNoLaboral = $this->diaNoLaborableRepository->findBy([
                'fecha' => $fecha,
                'puntoAtencion' => $params['puntoatencion']
            ]);
            if (! empty($isDiaNoLaboral)) {
                $errors[] = 'La fecha seleccionada no es un día hábil.';
            }

            $grupoTramite = null;
            if ($puntoAtencion && $tramite) {
                $grupoTramite = $tramite->getGrupoTramiteByPunto($puntoAtencion);
                if (! $grupoTramite) {
                    $errors[] = 'El tramite no esta asociado a un grupo de tramites del punto de atención.';
                }
            }

            if ($puntoAtencion && $grupoTramite) {
                if (! $this->disponibilidadServices->hasTurnoDisponible(
                    $grupoTramite->getId(),
                    $puntoAtencion->getId(),
                    $params['fecha'],
                    $params['hora']
                )) {
                    $errors[] = 'No hay disponibilidad para la fecha y hora seleccionada.';
                    return new ValidateResultado(null, $errors);
                }
                if(!$this->ValidarDeshabilitarHoy($puntoAtencion, $tramite, $params['fecha'])){
                    $errors[] = 'No hay disponibilidad para la fecha y hora seleccionada.';
                    return new ValidateResultado(null, $errors);
                }
            }
            
            if (! count($errors)) {
                return new ValidateResultado($puntoAtencion, []);
            }
        }
        return new ValidateResultado(null, $errors);
    }

    /**
     * Validar tramite, punto de atencion y estado
     *
     * @param array $params Array conteniendo el estado de un trámite
     * @param object $tramite objeto Tramite
     * @param object $puntoAtencion Este campo puede ser null
     * @return ValidateResultado
     */
    public function validarTramitePuntoAtencion($params, $tramite, $puntoAtencion = null)
    {
        $errors = [];

        if (! $puntoAtencion) {
            $errors[] = 'Punto de Atención Inexistente';
        }

        if (! $tramite) {
            $errors[] = 'Tramite inexistente';
        }

        $errors = $this->validar($params, [
            'estado' => 'required:integer'
        ]);

        if ($params['estado'] != 1) {
            $errors[] = 'El valor para este campo debe ser ASIGNADO';
        }

        return new ValidateResultado(null, $errors);
    }

    /**
     * Validar datos para el último turno
     *
     * @param integer $tramiteId identificador único del trámite
     * @param object $tramite objeto Tramite
     * @param integer $puntoAtencionId identificador único del punto de Atencion
     * @param PuntoAtencion $puntoAtencion
     * @param integer $grupoTramitesId identificador único del grupoTramites
     * @param object $grupoTramites objeto grupoTramites
     * @return ValidateResultado
     */
    public function validarUltimoTurno(
        $tramiteId,
        $tramite,
        $puntoAtencionId,
        $puntoAtencion,
        $grupoTramitesId,
        $grupoTramites
    ) {
        $errors = [];

        if (! is_null($puntoAtencionId) && ! $puntoAtencion) {
            $errors[] = 'Punto de Atención inexistente';
        }

        if (! is_null($grupoTramitesId) && ! $grupoTramites) {
            $errors[] = 'Grupo de tramites inexistente';
        }

        if (! is_null($tramiteId) && ! $tramite) {
            $errors[] = 'Tramite inexistente';
        }

        return new ValidateResultado(null, $errors);
    }

    /**
     * Validamos datos para buscar turno por fecha y punto de atencion
     *
     * @param type $puntoAtencionId Id del punto de atencion
     * @param type $fecha fecha de los turnos requeridos
     * @return ValidateResultado
     */
    public function validarBuscarTurno($puntoAtencionId, $fecha)
    {
        $errors = [];
        if (is_null($puntoAtencionId)) {
            $errors[] = 'Punto de Atención inexistente';
        }

        $dateTimeTurnos = new \DateTime($fecha);
        $dateTimeHoy = new \DateTime('now');
        $intervalo = $dateTimeHoy->diff($dateTimeTurnos);
        $menorAHoy = (int)$intervalo->format('%R%a');

        if ($menorAHoy < 0) {
            $errors[] = 'Fecha menor al día de hoy';
        }

        if (is_null($fecha)) {
            $errors[] = 'Fecha Inexistente';
        }

        return new ValidateResultado(null, $errors);
    }

    /**
     * Vaidar turno por ciudadano
     *
     * @param array $params array con datos a validar (cuil)
     * @return ValidateResultado
     */
    public function validarFindAllCiudadanos($params)
    {
        $errors = $this->validar($params, [
            'cuil' => 'required'
        ]);

        return new ValidateResultado(null, $errors);
    }

    /**
     * validar busqueda de ciudadanos por dni
     *
     * @param array $params array con datos a validar (dni)
     * @return ValidateResultado
     */
    public function validarFindAllCiudadanosDni($params)
    {
        $errors = $this->validar($params, [
            'dni' => 'required'
        ]);

        return new ValidateResultado(null, $errors);
    }

    /**
     * Validar fecha y punto de atención
     *
     * @param array $params array con datos a validar (fecha, punto de atención y motivo)
     * @return ValidateResultado
     */
    public function validarCancelByDate($params)
    {
        $errors = $this->validar($params, [
            'fecha' => 'required:dateTZ',
            'puntoatencion' => 'required:integer',
            'motivo' => 'required'
        ]);

        if (! count($errors)) {
            $fecha = new \DateTime($params['fecha']);
            // punto de atención
            $puntoAtencion = $this->puntoAtencionRepository->find($params['puntoatencion']);
            if (! $puntoAtencion) {
                $errors[] = 'Punto de Atención inexistente';
                return new ValidateResultado(null, $errors);
            }
            // dia no laborable
            $diaNoLaborable = $this->diaNoLaborableRepository->findOneBy([
                'puntoAtencion' => $puntoAtencion,
                'fecha' => $fecha
            ]);
            if ($diaNoLaborable) {
                $errors[] = 'Ya existe un día no laborable con esa fecha';
                return new ValidateResultado(null, $errors);
            }
        }

        return new ValidateResultado(null, $errors);
    }

    /**
     * Validar punto de atención y fecha
     * @param array $params arreglo con los datos a validar
     * @param integer $puntoAtencionId identificador único del punto de atención
     * @param integer $grupoTramiteId identificador único del grupo de trámites
     * @return \ApiV1Bundle\Entity\ValidateResultado
     */
    public function validateFechaReasignacion($params, $puntoAtencionId, $grupoTramiteId = null)
    {
        $errors = $this->validar($params, [
            'fecha' => 'required:date'
        ]);
        if (! count($errors)) {
            // punto de atención
            $puntoAtencion = $this->puntoAtencionRepository->find($puntoAtencionId);
            if (! $puntoAtencion) {
                $errors[] = 'Punto de Atención inexistente';
            }

            $hoy = new \DateTime('today');
            $fecha = new \DateTime($params['fecha']);
            $diff = (int)$hoy->diff($fecha)->format('%R%a');
            if ($diff < 0) {
                $errors[] = "La fecha debe ser posterior o igual a hoy";
            }

            // grupo tramite
            if ($puntoAtencion && $grupoTramiteId) {
                $grupoTramites = $puntoAtencion->getGrupoTramites();
                $activeGroup = $grupoTramites->filter(function ($grupo) use ($grupoTramiteId) {
                    if ($grupo->getId() == $grupoTramiteId) {
                        return $grupo->getId();
                    }
                });
                // check active group
                if (! count($activeGroup)) {
                    $errors[] = 'El grupo no pertenece al punto de atención';
                }
            }
        }
        return new ValidateResultado(null, $errors);
    }

    /**
     * Validamos la reasignación de turnos
     * @param array $params arreglo con los datos a validar
     * @param integer $puntoAtencionId identificador único del punto de atención
     * @return \ApiV1Bundle\Entity\ValidateResultado
     */
    public function validateReasignacion($params, $puntoAtencionId)
    {
        $listadoTurnos = [];
        $errors = $this->validar($params, [
            'fecha' => 'required:date',
            'grupoTramites' => 'required:matriz'
        ]);
        if (! count($errors)) {
            // punto de atención
            $puntoAtencion = $this->puntoAtencionRepository->find($puntoAtencionId);
            if (! $puntoAtencion) {
                $errors[] = 'Punto de Atención inexistente';
            }
            // validamos la fecha
            $dateTimeReasignar = new \DateTime($params['fecha']);

            // validamos que la fecha sea mayor o igual a hoy
            $hoy = new \DateTime('today');
            $diff = (int)$hoy->diff($dateTimeReasignar)->format('%R%a');
            if ($diff < 0) {
                $errors[] = "La fecha debe ser posterior o igual a hoy";
            }

            foreach ($params['grupoTramites'] as $grupoTramitesId => $grupoFechas) {
                $listadoTurnos[$grupoTramitesId] = [
                    'turnos' => [],
                    'fechas' => []
                ];
                // validar las fechas para reasignación
                foreach ($grupoFechas as $fecha => $cantidad) {
                    $dateTurno = new \DateTime($fecha);
                    $intervalo = $dateTimeReasignar->diff($dateTurno);
                    $menorAHoy = (int) $intervalo->format('%R%a');
                    if ($menorAHoy < 0) {
                        $errors[] = 'Fecha menor al día a reasignar';
                    }
                    $listadoTurnos[$grupoTramitesId]['fechas'][$fecha] = $cantidad;
                }
                // validamos los turnos
                $turnos = $this->turnoRepository->findTurnosAReasignar(
                    $puntoAtencionId,
                    $grupoTramitesId,
                    $dateTimeReasignar
                );
                // todos los turnos deben ser reasignados
                $totalTurnos = count($turnos);
                $totalTurnosReasignar = array_sum($grupoFechas);
                if ($totalTurnos == $totalTurnosReasignar) {
                    // ordenamos los turnos de forma aleatoria
                    shuffle($turnos);
                    // asignamos los turnos al grupo de trámites
                    $listadoTurnos[$grupoTramitesId]['turnos'] = $turnos;
                } else {
                    $errors[] = 'La cantidad de turnos que se quiere reasignar no coincide con la cantidad de turnos en total a reasignar';
                }
            }
        }
        return new ValidateResultado($listadoTurnos, $errors);
    }

    /**
     * Validar fecha y hora del cambio
     * @param array $params arreglo con los datos a validar
     * @return \ApiV1Bundle\Entity\ValidateResultado
     */
    public function validateFechaYHora($params)
    {
        if (isset($params['codigo']) && strlen($params['codigo']) !== 8){
            $errors['errors'] = "El código debe tener exactamente 8 caracteres.";
            return new ValidateResultado(null, $errors['errors']);
        }

        $errors = $this->validar($params, [
            'codigo' => 'required',
            'fecha' => 'date',
            'hora' => 'timeTryCatch',
            'punto_atencion_id' => 'integer'
        ]);

        $turno = $this->turnoRepository->searchOneByCodigo($params['codigo']);
        
        if($turno && $turno->getGrupoTramite()){
            $grupoTramiteId = $turno->getGrupoTramite()->getId();
            $puntoTramite = $this->disponibilidadServices->existeTramitePuntoAtencion($turno->getTramite()->getId(), $params['punto_atencion_id']);
            if (!$puntoTramite){
                $errors[] = "El punto de atención no es válido para el trámite del turno.";
            }

            $disp = $this->disponibilidadServices->hasTurnoDisponible($grupoTramiteId, $params['punto_atencion_id'], $params['fecha'], $params['hora'].':00');
            if (!$disp){
                $errors[] = "No hay turno disponible para la fecha y hora seleccionada.";
            }
        }else{
             $errors[] = "No existe el turnos.";
        }     
        if (!$errors) {
            return new ValidateResultado($params, []);
        }

        return new ValidateResultado(null, $errors);
    }
    
    /**
     * 
     * @param type $params
     * @param type $turno
     * @return boolean
     */
    private function validarTurnosPorCuil($params, $turno) {
        $puntoTramite = $this->puntoTramiteRepository->findOneBy([
            'puntoAtencion' => $turno->getPuntoAtencion(),
            'tramite' => $turno->getTramite() 
        ]);

        if (!$puntoTramite) {
            return false;
        }
        // verificar si el tramite puede tener tramites duplicados para el mismo cuil
        $tramite_horizonte = $this->tramiteRepository->findHorizonte($turno->getTramite()->getId());
        $puede_multiple = $this->turnoRepository->verificarMultipleTramite(
                $puntoTramite, $turno, $tramite_horizonte, ServicesHelper::buildValidDocument($params['campos']['cuil'])
        );
        
        // verificar fecha horizonte
        if ($puede_multiple) {
            return true;
        }
        // verificar si el tramite permite sacar turnos para otra persona
        $puede_otrapersona = $this->turnoRepository->verificarPermiteOtro(
                $puntoTramite, $turno, ServicesHelper::buildValidDocument($params['campos']['cuil'])
        );
        if ($puede_otrapersona) {
            return true;
        }
        return false;
    }
    
    function ValidarDeshabilitarHoy($puntoAtencion, $tramite, $fecha) {

                $puntoTramite = $this->puntoTramiteRepository->findOneBy([
                    'puntoAtencion' => $puntoAtencion,
                    'tramite' => $tramite
                ]);
                if($puntoTramite && $puntoTramite->getDeshabilitarHoy() && $fecha == date("Y-m-d")){
                    return false;
                }
                return true;
    }

    
     /**
     * Validar creación de turno
     *
     * @param array $params array con datos a validar (puntoatención, trámite, fecha, horas)
     * @return ValidateResultado
     */
    public function validarCreateMultiple($params)
    {
        $errors = $this->validar($params, [
            'puntoatencion' => 'required',
            'tramite' => 'required',
            'fecha' => 'required:date',
            'horas' => 'required:matriz'
        ]);

        if (! count($errors)) {
            $puntoAtencion = $this->puntoAtencionRepository->find($params['puntoatencion']);
            if (! $puntoAtencion) {
                $errors[] = 'Punto de atención inexistente';
            }

            $tramite = $this->tramiteRepository->find($params['tramite']);
            if (! $tramite) {
                $errors[] = 'Tramite inexistente';
            }

            $fecha = new \DateTime($params['fecha']);
            $isDiaNoLaboral = $this->diaNoLaborableRepository->findBy([
                'fecha' => $fecha,
                'puntoAtencion' => $params['puntoatencion']
            ]);
            if (! empty($isDiaNoLaboral)) {
                $errors[] = 'La fecha seleccionada no es un día hábil.';
            }

            $grupoTramite = null;
            if ($puntoAtencion && $tramite) {
                $grupoTramite = $tramite->getGrupoTramiteByPunto($puntoAtencion);
                if (! $grupoTramite) {
                    $errors[] = 'El tramite no esta asociado a un grupo de tramites del punto de atención.';
                }
            }
            if (! count($errors)) {
                return new ValidateResultado($puntoAtencion, []);
            }
        }
        return new ValidateResultado(null, $errors);
    }
    
    
    /**
     * Vaidar datos del turno
     *
     * @param object $turno objeto turno
     * @param array $params array con datos a validar
     * @return ValidateResultado
     */
    public function validaTurnoConfirmaMultiple($params)
    {
        $errors = $this->validar($params, [
            'turnos' => 'required:matriz',
            'cuilSolicitante'  => 'required:cuil',
            'email' => 'required:email'
        ]);
        return new ValidateResultado(null, $errors);
    }
}
