<?php
namespace ApiV1Bundle\Entity;

use ApiV1Bundle\Repository\AdminRepository;
use ApiV1Bundle\Repository\UserAreaRepository;
use ApiV1Bundle\Repository\UserOrganismoRepository;
use ApiV1Bundle\Repository\UserPuntoAtencionRepository;

class UsuarioStrategy
{
    private $adminRepository;
    private $userOrganismoRepository;
    private $userAreaRepository;
    private $userPuntoAtencionRepository;

    public function __construct(
        AdminRepository $adminRepository,
        UserOrganismoRepository $userOrganismoRepository,
        UserAreaRepository $userAreaRepository,
        UserPuntoAtencionRepository $userPuntoAtencionRepository
    ) {
        $this->adminRepository = $adminRepository;
        $this->userOrganismoRepository = $userOrganismoRepository;
        $this->userAreaRepository = $userAreaRepository;
        $this->userPuntoAtencionRepository = $userPuntoAtencionRepository;
    }

    /**
     * Obtenemos los datos del usuario
     *
     * @param $user
     * @return array
     */
    public function getUser($user)
    {
        $repository = $this->getRepository($user->getRol());
        $usuario = $repository->findOneByUser($user);
        return $this->getUserData($usuario, $user->getRol());
    }

    /**
     * Datos básicos del usuario
     *
     * @param $usuario
     * @return array
     */
    private function getUserData($usuario, $userRol)
    {
        $userData = [
            'id' => $usuario->getUser()->getId(),
            'nombre' => $usuario->getNombre(),
            'apellido' => $usuario->getApellido(),
            'username' => $usuario->getUser()->getUsername(),
            'rol' => $usuario->getUser()->getRol()
        ];
        $userData = array_merge($userData, $this->userDataByRol($usuario, $userRol));
        return $userData;
    }

    /**
     * Obtenemos el repositorio de acuerdo al tipo de usuario
     * @param $userRol
     * @return repository
     */
    private function getRepository($userRol)
    {
        switch ($userRol) {
            case User::ROL_ADMIN:
                return $this->adminRepository;
                break;
            case User::ROL_ORGANISMO:
                return $this->userOrganismoRepository;
                break;
            case User::ROL_AREA:
                return $this->userAreaRepository;
                break;
            case User::ROL_PUNTOATENCION:
                return $this->userPuntoAtencionRepository;
                break;
            case User::ROL_ORGANISMO_AUX:
                return $this->userOrganismoRepository;
                break;
        }
    }

    /**
     * User data by type
     * @param $usuario
     * @param $userRol
     * @return array
     */
    private function userDataByRol($usuario, $userRol)
    {
        switch ($userRol) {
            case User::ROL_ADMIN:
                return $this->adminData($usuario);
                break;
            case User::ROL_ORGANISMO:
                return $this->userOrganismoData($usuario);
                break;
            case User::ROL_AREA:
                return $this->userAreaData($usuario);
                break;
            case User::ROL_PUNTOATENCION:
                return $this->userPuntoAtencionData($usuario);
                break;
            case User::ROL_ORGANISMO_AUX:
                return $this->userOrganismoData($usuario);
                break;
        }
    }

    /**
     * Datos adicionales del admin
     *
     * @param $usuario
     * @return array
     */
    private function adminData($usuario)
    {
        return [];
    }

    /**
     * Datos adicionales del usuario del organismo
     *
     * @param $usuario
     * @return array
     */
    private function userOrganismoData($usuario)
    {
        $organismo = $usuario->getOrganismo();

        $data = [
            'organismo' => [
                'id' => $organismo->getId(),
                'nombre' => $organismo->getNombre()
            ]
        ];
        return $data;
    }

    /**
     * Datos adicionales del usuario del area
     *
     * @param $usuario
     * @return array
     */
    private function userAreaData($usuario)
    {
        $area = $usuario->getArea();
        $organismo = $area->getOrganismo();

        $data = [
            'organismo' => [
                'id' => $organismo->getId(),
                'nombre' => $organismo->getNombre()
            ],
            'area' => [
                'id' => $area->getId(),
                'nombre' => $area->getNombre()
            ]
        ];
        return $data;
    }

    /**
     * Datos adicionales del usuario del punto de atención
     *
     * @param $usuario
     * @return array
     */
    private function userPuntoAtencionData($usuario)
    {
        $puntoAtencion = $usuario->getPuntoAtencion();
        $area = $puntoAtencion->getArea();
        $organismo = $area->getOrganismo();

        $data = [
            'organismo' => [
                'id' => $organismo->getId(),
                'nombre' => $organismo->getNombre()
            ],
            'area' => [
                'id' => $area->getId(),
                'nombre' => $area->getNombre()
            ],
            'puntoAtencion' => [
                'id' => $puntoAtencion->getId(),
                'nombre' => $puntoAtencion->getNombre()
            ]
        ];
        return $data;
    }
}
