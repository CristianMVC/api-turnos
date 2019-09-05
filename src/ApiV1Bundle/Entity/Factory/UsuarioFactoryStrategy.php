<?php
namespace ApiV1Bundle\Entity\Factory;

use ApiV1Bundle\Entity\Interfaces\UsuarioFactoryInterface;
use ApiV1Bundle\Entity\User;
use ApiV1Bundle\Repository\AdminRepository;
use ApiV1Bundle\Repository\AreaRepository;
use ApiV1Bundle\Repository\OrganismoRepository;
use ApiV1Bundle\Repository\PuntoAtencionRepository;
use ApiV1Bundle\Repository\UserAreaRepository;
use ApiV1Bundle\Repository\UserOrganismoRepository;
use ApiV1Bundle\Repository\UserPuntoAtencionRepository;
use ApiV1Bundle\Entity\Validator\AdminValidator;
use ApiV1Bundle\Entity\Validator\UserOrganismoValidator;
use ApiV1Bundle\Entity\Validator\UserAreaValidator;
use ApiV1Bundle\Entity\Validator\UserPuntoAtencionValidator;

/**
 * Class UsuarioFactoryStrategy
 * @package ApiV1Bundle\Entity\Factory
 */
class UsuarioFactoryStrategy implements UsuarioFactoryInterface
{
    /** @var AdminRepository  */
    private $adminRepository;
    /** @var AdminValidator  */
    private $adminValidator;
    /** @var UserOrganismoRepository  */
    private $userOrganismoRepository;
    /** @var UserOrganismoValidator  */
    private $userOrganismoValidator;
    /** @var UserAreaRepository  */
    private $userAreaRepository;
    /** @var UserAreaValidator  */
    private $userAreaValidator;
    /** @var UserPuntoAtencionRepository  */
    private $userPuntoAtencionRepository;
    /** @var UserPuntoAtencionValidator  */
    private $userPuntoAtencionValidator;
    /** @var OrganismoRepository  */
    private $organismoRepository;
    /** @var AreaRepository  */
    private $areaRepository;
    /** @var PuntoAtencionRepository  */
    private $puntoAtencionRepository;
    /** @var factory genérico */
    private $factory;
    /** @var  Repository genérico */
    private $repository;

    /**
     * UsuarioFactoryStrategy constructor.
     * @param AdminRepository $adminRepository
     * @param AdminValidator $adminValidator
     * @param UserOrganismoRepository $userOrganismoRepository
     * @param UserOrganismoValidator $userOrganismoValidator
     * @param UserAreaRepository $userAreaRepository
     * @param UserAreaValidator $userAreaValidator
     * @param UserPuntoAtencionRepository $userPuntoAtencionRepository
     * @param UserPuntoAtencionValidator $userPuntoAtencionValidator
     * @param OrganismoRepository $organismoRepository
     * @param AreaRepository $areaRepository
     * @param PuntoAtencionRepository $puntoAtencionRepository
     * @param $userRol
     */
    public function __construct(
        AdminRepository $adminRepository,
        AdminValidator $adminValidator,
        UserOrganismoRepository $userOrganismoRepository,
        UserOrganismoValidator $userOrganismoValidator,
        UserAreaRepository $userAreaRepository,
        UserAreaValidator $userAreaValidator,
        UserPuntoAtencionRepository $userPuntoAtencionRepository,
        UserPuntoAtencionValidator $userPuntoAtencionValidator,
        OrganismoRepository $organismoRepository,
        AreaRepository $areaRepository,
        PuntoAtencionRepository $puntoAtencionRepository,
        $userRol
    ) {
        $this->adminRepository = $adminRepository;
        $this->adminValidator = $adminValidator;
        $this->userOrganismoRepository = $userOrganismoRepository;
        $this->userOrganismoValidator = $userOrganismoValidator;
        $this->userAreaRepository = $userAreaRepository;
        $this->userAreaValidator = $userAreaValidator;
        $this->userPuntoAtencionRepository = $userPuntoAtencionRepository;
        $this->userPuntoAtencionValidator = $userPuntoAtencionValidator;
        $this->organismoRepository = $organismoRepository;
        $this->areaRepository = $areaRepository;
        $this->puntoAtencionRepository = $puntoAtencionRepository;
        $this->factory = $this->setFactory($userRol);
    }

    /**
     * Crear nuevo usuario
     *
     * @param array $params datos del usuario a crear
     * @return mixed
     * {@inheritDoc}
     * @see \ApiV1Bundle\Entity\Interfaces\UsuarioInterface::create()
     */
    public function create($params)
    {
        return $this->factory->create($params);
    }

    /**
     * Obtener el repositorio
     *
     * @return mixed
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * Encriptamos el password del usuario para poder guardarlo en la base de datos
     *
     * @param object $user objeto usuario
     * @param object $encoder encoder para el password
     */
    public function securityPassword($user, $encoder)
    {
        $this->factory->securityPassword($user, $encoder);
    }

    /**
     * Seteamos el factory de acuerdo al tipo de usuario
     *
     * @param integer $userRol Rol del usuario
     * @return mixed
     */
    private function setFactory($userRol)
    {
        switch ($userRol) {
            case User::ROL_ADMIN:
                return $this->adminFactorySetup();
                break;
            case User::ROL_ORGANISMO:
                return $this->userOrganismoFactorySetup();
                break;
            case User::ROL_AREA:
                return $this->userAreaFactorySetup();
                break;
            case User::ROL_PUNTOATENCION:
                return $this->userPuntoAtencionFactorySetup();
                break;
            case User::ROL_ORGANISMO_AUX:
                return $this->userOrganismoFactorySetup();
                break;
        }
    }

    /**
     * Factory de los usuarios tipo admin
     *
     * @return mixed
     */
    private function adminFactorySetup()
    {
        $this->repository = $this->adminRepository;
        $factory = new AdminFactory($this->adminValidator);
        return $factory;
    }

    /**
     * Factory de los usuarios tipo organismo
     *
     * @return mixed
     */
    private function userOrganismoFactorySetup()
    {
        $this->repository = $this->userOrganismoRepository;
        $factory = new UserOrganismoFactory(
            $this->userOrganismoValidator,
            $this->organismoRepository
        );
        return $factory;
    }

    /**
     * Factory de los usuarios tipo agente
     *
     * @return mixed
     */
    private function userAreaFactorySetup()
    {
        $this->repository = $this->userAreaRepository;
        $factory = new UserAreaFactory(
            $this->userAreaValidator,
            $this->userAreaRepository,
            $this->areaRepository,
            $this->organismoRepository
        );
        return $factory;
    }

    /**
     * Factory de los usuarios tipo responsable
     *
     * @return mixed
     */
    private function userPuntoAtencionFactorySetup()
    {
        $this->repository = $this->userPuntoAtencionRepository;
        $factory = new UserPuntoAtencionFactory(
            $this->userPuntoAtencionValidator,
            $this->userPuntoAtencionRepository,
            $this->puntoAtencionRepository
        );
        return $factory;
    }
}
