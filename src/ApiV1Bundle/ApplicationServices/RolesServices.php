<?php

namespace ApiV1Bundle\ApplicationServices;

use ApiV1Bundle\Entity\ValidateResultado;
use ApiV1Bundle\Entity\Validator\TokenValidator;
use ApiV1Bundle\Helper\JWToken;
use ApiV1Bundle\Repository\UsuarioRepository;

/**
 * Class RolesServices
 * @package ApiV1Bundle\ApplicationServices
 */
class RolesServices
{
    /** @var TokenValidator  */
    private $tokenValidator;
    /** @var UsuarioRepository  */
    private $usuarioRepository;

    /**
     * RolesServices constructor.
     * @param TokenValidator $tokenValidator
     * @param UsuarioRepository $usuarioRepository
     */
    public function __construct(
        TokenValidator $tokenValidator,
        UsuarioRepository $usuarioRepository
    ) {
        $this->tokenValidator = $tokenValidator;
        $this->usuarioRepository = $usuarioRepository;
    }

    /**
     * valida un token
     *
     * @param string $authorization token del usuario logueado
     * @return mixed
     */
    public function getUsuario($authorization)
    {
        $validateResultado = $this->tokenValidator->validarToken($authorization);
        if (! $validateResultado->hasError()) {
            $token = preg_split('/\\s+/', $authorization);
            $userID = JWToken::getUid($token[1]);
            if ( isset($userID)) {
                $usuario = $this->usuarioRepository->findOneBy(['user' => $userID]);
                return new ValidateResultado($usuario, []);
            }
        }
        return $validateResultado;
    }
}
