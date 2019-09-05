<?php
namespace ApiV1Bundle\Entity\Factory;

use ApiV1Bundle\Entity\Interfaces\UsuarioFactoryInterface;
use ApiV1Bundle\Entity\User;
use ApiV1Bundle\Entity\UserOrganismo;
use ApiV1Bundle\Entity\ValidateResultado;
use ApiV1Bundle\Entity\Validator\UserOrganismoValidator;
use ApiV1Bundle\Entity\Validator\UserValidator;
use ApiV1Bundle\Repository\OrganismoRepository;

/**
 * Class UserOrganismoFactory
 * @package ApiV1Bundle\Entity\Factory
 */
class UserOrganismoFactory extends UsuarioFactory implements UsuarioFactoryInterface
{
    /** @var UserOrganismoValidator  */
    private $userOrganismoValidator;
    /** @var OrganismoRepository  */
    private $organismoRepository;

    /**
     * UserOrganismoFactory constructor.
     * @param UserOrganismoValidator $userOrganismoValidator
     * @param OrganismoRepository $organismoRepository
     */
    public function __construct(
        UserOrganismoValidator $userOrganismoValidator,
        OrganismoRepository $organismoRepository
    ) {
        $this->userOrganismoValidator = $userOrganismoValidator;
        $this->organismoRepository = $organismoRepository;
    }

    /**
     * crear un usuario organismo
     *
     * @param array $params arreglo con los datos del usuario a crear
     * @return mixed
     */
    public function create($params)
    {
        $validateResultado = $this->userOrganismoValidator->validarCreate($params);
        // el organismo
        $organismo = null;
        if (! $validateResultado->hasError()) {
            $organismo = $this->organismoRepository->find($params['organismo']);
            $validateResultado = $this->userOrganismoValidator->validarEntidad($organismo, 'Organismo inexistente');
        }
        if (! $validateResultado->hasError()) {
            $user = new User(
                $params['username'],
                $params['rol']
            );

            $userOrganismo = new UserOrganismo(
                $params['nombre'],
                $params['apellido'],
                $organismo,
                $user
            );
            $validateResultado->setEntity($userOrganismo);
        }
        return $validateResultado;
    }
}
