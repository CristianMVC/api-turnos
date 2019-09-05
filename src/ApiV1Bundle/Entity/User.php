<?php
namespace ApiV1Bundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use ApiV1Bundle\Helper\ServicesHelper;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="ApiV1Bundle\Repository\UserRepository")
 * @Gedmo\SoftDeleteable(fieldName="fechaBorrado")
 * @ORM\HasLifecycleCallbacks()
 */
class User implements UserInterface, \Serializable
{

    const ROL_ADMIN = 1;
    const ROL_ORGANISMO = 2;
    const ROL_AREA = 3;
    const ROL_PUNTOATENCION = 4;
    const ROL_AGENTE = 5;
    const ROL_ORGANISMO_AUX = 6;

    private $roles = [
        1 => 'ROL_ADMIN',
        2 => 'ROL_ORGANISMO',
        3 => 'ROL_AREA',
        4 => 'ROL_PUNTOATENCION',
        6 => 'ROL_ORGANISMO_AUX'
    ];

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=128)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=60)
     */
    private $password;

    /**
     * @var int
     *
     * @ORM\Column(name="rol_id", type="smallint")
     */
    private $rol;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_creado", type="datetimetz")
     */
    private $fechaCreado;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_modificado", type="datetimetz")
     */
    private $fechaModificado;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_borrado", type="datetimetz", nullable=true)
     */
    private $fechaBorrado;

    public function __construct($username, $rol)
    {
        $this->username = $username;
        $this->password = ServicesHelper::randomPassword(12);
        $this->rol = $rol;
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     *
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return int
     */
    public function getRol()
    {
        return $this->rol;
    }

    /**
     * @param int $rol
     */
    public function setRol($rol)
    {
        $this->rol = $rol;
    }

    /**
     * The user rol
     * {@inheritDoc}
     * @see \Symfony\Component\Security\Core\User\UserInterface::getRoles()
     */
    public function getRoles()
    {
        return $this->roles[$this->rol];
    }

    /**
     * Password salt
     * {@inheritDoc}
     * @see \Symfony\Component\Security\Core\User\UserInterface::getSalt()
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * Erase user credentials
     *
     * {@inheritDoc}
     * @see \Symfony\Component\Security\Core\User\UserInterface::eraseCredentials()
     */
    public function eraseCredentials()
    {
    }

    /**
     * Serialize user
     *
     * {@inheritDoc}
     * @see Serializable::serialize()
     */
    public function serialize()
    {
        return serialize([
            $this->id,
            $this->username,
            $this->password
        ]);
    }

    /**
     * Unserialize user
     *
     * {@inheritDoc}
     * @see Serializable::unserialize()
     */
    public function unserialize($serialized)
    {
        list ($this->id, $this->username, $this->password) = unserialize($serialized);
    }

    /**
     * Genera las fechas de creación y modificación de un punto de atención
     *
     * @ORM\PrePersist
     */
    public function setFechas()
    {
        $this->fechaCreado = new \DateTime();
        $this->fechaModificado = new \DateTime();
    }
}
