<?php
namespace ApiV1Bundle\Entity\Validator;

use ApiV1Bundle\Entity\PuntoAtencion;
use ApiV1Bundle\Entity\User;
use ApiV1Bundle\Entity\Usuario;
use ApiV1Bundle\Entity\ValidateResultado;
use ApiV1Bundle\Helper\ServicesHelper;
use ApiV1Bundle\Repository\AreaRepository;
use ApiV1Bundle\Repository\DiaNoLaborableRepository;
use ApiV1Bundle\Repository\LocalidadRepository;
use ApiV1Bundle\Repository\ProvinciaRepository;
use ApiV1Bundle\Repository\PuntoAtencionRepository;
use ApiV1Bundle\Repository\TramiteRepository;
use ApiV1Bundle\Repository\TurnoRepository;
use ApiV1Bundle\Entity\Turno;

/**
 * Class PuntoAtencionValidator
 * @package ApiV1Bundle\Entity\Validator
 */
class PuntoAtencionValidator extends SNTValidator
{
    /** @var TramiteRepository  */
    private $tramiteRepository;
    /** @var ProvinciaRepository  */
    private $provinciaRepository;
    /** @var LocalidadRepository  */
    private $localidadRepository;
    /** @var AreaRepository  */
    private $areaRepository;
    /** @var PuntoAtencionRepository  */
    private $puntoAtencionRepository;
    /** @var TurnoRepository  */
    private $turnoRepository;
    /** @var DiaNoLaborableRepository  */
    private $diaNoLaborableRepository;

    /**
     * PuntoAtencionValidator constructor.
     * @param TramiteRepository $tramiteRepository
     * @param ProvinciaRepository $provinciaRepository
     * @param LocalidadRepository $localidadRepository
     * @param AreaRepository $areaRepository
     * @param PuntoAtencionRepository $puntoAtencionRepository
     * @param TurnoRepository $turnoRepository
     * @param DiaNoLaborableRepository $diaNoLaborableRepository
     */
    public function __construct(
        TramiteRepository $tramiteRepository,
        ProvinciaRepository $provinciaRepository,
        LocalidadRepository $localidadRepository,
        AreaRepository $areaRepository,
        PuntoAtencionRepository $puntoAtencionRepository,
        TurnoRepository $turnoRepository,
        DiaNoLaborableRepository $diaNoLaborableRepository
    ) {
        $this->tramiteRepository = $tramiteRepository;
        $this->provinciaRepository = $provinciaRepository;
        $this->localidadRepository = $localidadRepository;
        $this->areaRepository = $areaRepository;
        $this->puntoAtencionRepository = $puntoAtencionRepository;
        $this->turnoRepository = $turnoRepository;
        $this->diaNoLaborableRepository = $diaNoLaborableRepository;
    }

    /**
     * Validar parámetros
     *
     * @param array $params arreglo con los datos a validar
     * @return mixed
     */
    private function validarParams($params)
    {
        return $this->validar($params, [
            'provincia' => 'required',
            'localidad' => 'required',
            'area' => ' required',
            'nombre' => 'required',
            'direccion' => 'required',
            'latitud' => 'float',
            'longitud' => 'float',
            'tramites' => 'matriz',
            'estado' => 'integer'
        ]);
    }

    /**
     * Valida la creación de un punto de atención
     *
     * @param array $params arreglo con los datos a validar
     * @return mixed
     */
    public function validarCreate($params)
    {
        $errors = $this->validarParams($params);

        $provincia = (isset($params['provincia']))
            ? $this->provinciaRepository->find($params['provincia'])
            : null;
        $localidad = (isset($params['localidad']))
            ? $this->localidadRepository->find($params['localidad'])
            : null;
        $area = (isset($params['area']))
            ? $this->areaRepository->find($params['area'])
            : null;

        if (!$provincia) {
            $errors[] = 'Provincia inexistente';
        }

        if (!$localidad) {
            $errors[] = 'Localidad inexistente';
        }

        if (!$area) {
            $errors[] = 'Area inexistente';
        }

        return new ValidateResultado(null, $errors);
    }

