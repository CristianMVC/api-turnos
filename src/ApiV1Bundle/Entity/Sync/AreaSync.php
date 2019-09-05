<?php
namespace ApiV1Bundle\Entity\Sync;

use ApiV1Bundle\Entity\ValidateResultado;
use ApiV1Bundle\Repository\OrganismoRepository;
use ApiV1Bundle\Repository\AreaRepository;
use ApiV1Bundle\Entity\Validator\AreaValidator;

/**
 * Class AreaSync
 * @package ApiV1Bundle\Entity\Sync
 */

class AreaSync
{
    /** @var OrganismoRepository  */
    private $organismoRepository;
    /** @var AreaRepository  */
    private $areaRepository;
    /** @var AreaValidator  */
    private $areaValidator;

    /**
     * AreaSync constructor.
     * @param OrganismoRepository $organismoRepository
     * @param AreaRepository $areaRepository
     * @param AreaValidator $areaValidator
     */
    public function __construct(
        OrganismoRepository $organismoRepository,
        AreaRepository $areaRepository,
        AreaValidator $areaValidator
    ) {
        $this->organismoRepository = $organismoRepository;
        $this->areaRepository = $areaRepository;
        $this->areaValidator = $areaValidator;
    }

    /**
     * Edita un área
     *
     * @param integer $organismoId Identificador único del organismo
     * @param integer $id Identificador único del área
     * @param array $params array con datos del área
     * @return mixed
     */
    public function edit($organismoId, $id, $params)
    {
        $area = $this->areaRepository->find($id);
        $organismo = $this->organismoRepository->find($organismoId);

        $validateResultados = $this->areaValidator->validarEdit($params, $area, $organismo);

        if (!$validateResultados->hasError()) {
            $area->setNombre($params['nombre']);
            $area->setAbreviatura($params['abreviatura']);
            return new ValidateResultado($area, []);
        }

        return $validateResultados;
    }

    /**
     * Borra un área
     * @param integer $areaId Identificador único del área
     * @param integer $organismoId Identificador único del organismo
     * @return mixed
     */
    public function delete($organismoId, $areaId)
    {

        $area = $this->areaRepository->find($areaId);
        $organismo = $this->organismoRepository->find($organismoId);
        $validateResultado = $this->areaValidator->validarDelete($organismo, $area);

        if (! $validateResultado->hasError()) {
            return new ValidateResultado($area, []);
        }

        return $validateResultado;
    }
}
