<?php
namespace ApiV1Bundle\Entity\Factory;

use ApiV1Bundle\Entity\ValidateResultado;
use ApiV1Bundle\Entity\Token;
use ApiV1Bundle\Repository\TokenRepository;

/**
 * Class TokenFactory
 * @package ApiV1Bundle\Entity\Factory
 */
class TokenFactory
{
    /** @var TokenRepository  */
    private $tokenRepository;

    /**
     * TokenFactory constructor.
     * @param TokenRepository $tokenRepository
     */
    public function __construct(TokenRepository $tokenRepository)
    {
        $this->tokenRepository = $tokenRepository;
    }

    /**
     * Agrega un token al listado de tokens invalidados
     *
     * @param string $token
     * @return mixed
     */
    public function insert($token)
    {
        $invalidToken = new Token($token);
        return new ValidateResultado($invalidToken, []);
    }
}
