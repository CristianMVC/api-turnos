<?php
namespace ApiV1Bundle\Entity\Sync;

use ApiV1Bundle\Entity\Validator\UserValidator;
use ApiV1Bundle\Entity\Validator\UserOrganismoValidator;
use ApiV1Bundle\Repository\UserOrganismoRepository;
use ApiV1Bundle\Entity\ValidateResultado;
use ApiV1Bundle\Entity\Interfaces\UsuarioSyncInterface;
use ApiV1Bundle\Repository\OrganismoRepository;

/**
 * Class UserOrganismoSync
 * @package ApiV1Bundle\Entity\Sync
 */
class UserOrganismoSync implements UsuarioSyncInterface
{
    /** @var UserOrganismoValidator  */
    private $userOrganismoValidator;
    /** @var UserOrganismoRepository  */
    private $userOrganismoRepository;
    /** @var OrganismoRepository  */
    private $organismoRepository;

    /**
     * UserOrganismoSync constructor.
     * @param UserOrganismoValidator $userOrganismoValidator
     * @param UserOrganismoRepository $userOrganismoRepository
     * @param OrganismoRepository $organismoRepository
     */
    public function __construct(
        UserOrganismoValidator $userOrganismoValidator,
        UserOrganismoRepository $userOrganismoRepository,
        OrganismoRepository $organismoRepository
    ) {
        $this->userOrganismoValidator = $userOrganismoValidator;
        $this->userOrganismoRepository = $userOrganismoRepository;
        $this->organismoRepository = $organismoRepository;
    }

    /**
     * Editar los datos de un usuario de organismo
     *
     * @param integer $id identificador único de user organismo
     * @param array $params arreglo con los datos del user a editar
     * @return mixed
     * {@inheritDoc}
     * @see \ApiV1Bundle\Entity\Interfaces\UsuarioSyncInterface::edit()
     */
    public function edit($id, $params)
    {
        $userOrganismo = $this->userOrganismoRepository->findOneByUser($id);
        $validateResultado = $this->userOrganismoValidator->validarEdit($params, $userOrganismo, $id);
        if (! $validateResultado->hasError()) {
            $user = $userOrganismo->getUser();
            $userOrganismo->setNombre($params['nombre']);
            $userOrganismo->setApellido($params['apellido']);
            if (isset($params['username'])) {
                $user->setUsername($params['username']);
            }
            $validateResultado->setEntity($userOrganismo);
        }
        return $validateResultado;
    }

    /**
     * Eliminar user organismo
     *
     * @param integer $id identificador único de user organismo
     * @return mixed
     */
    public function delete($id)
    {
        $user = $this->userOrganismoRepository->findOneByUser($id);
        $validateResultado = $this->userOrganismoValidator->validarEntidad($user, 'Usuario inexistente');
        if (! $validateResultado->hasError()) {
            $validateResultado->setEntity($user);
            return $validateResultado;
        }
        return $validateResultado;
    }
}
