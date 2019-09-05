<?php
namespace ApiV1Bundle\Entity\Sync;

use ApiV1Bundle\Entity\Interfaces\UsuarioSyncInterface;
use ApiV1Bundle\Entity\User;
use ApiV1Bundle\Entity\Validator\UserValidator;
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
use PhpOption\Tests\Repository;

/**
 * Class UsuarioSyncStrategy
 * @package ApiV1Bundle\Entity\Sync
 */
class UsuarioSyncStrategy implements UsuarioSyncInterface
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
    /** @var AdminSync|OrganismoSync|UserAreaSync|UserPuntoAtencionSync  */
    private $sync;
    /** @var Repository genérico */
    private $repository;

    /**
     * UsuarioSyncStrategy constructor.
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
        $this->sync = $this->getSync($userRol);
    }

    /**
     * Obtenemos el repositorio
     *
     * @return UserOrganismoRepository|UserPuntoAtencionRepository|AdminRepository|UserAreaRepository
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * Editar usuario
     *
     * @param integer $id identificador único de user
     * @param array $params arreglo con los datos del user a editar
     * @return mixed
     * {@inheritDoc}
     * @see \ApiV1Bundle\Entity\Interfaces\UsuarioSyncInterface::edit()
     */
    public function edit($id, $params)
    {
        return $this->sync->edit($id, $params);
    }

    /**
     * Eliminar usuario
     *
     * @param integer $id identificador único de user
     * @return mixed
     * {@inheritDoc}
     * @see \ApiV1Bundle\Entity\Interfaces\UsuarioSyncInterface::delete()
     */
    public function delete($id)
    {
        return $this->sync->delete($id);
    }

    /**
     * Seteamos el sync de acuerdo al tipo de usuario
     *
     * @param integer $userRol Rol de usuario
     * @return AdminSync|OrganismoSync|UserAreaSync|UserPuntoAtencionSync
     */
    private function getSync($userRol)
    {
        switch ($userRol) {
            case User::ROL_ADMIN:
                return $this->adminSyncSetup();
                break;
            case User::ROL_ORGANISMO:
                return $this->userOrganismoSyncSetup();
                break;
            case User::ROL_AREA:
                return $this->userAreaSyncSetup();
                break;
            case User::ROL_PUNTOATENCION:
                return $this->userPuntoAtencionSyncSetup();
                break;
            case User::ROL_ORGANISMO_AUX:
                return $this->userOrganismoSyncSetup();
                break;
        }
    }

    /**
     * Sync del usuario admin
     *
     * @return \ApiV1Bundle\Entity\Sync\AdminSync
     */
    private function adminSyncSetup()
    {
        $this->repository = $this->adminRepository;
        $sync = new AdminSync(
            $this->adminRepository,
            $this->adminValidator
        );
        return $sync;
    }

    /**
     * Sync del usuario del organismo
     *
     * @return \ApiV1Bundle\Entity\Sync\OrganismoSync
     */
    private function userOrganismoSyncSetup()
    {
        $this->repository = $this->userOrganismoRepository;
        $sync = new UserOrganismoSync(
            $this->userOrganismoValidator,
            $this->userOrganismoRepository,
            $this->organismoRepository
        );
        return $sync;
    }

    /**
     * Sync del usuario del area
     *
     * @return \ApiV1Bundle\Entity\Sync\UserAreaSync
     */
    private function userAreaSyncSetup()
    {
        $this->repository = $this->userAreaRepository;
        $sync = new UserAreaSync(
            $this->userAreaValidator,
            $this->userAreaRepository,
            $this->areaRepository
        );
        return $sync;
    }

    /**
     * Sync del usuario del punto de atencion
     *
     * @return \ApiV1Bundle\Entity\Sync\UserPuntoAtencionSync
     */
    private function userPuntoAtencionSyncSetup()
    {
        $this->repository = $this->userPuntoAtencionRepository;
        $sync = new UserPuntoAtencionSync(
            $this->userPuntoAtencionValidator,
            $this->userPuntoAtencionRepository,
            $this->puntoAtencionRepository
        );
        return $sync;
    }
}
