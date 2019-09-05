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
class MiargentinaValidator extends SNTValidator
{
    /** @var TurnoRepository  */
    private $turnoRepository;
    /** @var TramiteRepository  */
    private $tramiteRepository;
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
        PuntoTramiteRepository $puntoTramiteRepository
    ) {
        $this->turnoRepository = $turnoRepository;
        $this->tramiteRepository = $tramiteRepository;
        $this->puntoTramiteRepository = $puntoTramiteRepository;
    }

    
    /**
     * 
     * @param type $params
     * @param type $turno
     * @return boolean
     */
    public function validarTurnosPorCuil($params, $puntoAtencionId, $tramiteId) {
        $puntoTramite = $this->puntoTramiteRepository->findOneBy([
            'puntoAtencion' => $puntoAtencionId,
            'tramite' => $tramiteId 
        ]);
        if (!$puntoTramite) {
            return false;
        }
        // verificar si el tramite puede tener tramites duplicados para el mismo cuil
        $tramite_horizonte = $this->tramiteRepository->findHorizonte($tramiteId);
        $puede_multiple = $this->turnoRepository->verificarMultipleTramite(
                $puntoTramite, null, $tramite_horizonte, ServicesHelper::buildValidDocument($params['cuil']), $params["fecha"], $tramiteId, $puntoAtencionId
        );
        // verificar fecha horizonte
        if ($puede_multiple) {
            return true;
        }
        // verificar si el tramite permite sacar turnos para otra persona
        $puede_otrapersona = $this->turnoRepository->verificarPermiteOtro(
                $puntoTramite, null, ServicesHelper::buildValidDocument($params['cuil']), $params["fecha"], $tramiteId, $puntoAtencionId
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

}
