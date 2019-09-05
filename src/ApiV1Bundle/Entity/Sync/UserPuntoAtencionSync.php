<?php
namespace ApiV1Bundle\Entity\Sync;

use ApiV1Bundle\Entity\Validator\UserValidator;
use ApiV1Bundle\Entity\ValidateResultado;
use ApiV1Bundle\Entity\Interfaces\UsuarioSyncInterface;
use ApiV1Bundle\Entity\Validator\UserPuntoAtencionValidator;
use ApiV1Bundle\Repository\UserPuntoAtencionRepository;
use ApiV1Bundle\Repository\PuntoAtencionRepository;

/**
 * Class UserPuntoAtencionSync
 * @package ApiV1Bundle\Entity\Sync
 */
class UserPuntoAtencionSync implements UsuarioSyncInterface
{
    /** @var UserPuntoAtencionValidator  */
    private $userPuntoAtencionValidator;
    /** @var UserPuntoAtencionRepository  */
    private $userPuntoAtencionRepository;
    /** @var PuntoAtencionRepository  */
    private $puntoatencionRepository;

    /**
     * UserPuntoAtencionSync constructor.
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
     * Editar los datos de un usuario de puntoatencion
     *
     * @param integer $id identificador único de user puntoatencion
     * @param array $params arreglo con los datos del user a editar
     * @return mixed
     * {@inheritDoc}
     * @see \ApiV1Bundle\Entity\Interfaces\UsuarioSyncInterface::edit()
     */
    public function edit($id, $params)
    {
        $userPuntoAtencion = $this->userPuntoAtencionRepository->findOneByUser($id);
        $validateResultado = $this->userPuntoAtencionValidator->validarEdit($params, $userPuntoAtencion, $id);
        if (! $validateResultado->hasError()) {
            $user = $userPuntoAtencion->getUser();
            $userPuntoAtencion->setNombre($params['nombre']);
            $userPuntoAtencion->setApellido($params['apellido']);
            if (isset($params['username'])) {
                $user->setUsername($params['username']);
            }
            $validateResultado->setEntity($userPuntoAtencion);
        }
        return $validateResultado;
    }

    /**
     * Eliminar user puntoatencion
     *
     * @param integer $id identificador único de user puntoatencion
     * @return mixed
     */
    public function delete($id)
    {
        $user = $this->userPuntoAtencionRepository->findOneByUser($id);
        $validateResultado = $this->userPuntoAtencionValidator->validarEntidad($user, 'Usuario inexistente');
        if (! $validateResultado->hasError()) {
            $validateResultado->setEntity($user);
            return $validateResultado;
        }
        return $validateResultado;
    }
}
