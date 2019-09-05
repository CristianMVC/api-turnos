<?php

namespace ApiV1Bundle\Entity\Sync;

use ApiV1Bundle\Entity\ValidateResultado;
use ApiV1Bundle\Entity\Validator\OrganismoValidator;
use ApiV1Bundle\Repository\OrganismoRepository;

/**
 * Class OrganismoSync
 * @package ApiV1Bundle\Entity\Sync
 */
class OrganismoSync
{
    /** @var OrganismoRepository  */
    private $organismoRepository;
    /** @var OrganismoValidator  */
    private $organismoValidator;

    /**
     * OrganismoSync constructor.
     * @param OrganismoRepository $organismoRepository
     * @param OrganismoValidator $organismoValidator
     */
    public function __construct(
        OrganismoRepository $organismoRepository,
        OrganismoValidator $organismoValidator
    ) {
        $this->organismoRepository = $organismoRepository;
        $this->organismoValidator = $organismoValidator;
    }

    /**
     * Editar organismo
     *
     * @param integer $id Identificador único del organismo
     * @param array $params array con datos del organismo
     * @return mixed
     */

    public function edit($id, $params)
    {
        $organismo = $this->organismoRepository->find($id);
        $validateResultado = $this->organismoValidator->validarEdit($organismo, $params);

        if (!$validateResultado->hasError()) {
            $organismo->setNombre($params['nombre']);
            $organismo->setAbreviatura($params['abreviatura']);
            return new ValidateResultado($organismo, []);
        }
        return $validateResultado;
    }

    /**
     * Eliminar organismo
     *
     * @param integer $id Identificador único del organismo
     * @return mixed
     */
    public function delete($id)
    {
        $organismo = $this->organismoRepository->find($id);

        $validateResultado = $this->organismoValidator->validarDelete($organismo);

        if (! $validateResultado->hasError()) {
            return new ValidateResultado($organismo, []);
        }

        return $validateResultado;
    }
}