    /**
     * Valida la edición de un punto de atención
     *
     * @param object $puntoAtencion objeto punto de atención
     * @param array $params arreglo con los datos a validar
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function validarEditar($puntoAtencion, $params)
    {
        $errors = $this->validarParams($params);

        $provincia = (isset($params['provincia']))
            ? $this->provinciaRepository->find($params['provincia'])
            : null;
        $localidad = (isset($params['localidad']))
            ? $this->localidadRepository->find($params['localidad'])
            : null;
        $area = (isset($params['area']))
            ? $this->areaRepository->find($params['area'])
            : null;

        if (!count($errors)) {
            if (!$provincia) {
                $errors[] = 'Provincia inexistente';
            }

            if (!$localidad) {
                $errors[] = 'Localidad inexistente';
            }

            if (!$area) {
                $errors[] = 'Area inexistente';
            }

            if (!$puntoAtencion) {
                $errors[] = 'Punto de atención inexistente';
            }

            if ($puntoAtencion && isset($params['tramites'])) {
                foreach ($puntoAtencion->getTramites() as $tramite) {
                    // si no está en el array de tramites, es porque se tiene que eliminar
                    if (!in_array($tramite->getId(), $params['tramites'])) {
                        // valido si el trámite tiene turnos asociados
                        $ultimoTurno = $this->turnoRepository->findUltimoTurno(
                            $tramite->getId(),
                            $puntoAtencion->getId()
                        );
                        if ($ultimoTurno) {
                            $errors[] = "El tramite {$tramite->getNombre()} tiene turnos asociados. Últio turno el {$ultimoTurno['fecha']->format('Y-m-d')}.";
                        }
                    }
                }
            }
        }
        return new ValidateResultado(null, $errors);
    }

    /**
     * Valida el borrado de un punto de atención
     *
     * @param object $puntoAtencion objeto punto de atención
     * @return mixed
     */
    public function validarDelete($puntoAtencion)
    {
        $errors = [];

        if (! $puntoAtencion) {
            $errors[] = 'Punto de Atención inexistente';
            return new ValidateResultado(null, $errors);
        }

        if ($puntoAtencion->getTramites()->count() > 0) {
            $errors[] = 'Existen tramites asociados al Punto de atencion';
        }

        if ($puntoAtencion->getGrupoTramites()->count() > 0) {
            $errors[] = 'Existen Grupos de Tramites asociados al Punto de atencion';
        }

        return new ValidateResultado(null, $errors);
    }

    /**
     * Vaidar tramites
     *
     * @param integer $id identificador único del punto de atención
     * @param array $params arreglo con los datos a validar
     * @return mixed
     */
    public function validarTramites($id, $params)
    {
        $errors = $this->validar($params, [
            'tramites' => 'required:matriz'
        ]);

        if (! count($errors)) {
            $puntoAtencion = $this->puntoAtencionRepository->find($id);

            if (! $puntoAtencion) {
                $errors[] = 'Punto de Atención inexistente';
                return new ValidateResultado(null, $errors);
            }

            foreach (ServicesHelper::toArray($params['tramites']) as $tramiteId) {
                $tramite = $this->tramiteRepository->find($tramiteId);

                if (is_null($tramite)) {
                    $errors[] = "Tramite con ID {$tramiteId} inexistente";
                }
            }

            if (count($errors)) {
                return new ValidateResultado(null, $errors);
            }

            return new ValidateResultado($puntoAtencion, []);
        }
        return new ValidateResultado(null, $errors);
    }

    /**
     * Validar busqueda
     *
     * @param array $params arreglo con los datos a validar
     * @return mixed
     */
    public function validarSearch($params)
    {
        $errors = $this->validar($params, [
            'q' => 'required'
        ]);
        return new ValidateResultado(null, $errors);
    }

    /**
     * Validar si existe el punto de atención
     *

     * @return mixed
     */

    public function verificaPuntoAtencion($puntoAtencion)
    {
        $errors = [];

        if ( ! $puntoAtencion) {
            $errors[] = 'Punto de atención inexiste';
            return new ValidateResultado(null, $errors);
        }

        return new ValidateResultado($puntoAtencion, []);
    }

    /**
     * Validamos la fecha
     *
     * @param array $params arreglo con los datos a validar
     * @return mixed
     */
    public function validarFecha($params)
    {
        $errors = $this->validar($params, [
            'fecha' => 'required:date'
        ]);

        return new ValidateResultado(null, $errors);
    }

