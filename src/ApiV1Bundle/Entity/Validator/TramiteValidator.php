<?php
namespace ApiV1Bundle\Entity\Validator;

use ApiV1Bundle\Entity\ValidateResultado;
use ApiV1Bundle\Repository\AreaRepository;
use ApiV1Bundle\Repository\TramiteRepository;

/**
 * Class TramiteValidator
 * @package ApiV1Bundle\Entity\Validator
 */

class TramiteValidator extends SNTValidator
{
    /** @var AreaRepository  */
    private $areaRepository;
    /** @var TramiteRepository  */
    private $tramiteRepository;

    private $area;
    /**
     * TramiteValidator constructor.
     * @param AreaRepository $areaRepository
     * @param TramiteRepository $tramiteRepository
     */
    public function __construct(
        AreaRepository $areaRepository,
        TramiteRepository $tramiteRepository
    ) {
        $this->areaRepository = $areaRepository;
        $this->tramiteRepository = $tramiteRepository;
    }

    /**
     * Valida parametros para crear un trámite
     *
     * @param array $params array con datos a validar
     * @return array
     */
    private function validarParam($params)
    {
        return $this->validar($params, [
            'nombre' => 'required',
            'visibilidad' => 'required:integer',
            'campos' => 'required:json',
            'duracion' => 'required:duracionTramite',
            'excepcional' => 'required:integer',
        ]);
    }

    /**
     * Valida parámetros en la creación de un trámite
     *
     * @param array $params array con datos a validar
     * @return ValidateResultado
     */
    public function validarCreate($params)
    {
        $errors = $this->validarParam($params);

       if(is_array($params['area'])) {

           foreach($params['area'] as $area) {

               if(is_null($valor = $this->areaRepository->find($area))) {

                   $errors[] = 'Area '.$area.' inexistente';

               } else {

                   $this->area[] = $valor;
               }

           }

       } else {

           if(!empty($params['area'])) {

               if(is_null($valor = $this->areaRepository->find($params['area']))) {
                   $errors[] = 'Area '.$params['area'].' inexistente';

               } else {
                   $this->area[] = $valor;

               }
           } else {

               $errors[] = 'El parametro Area debe existir';
           }

       }
        if ($params['duracion'] < 0) {
            $errors[] = 'Duración negativa';
        }

        return new ValidateResultado(null, $errors);
    }

    /**
     * Valida los parámetros en la edición de un trámite y qué tanto dicho
     * trámite y su área existan.
     *
     * @param integer $tramiteID identificador único de trámite
     * @param array $params array con datos a validar
     * @return ValidateResultado
     */
    public function validarEdit($params, $tramiteID)
    {

        $errors = $this->validarParam($params);


        if(is_array($params['area'])) {

            foreach ($params['area'] as $p) {

                if (is_null($this->areaRepository->find($p))) {
                    $errors[] = "Area " . $p . " no esxiste";
                }
            }

        } else {

            if (!empty($params['area'])) {
                if ($this->areaRepository->find($params['area'])) {
                    $errors[] = "Area " . $params['area'] . " inexistente";
                        }

                    } else {

                        $errors[] = "Area inexistente";

                    }

                }

        $tramite = $this->tramiteRepository->find($tramiteID);
        if (! $tramite) {

            $errors[] = 'Tramite inexistente';
        }

        return new ValidateResultado(null, $errors);
    }

    /**
     * Validar eliminar
     *
     * @param object $tramite objeto trámite
     * @param object $turnoRepository objeto TurnoRepository
     * @return ValidateResultado
     */
    public function validarDelete($tramite, $turnoRepository)
    {
        $errors = [];

        if (! $tramite) {
            $errors[] = 'Tramite inexistente';
            return new ValidateResultado(null, $errors);
        }

        if ($tramite->getPuntosAtencion()->count() > 0) {
            $errors[] = 'Existen puntos de atencion asociados al Tramite, no puede ser borrado';
        }

        if ($turnoRepository->findTotalTurnosByTramite($tramite->getId()) > 0) {
            $errors[] = 'Existen turnos asociados al Tramite, no puede ser borrado';
        }

        return new ValidateResultado(null, $errors);
    }

    /**
     * Validar datos de busqueda
     *
     * @param object $tramite objeto trámite
     * @param array $params array con datos a validar
     * @return ValidateResultado
     */
    public function validarSearch($tramite, $params, $areaId)
    {
        $errors = [];

        if(! $tramite){
            $errors[] = 'Tramite inexistente';
            return new ValidateResultado(null, $errors);
        }

        $errors[] = $this->validar($params, [
            'nombre' => 'required'
        ]);
        return new ValidateResultado(null, $errors);
    }

    /**
     * Validar tramite
     *
     * @param object $tramite objeto trámite
     * @return ValidateResultado
     */
    public function validarTramite($tramite)
    {
        $errors = [];
        if (! $tramite) {
            $errors[] = 'Tramite inexistente';
        }
        return new ValidateResultado(null, $errors);
    }


    public function getArea() {

        return $this->area;
    }
}
