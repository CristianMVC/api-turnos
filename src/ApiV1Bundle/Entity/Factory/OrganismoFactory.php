<?php
namespace ApiV1Bundle\Entity\Factory;

use ApiV1Bundle\Entity\ValidateResultado;
use ApiV1Bundle\Entity\Organismo;
use ApiV1Bundle\Entity\Validator\OrganismoValidator;

/**
 * Class OrganismoFactory
 * @package ApiV1Bundle\Entity\Factory
 */

class OrganismoFactory
{
    /** @var OrganismoValidator  */
    private $organismoValidator;

    /**
     * OrganismoFactory constructor.
     * @param OrganismoValidator $organismoValidator
     */
    public function __construct(OrganismoValidator $organismoValidator)
    {
        $this->organismoValidator = $organismoValidator;
    }

    /**
     * Crea un organismo
     *
     * @param array $params array con los datos del organismo
     * @return mixed
     */
    public function create($params)
    {
        $validateResultados = $this->organismoValidator->validarCreate($params);
        if (!$validateResultados->hasError()) {
            $organismo = new Organismo($params['nombre'], $params['abreviatura']);
            return new ValidateResultado($organismo, []);
        }
        return $validateResultados;
    }
}
