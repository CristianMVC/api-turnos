<?php
namespace ApiV1Bundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Usuario
 * @ORM\MappedSuperclass
 */

/**
 * Class Usuario
 * @package ApiV1Bundle\Entity
 *
 * @ORM\Entity(repositoryClass="ApiV1Bundle\Repository\UsuarioRepository")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({
 *   "admin" = "Admin",
 *   "organismo" = "UserOrganismo",
 *   "area" = "UserArea",
 *   "puntoatencion" = "UserPuntoAtencion"
 * })
 */

abstract class Usuario
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=128)
     */
    protected $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="apellido", type="string", length=128)
     */
    protected $apellido;

    /**
     * Un usuario tiene un User
     * @ORM\OneToOne(targetEntity="User", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="Organismo", inversedBy="usuarios")
     * @ORM\JoinColumn(name="organismo_id", referencedColumnName="id")
     */
    protected $organismo;

    /**
     * @ORM\ManyToOne(targetEntity="Area", inversedBy="usuarios")
     * @ORM\JoinColumn(name="area_id", referencedColumnName="id")
     */
    protected $area;

    /**
     * @ORM\ManyToOne(targetEntity="PuntoAtencion", inversedBy="usuarios")
     * @ORM\JoinColumn(name="puntoatencion_id", referencedColumnName="id")
     */
    protected $puntoAtencion;

    protected function __construct($nombre, $apellido, $user)
    {
        $this->nombre = $nombre;
        $this->apellido = $apellido;
        $this->user = $user;
    }

    /**
     * Get nombre
     *
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Get apellido
     *
     * @return string
     */
    public function getApellido()
    {
        return $this->apellido;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param string $nombre
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }

    /**
     * @param string $apellido
     */
    public function setApellido($apellido)
    {
        $this->apellido = $apellido;
    }

    /**
     * @return mixed
     */
    public function getOrganismo()
    {
        return $this->organismo;
    }

    /**
     * @return mixed
     */
    public function getArea()
    {
        return $this->area;
    }

    /**
     * @return mixed
     */
    public function getPuntoAtencion()
    {
        return $this->puntoAtencion;
    }

    abstract public function getOrganismoData();

    abstract public function getAreaData();

    abstract public function getPuntoAtencionData();

    abstract public function getOrganismoId();

    abstract public function getAreaId();

    abstract public function getPuntoAtencionId();
}
