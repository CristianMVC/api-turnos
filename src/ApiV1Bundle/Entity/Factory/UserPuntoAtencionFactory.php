<?php
namespace ApiV1Bundle\Entity\Factory;

use ApiV1Bundle\Entity\Interfaces\UsuarioFactoryInterface;
use ApiV1Bundle\Entity\User;
use ApiV1Bundle\Entity\UserPuntoAtencion;
use ApiV1Bundle\Entity\ValidateResultado;
use ApiV1Bundle\Entity\Validator\UserPuntoAtencionValidator;
use ApiV1Bundle\Entity\Validator\UserValidator;
use ApiV1Bundle\Repository\PuntoAtencionRepository;
use ApiV1Bundle\Repository\UserPuntoAtencionRepository;

/**
 * Class UserPuntoAtencionFactory
 * @package ApiV1Bundle\Entity\Factory
 */
class UserPuntoAtencionFactory extends UsuarioFactory implements UsuarioFactoryInterface
{
    /** @var UserPuntoAtencionValidator  */
    private $userPuntoAtencionValidator;
    /** @var UserPuntoAtencionRepository  */
    private $userPuntoAtencionRepository;
    /** @var PuntoAtencionRepository  */
    private $puntoatencionRepository;


    /**
     * UserPuntoAtencionFactory constructor.
     * @param UserPuntoAtencionValidator $userPuntoAtencionValidator
     * @param UserPuntoAtencionRepository $userPuntoAtencionRepository
     * @param PuntoAtencionRepository $puntoatencionRepository
     */
    public function __construct(
        UserPuntoAtencionValidator $userPuntoAtencionValidator,
        UserPuntoAtencionRepository $userPuntoAtencionRepository,
        PuntoAtencionRepository $puntoatencionRepository
    ) {
        $this->userPuntoAtencionValidator = $userPuntoAtencionValidator;
        $this->userPuntoAtencionRepository = $userPuntoAtencionRepository;
        $this->puntoatencionRepository = $puntoatencionRepository;
    }

    /**
     * crear un usuario respondable del punto de atención
     *
     * @param array $params arreglo con los datos del usuario a crear
     * @return mixed
     */
    public function create($params)
    {
        $validateResultado = $this->userPuntoAtencionValidator->validarCreate($params);
        // el puntoatencion
        $puntoatencion = null;
        if (! $validateResultado->hasError()) {
            $puntoatencion = $this->puntoatencionRepository->find($params['puntoAtencion']);
            $validateResultado = $this->userPuntoAtencionValidator->validarEntidad(
                $puntoatencion,
                'Punto de atencion inexistente'
            );
        }
        if (! $validateResultado->hasError()) {
            $user = new User(
                $params['username'],
                $params['rol']
            );

            $userPuntoAtencion = new UserPuntoAtencion(
                $params['nombre'],
                $params['apellido'],
                $puntoatencion->getArea()->getOrganismo(),
                $puntoatencion->getArea(),
                $puntoatencion,
                $user
            );
            $validateResultado->setEntity($userPuntoAtencion);
        }
        return $validateResultado;
    }
}
