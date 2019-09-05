<?php

namespace ApiV1Bundle\Entity\Factory;

use ApiV1Bundle\Entity\User;

/**
 * Class UsuarioFactory
 * @package ApiV1Bundle\Entity\Factory
 */
abstract class UsuarioFactory
{
    /**
     * Firma del método crear para implementar en cada tipo de usuario
     *
     * @param array $params arreglo con los datos paera crear un usuario
     */
    abstract public function create($params);

    /**
     * Securizar el password
     * @param object $user objeto user
     * @param object $encoder encoder para el password
     */
    public function securityPassword($user, $encoder)
    {
        $user->setPassword($encoder->encodePassword($user, $user->getPassword()));
    }
}
