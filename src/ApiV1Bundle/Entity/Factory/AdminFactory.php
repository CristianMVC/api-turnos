<?php
namespace ApiV1Bundle\Entity\Factory;

use ApiV1Bundle\Entity\Interfaces\UsuarioFactoryInterface;
use ApiV1Bundle\Entity\Admin;
use ApiV1Bundle\Entity\User;
use ApiV1Bundle\Entity\Validator\AdminValidator;
use ApiV1Bundle\Entity\ValidateResultado;

/**
 * Class AdminFactory
 * @package ApiV1Bundle\Entity\Factory
 */
class AdminFactory extends UsuarioFactory implements UsuarioFactoryInterface
{
    /** @var AdminValidator  */
    private $adminValidator;

    /**
     * ResponsableFactory constructor.
     * @param AdminValidator $adminValidator
     */
    public function __construct(AdminValidator $adminValidator)
    {
        $this->adminValidator = $adminValidator;
    }

    /**
     * crear un objeto Admin
     *
     * @param array $params arreglo con los datos del admin
     * @return mixed
     */
    public function create($params)
    {
        $validateResultado = $this->adminValidator->validarCreate($params);
        if (! $validateResultado->hasError()) {
            $user = new User(
                $params['username'],
                $params['rol']
            );

            $admin = new Admin(
                $params['nombre'],
                $params['apellido'],
                $user
            );
            $validateResultado->setEntity($admin);
        }
        return $validateResultado;
    }
}
