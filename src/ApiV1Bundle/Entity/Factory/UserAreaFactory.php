<?php
namespace ApiV1Bundle\Entity\Factory;

use ApiV1Bundle\Entity\Interfaces\UsuarioFactoryInterface;
use ApiV1Bundle\Entity\User;
use ApiV1Bundle\Entity\UserArea;
use ApiV1Bundle\Entity\ValidateResultado;
use ApiV1Bundle\Entity\Validator\UserAreaValidator;
use ApiV1Bundle\Entity\Validator\UserValidator;
use ApiV1Bundle\Repository\AreaRepository;
use ApiV1Bundle\Repository\OrganismoRepository;
use ApiV1Bundle\Repository\UserAreaRepository;
use Proxies\__CG__\ApiV1Bundle\Entity\Organismo;

/**
 * Class UserAreaFactory
 * @package ApiV1Bundle\Entity\Factory
 */
class UserAreaFactory extends UsuarioFactory implements UsuarioFactoryInterface
{
    /** @var UserAreaValidator  */
    private $userAreaValidator;
    /** @var UserAreaRepository  */
    private $userAreaRepository;
    /** @var AreaRepository  */
    private $areaRepository;
    /** @var OrganismoRepository  */
    private $organismoRepository;

    /**
     * UserAreaFactory constructor.
     * @param UserAreaValidator $userAreaValidator
     * @param UserAreaRepository $userAreaRepository
     * @param AreaRepository $areaRepository
     * @param OrganismoRepository $organismoRepository
     */
    public function __construct(
        UserAreaValidator $userAreaValidator,
        UserAreaRepository $userAreaRepository,
        AreaRepository $areaRepository,
        OrganismoRepository $organismoRepository
    ) {
        $this->userAreaValidator = $userAreaValidator;
        $this->userAreaRepository = $userAreaRepository;
        $this->areaRepository = $areaRepository;
        $this->organismoRepository = $organismoRepository;
    }

    /**
     * Crear un Usuario Area
     * @param array $params arreglo con los datos del usuario a crear
     * @return mixed
     */
    public function create($params)
    {
        $validateResultado = $this->userAreaValidator->validarCreate($params);
        // el area
        $area = null;
        $organismo = null;
        if (! $validateResultado->hasError()) {
            $area = $this->areaRepository->find($params['area']);
            $organismo = $this->organismoRepository->find($params['organismo']);
            //TODO validar si el area pertenece al organismo y si existe el organismo
            $validateResultado = $this->userAreaValidator->validarEntidad($area, 'Area inexistente');
        }
        if (! $validateResultado->hasError()) {
            $user = new User(
                $params['username'],
                $params['rol']
            );

            $userArea = new UserArea(
                $params['nombre'],
                $params['apellido'],
                $organismo,
                $area,
                $user
            );
            $validateResultado->setEntity($userArea);
        }
        return $validateResultado;
    }
}
