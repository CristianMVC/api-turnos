<?php
namespace ApiV1Bundle\Entity\Sync;

use ApiV1Bundle\Entity\Validator\UserValidator;
use ApiV1Bundle\Entity\ValidateResultado;
use ApiV1Bundle\Entity\Interfaces\UsuarioSyncInterface;
use ApiV1Bundle\Entity\Validator\UserAreaValidator;
use ApiV1Bundle\Repository\UserAreaRepository;
use ApiV1Bundle\Repository\AreaRepository;

/**
 * Class UserAreaSync
 * @package ApiV1Bundle\Entity\Sync
 */
class UserAreaSync implements UsuarioSyncInterface
{
    /** @var UserAreaValidator  */
    private $userAreaValidator;
    /** @var UserAreaRepository  */
    private $userAreaRepository;
    /** @var AreaRepository  */
    private $areaRepository;

    /**
     * UserAreaSync constructor.
     * @param UserAreaValidator $userAreaValidator
     * @param UserAreaRepository $userAreaRepository
     * @param AreaRepository $areaRepository
     */
    public function __construct(
        UserAreaValidator $userAreaValidator,
        UserAreaRepository $userAreaRepository,
        AreaRepository $areaRepository
    ) {
        $this->userAreaValidator = $userAreaValidator;
        $this->userAreaRepository = $userAreaRepository;
        $this->areaRepository = $areaRepository;
    }

    /**
     * Editar los datos de un usuario de area
     *
     * @param integer $id identificador único de user area
     * @param array $params arreglo con los datos del user a editar
     * @return mixed
     * {@inheritDoc}
     * @see \ApiV1Bundle\Entity\Interfaces\UsuarioSyncInterface::edit()
     */
    public function edit($id, $params)
    {
        $userArea = $this->userAreaRepository->findOneByUser($id);
        $validateResultado = $this->userAreaValidator->validarEdit($params, $userArea, $id);
        if (! $validateResultado->hasError()) {
            $user = $userArea->getUser();
            $userArea->setNombre($params['nombre']);
            $userArea->setApellido($params['apellido']);
            if (isset($params['username'])) {
                $user->setUsername($params['username']);
            }
            $validateResultado->setEntity($userArea);
        }
        return $validateResultado;
    }

    /**
     * Eliminar user area
     *
     * @param integer $id identificador único de user area
     * @return mixed
     */
    public function delete($id)
    {
        $user = $this->userAreaRepository->findOneByUser($id);
        $validateResultado = $this->userAreaValidator->validarEntidad($user, 'Usuario inexistente');
        if (! $validateResultado->hasError()) {
            $validateResultado->setEntity($user);
            return $validateResultado;
        }
        return $validateResultado;
    }
}