    /**
     * Validar nuevo día no hábil
     *
     * @param array $params arreglo con los datos a validar
     * @param integer $puntoAtencion identificador único de punto de atención
     * @return mixed
     */
    public function validarNuevoDiaNoHabil($params, $puntoAtencion)
    {
        $errors = [];
        $validateResultado = $this->validarFecha($params);

        if (! $validateResultado->hasError()) {
            $fecha = new \DateTime($params['fecha']);
            $diaNoLaborable = $this->diaNoLaborableRepository->findOneBy([
                'puntoAtencion' => $puntoAtencion,
                'fecha' => $fecha
            ]);

            if ($diaNoLaborable) {
                $errors[] = 'Ya existe un día no laborable con esa fecha';
                return new ValidateResultado(null, $errors);
            }

            $turno = $this->turnoRepository->findOneBy(['puntoAtencion' => $puntoAtencion, 'fecha' => $fecha ,'estado'=> Turno::ESTADO_ASIGNADO]);
            

            if ($turno) {
                $errors[] = 'La fecha seleccionada posee turnos asignados';
                return new ValidateResultado(null, $errors);
            }
        }

        return $validateResultado;
    }

    /**
     * Validar integración (pda)
     *
     * @param array $params arreglo con los datos a validar
     * @return mixed
     */
    public function validarIntegracionGetDatos($params) {
        $errors = $this->validar($params, [
            'puntoatencion' => 'required:integer'
        ]);
        return new ValidateResultado(null, $errors);
    }

    /**
     * validar asignación de visibilidad
     *
     * @param object $puntoAtencion objeto punto de atención
     * @param object $tramite objeto trámite
     * @param array $params arreglo con los datos a validar
     * @return ValidateResultado
     */
    public function validarSetVisibilidad($puntoAtencion, $tramite, $params)
    {
        $errors = $this->validar($params, [
            'estado' => 'integer:required'
        ]);

        if (!$puntoAtencion) {
            $errors[] = "Punto atención inexistente";
        }

        if (!$tramite) {
            $errors[] = "Trámite inexistente";
        }

        return new ValidateResultado(null, $errors);
    }

    /**
     * @param Usuario $usuario
     * @param PuntoAtencion $puntoAtencion
     * @return ValidateResultado
     */
    public function validarPuntoAtencionUsuario($usuario, $puntoAtencion)
    {
        if ($usuario->getUser()->getRol() != User::ROL_PUNTOATENCION) {
            $errors = ["Debe ser responsable de punto de atención"];
            return new ValidateResultado(null, $errors);
        }

        if ($usuario->getPuntoAtencionId() != $puntoAtencion->getId()) {
            $errors = ["Usted no es responsable de este punto de atención"];
            return new ValidateResultado(null, $errors);
        }

        return new ValidateResultado($puntoAtencion, []);
    }

    /**
     * Verifica que un día a ser inhabilitado para un punto de atención no esté inhabilitado
     *
     * @param $puntoAtencion
     * @param $params
     * @return ValidateResultado
     */
    public function validarInhabilitarDia($puntoAtencion, $params)
    {
        $validateResultado = $this->validarFecha($params);

        if (!$validateResultado->hasError()) {
            $fecha = new \DateTime($params['fecha']);
            $diaNoLaborable = $this->diaNoLaborableRepository->findOneBy([
                'puntoAtencion' => $puntoAtencion,
                'fecha' => $fecha
            ]);

            if ($diaNoLaborable) {
                $errors = ['Ya existe un día no laborable con esa fecha'];
                return new ValidateResultado(null, $errors);
            }
        }

        return $validateResultado;
    }
    /**
     * validar multiples
     *
     * @param object $puntoAtencion objeto punto de atención
     * @param object $tramite objeto trámite
     * @param array $params arreglo con los datos a validar
     * @return ValidateResultado
     */
    public function validarEditarPuntoTramite($puntoAtencion, $tramite, $params)
    {
        $errors = $this->validar($params, [
            'multiple' => 'integer:required',
            'multiple_horizonte' => 'integer:required',
            'multiple_max' => 'integer:required',
            'permite_otro' => 'integer:required',
            'permite_otro_cantidad' => 'integer:required'
        ]);

        if (!$puntoAtencion) {
            $errors[] = "Punto atención inexistente";
        }

        if (!$tramite) {
            $errors[] = "Trámite inexistente";
        }

        return new ValidateResultado(null, $errors);
    }
}
