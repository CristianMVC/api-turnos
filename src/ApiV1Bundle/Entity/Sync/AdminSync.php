<?php
namespace ApiV1Bundle\Entity\Sync;

use ApiV1Bundle\Entity\Validator\UserValidator;
use ApiV1Bundle\Entity\Validator\AdminValidator;
use ApiV1Bundle\Repository\AdminRepository;
use ApiV1Bundle\Entity\ValidateResultado;
use ApiV1Bundle\Entity\Interfaces\UsuarioSyncInterface;

/**
 * Class AdminSync
 * @package ApiV1Bundle\Entity\Sync
 */
class AdminSync implements UsuarioSyncInterface
{
    /** @var AdminValidator  */
    private $adminValidator;
    /** @var AdminRepository  */
    private $adminRepository;

    /**
     * AdminSync constructor.
     * @param AdminRepository $adminRepository
     * @param AdminValidator $adminValidator
     */
    public function __construct(
        AdminRepository $adminRepository,
        AdminValidator $adminValidator
    ) {
        $this->adminRepository = $adminRepository;
        $this->adminValidator = $adminValidator;
    }

    /**
     * Editar los datos de un admin
     *
     * @param integer $id identificador único de admin
     * @param array $params arreglo con los datos para la edición
     * @return mixed
     * {@inheritDoc}
     * @see \ApiV1Bundle\Entity\Interfaces\UsuarioSyncInterface::edit()
     */
    public function edit($id, $params)
    {
        $admin = $this->adminRepository->findOneByUser($id);
        $validateResultado = $this->adminValidator->validarEdit($params, $admin, $id);
        if (! $validateResultado->hasError()) {
            $user = $admin->getUser();
            $admin->setNombre($params['nombre']);
            $admin->setApellido($params['apellido']);
            if (isset($params['username'])) {
                $user->setUsername($params['username']);
            }
            $validateResultado->setEntity($admin);
        }
        return $validateResultado;
    }

    /**
     * Borra un admin
     * @param integer $id Identificador único del admin
     * @return mixed
     */
    public function delete($id)
    {
        $admin = $this->adminRepository->findOneByUser($id);
        $validateResultado = $this->adminValidator->validarEntidad($admin, 'Usuario inexistente');
        if (! $validateResultado->hasError()) {
            $validateResultado->setEntity($admin);
            return $validateResultado;
        }
        return $validateResultado;
    }
}
