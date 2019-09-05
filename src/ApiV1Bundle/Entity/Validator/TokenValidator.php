<?php

namespace ApiV1Bundle\Entity\Validator;

use ApiV1Bundle\Entity\ValidateResultado;
use ApiV1Bundle\Helper\JWToken;
use ApiV1Bundle\Repository\TokenRepository;
use ApiV1Bundle\Repository\UserRepository;

/**
 * Class TokenValidator
 * @package ApiV1Bundle\Entity\Validator
 */
class TokenValidator extends SNTValidator
{
    /** @var UserRepository  */
    private $userRepository;
    /** @var TokenRepository  */
    private $tokenRepository;
    /** @var JWToken  */
    private $jwtoken;

    /**
     * TokenValidator constructor.
     * @param UserRepository $userRepository
     * @param TokenRepository $tokenRepository
     * @param JWToken $jwtoken
     */
    public function __construct(
        UserRepository $userRepository,
        TokenRepository $tokenRepository,
        JWToken $jwtoken
    )
    {
        $this->userRepository = $userRepository;
        $this->tokenRepository = $tokenRepository;
        $this->jwtoken = $jwtoken;
    }

    /**
     * Validar Token
     *
     * @param string $authorization token del usuario logueado
     * @return ValidateResultado
     */
    public function validarToken($authorization)
    {
        $errors = [];
        $dataToken = $this->validarAuthorization($authorization);

        if (! $dataToken->isValid()) {
            $errors[] = 'Token invalido';
            return new ValidateResultado(null, $errors);
        }

        if (!preg_match('/^Bearer\\s+(.)*?/', $authorization)) {
            return new ValidateResultado(null, ['token ausente']);
        }

        $token = preg_split('/\\s+/', $authorization);

        if (count($token) !== 2) {
            return new ValidateResultado(null, ['token invalido']);
        }

        $userID = $this->jwtoken->getUid($token[1]);

        if (isset($userID)) {
            $user = $this->userRepository->find($userID);
        } else {
            $errors[] = 'El Token no es de un usuario valido.';
            return  new ValidateResultado(null, $errors);
        }

        return new ValidateResultado($user, []);
    }


    /**
     * Validar authorization
     *
     * @param string $authorization
     * @return mixed
     */
    public function validarAuthorization($authorization)
    {
        $token = md5($authorization);
        $tokenCancelado = $this->tokenRepository->findOneByToken($token);
        if ($authorization) {
            list($bearer, $token) = explode(' ', $authorization);
            $token = str_replace('"', '', $token);
        }
        return $this->jwtoken->validate($token, $tokenCancelado);
    }
}
