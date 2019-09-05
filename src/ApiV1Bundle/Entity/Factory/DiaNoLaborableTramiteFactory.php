<?php
namespace ApiV1Bundle\Entity\Factory;

use ApiV1Bundle\Entity\ValidateResultado;
use ApiV1Bundle\Entity\DiaNoLaborableTramite;
use ApiV1Bundle\Entity\Validator\DiaNoLaborableTramiteValidator;
use ApiV1Bundle\Repository\PuntoAtencionRepository;
use ApiV1Bundle\Repository\TramiteRepository;

/**
 * Class DiaNoLaborableTramiteFactory
 * @package ApiV1Bundle\Entity\Factory
 */

class DiaNoLaborableTramiteFactory
{
    /** @var OrganismoValidator  */
    private $DiaNoLaborableTramiteValidator;
    /** @var PuntoAtencionRepository  */
    private $puntoAtencionRepository;
    /** @var TramiteRepository  */
    private $tramiteRepository;

    /**
     * OrganismoFactory constructor.
     * @param OrganismoValidator $DiaNoLaborableTramiteValidator
     */
    public function __construct(DiaNoLaborableTramiteValidator $DiaNoLaborableTramiteValidator,
            PuntoAtencionRepository $puntoAtencionRepository,
            TramiteRepository $tramiteRepository)
    {
        $this->DiaNoLaborableTramiteValidator = $DiaNoLaborableTramiteValidator;
        $this->puntoAtencionRepository = $puntoAtencionRepository;
        $this->tramiteRepository = $tramiteRepository;
    }

    /**
     * Crea un DiaNoLaborableTramite
     *
     * @param array $params array con los datos
     * @return mixed
     */
    public function create($params,$punto_atencion_id, $tramite_id)
    {
        $tramite = $this->tramiteRepository->find($tramite_id);
        $punto_atencion = $this->puntoAtencionRepository->find($punto_atencion_id);
        $validateResultados = $this->DiaNoLaborableTramiteValidator->validarDiaNoLaborable($params,$punto_atencion, $tramite);
        if (!$validateResultados->hasError()) {
            $fecha = new \DateTime($params['fecha']);
            $objeto = new DiaNoLaborableTramite($fecha, $punto_atencion, $tramite);
            return new ValidateResultado($objeto, []);
        }
        return $validateResultados;
    }
}
