<?php
namespace ApiV1Bundle\Entity\Factory;

use ApiV1Bundle\Entity\ValidateResultado;
use ApiV1Bundle\Repository\OrganismoRepository;
use ApiV1Bundle\Entity\Area;
use ApiV1Bundle\Entity\Validator\AreaValidator;

/**
 * Class AreaFactory
 * @package ApiV1Bundle\Entity\Factory
 */
class AreaFactory
{
    /** @var OrganismoRepository  */
    private $organismoRepository;
    /** @var AreaValidator  */
    private $areaValidator;

    /**
     * AreaFactory constructor.
     * @param OrganismoRepository $organismoRepository
     * @param AreaValidator $areaValidator
     */
    public function __construct(OrganismoRepository $organismoRepository, AreaValidator $areaValidator)
    {
        $this->organismoRepository = $organismoRepository;
        $this->areaValidator = $areaValidator;
    }

    /**
     * Crea un objeto área
     *
     * @param array $params array con los datos del área a crear
     * @param integer $organismoId Identificador único del organismo al que el área pertenece
     * @return mixed
     */
    public function create($params, $organismoId)
    {
        $organismo = $this->organismoRepository->find($organismoId);

        $validateResultados = $this->areaValidator->validarCreate($params, $organismo);

        if (!$validateResultados->hasError()) {
            $area = new Area($params['nombre'], $params['abreviatura']);
            $area->setOrganismo($organismo);
            return new ValidateResultado($area, []);
        }

        return $validateResultados;
    }
}
