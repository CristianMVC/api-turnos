<?php
namespace ApiV1Bundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use ApiV1Bundle\Entity\Organismo;

/**
 * Agente
 *
 * @ORM\Table(name="user_organismo")
 * @ORM\Entity(repositoryClass="ApiV1Bundle\Repository\UserOrganismoRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class UserOrganismo extends Usuario
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * Fecha de creación del usuario
     *
     * @var \DateTime
     * @ORM\Column(name="fecha_creado", type="datetimetz")
     */
    private $fechaCreado;

    /**
     * Fecha de modificación del usuario
     *
     * @var \DateTime
     * @ORM\Column(name="fecha_modificado", type="datetimetz")
     */
    private $fechaModificado;

    /**
     * Fecha de borrado del usuario
     *
     * @var \DateTime
     * @ORM\Column(name="fecha_borrado", type="datetimetz", nullable=true)
     */
    private $fechaBorrado;

    /**
     * Agente constructor.
     * @param $nombre
     * @param $apellido
     * @param $puntoAtencion
     * @param User $user
     */
    public function __construct($nombre, $apellido, Organismo $organismo, User $user)
    {
        parent::__construct($nombre, $apellido, $user);
        $this->organismo = $organismo;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set fechaCreado
     *
     * @param \DateTime $fechaCreado
     *
     * @return UserOrganismo
     */
    public function setFechaCreado($fechaCreado)
    {
        $this->fechaCreado = $fechaCreado;

        return $this;
    }

    /**
     * Get fechaCreado
     *
     * @return \DateTime
     */
    public function getFechaCreado()
    {
        return $this->fechaCreado;
    }

    /**
     * Set fechaModificado
     *
     * @param \DateTime $fechaModificado
     *
     * @return UserOrganismo
     */
    public function setFechaModificado($fechaModificado)
    {
        $this->fechaModificado = $fechaModificado;

        return $this;
    }

    /**
     * Get fechaModificado
     *
     * @return \DateTime
     */
    public function getFechaModificado()
    {
        return $this->fechaModificado;
    }

    /**
     * Set fechaBorrado
     *
     * @param \DateTime $fechaBorrado
     *
     * @return UserOrganismo
     */
    public function setFechaBorrado($fechaBorrado)
    {
        $this->fechaBorrado = $fechaBorrado;

        return $this;
    }

    /**
     * Get fechaBorrado
     *
     * @return \DateTime
     */
    public function getFechaBorrado()
    {
        return $this->fechaBorrado;
    }

    /**
     * Genera las fechas de creación y modificación
     *
     * @ORM\PrePersist
     */
    public function setFechas()
    {
        $this->fechaCreado = new \DateTime();
        $this->fechaModificado = new \DateTime();
    }

    /**
     * Actualiza la fecha de modificación
     *
     * @ORM\PreUpdate
     */
    public function updatedFechas()
    {
        $this->fechaModificado = new \DateTime();
    }

    /**
     * Set organismo
     *
     * @param \ApiV1Bundle\Entity\Organismo $organismo
     *
     * @return UserOrganismo
     */
    public function setOrganismo(\ApiV1Bundle\Entity\Organismo $organismo = null)
    {
        $this->organismo = $organismo;

        return $this;
    }

    /**
     * Get organismo
     *
     * @return \ApiV1Bundle\Entity\Organismo
     */
    public function getOrganismo()
    {
        return $this->organismo;
    }

    /**
     * Set user
     *
     * @param \ApiV1Bundle\Entity\User $user
     *
     * @return UserOrganismo
     */
    public function setUser(\ApiV1Bundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Datos del organismo
     *
     * {@inheritDoc}
     * @see \ApiV1Bundle\Entity\Usuario::getOrganismoData()
     */
    public function getOrganismoData()
    {
        return [
            'id' => $this->organismo->getId(),
            'nombre' => $this->organismo->getNombre()
        ];
    }

    /**
     * Datos del area
     *
     * {@inheritDoc}
     * @see \ApiV1Bundle\Entity\Usuario::getAreaData()
     */
    public function getAreaData()
    {
        return [
            'id' => null,
            'nombre' => null
        ];
    }

    /**
     * Datos del punto de atencion
     *
     * {@inheritDoc}
     * @see \ApiV1Bundle\Entity\Usuario::getPuntoAtencionData()
     */
    public function getPuntoAtencionData()
    {
        return [
            'id' => null,
            'nombre' => null
        ];
    }

    public function getPuntoAtencionId()
    {
        return null;
    }

    public function getAreaId()
    {
        return null;
    }

    public function getOrganismoId()
    {
        return $this->getOrganismo()->getId();
    }
}
