<?php
namespace ApiV1Bundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use ApiV1Bundle\Entity\User;

/**
 * Responsable
 *
 * @ORM\Table(name="user_area")
 * @ORM\Entity(repositoryClass="ApiV1Bundle\Repository\UserAreaRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class UserArea extends Usuario
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
     * UserArea constructor.
     * @param $nombre
     * @param $apellido
     * @param $organismo
     * @param $area
     * @param User $user
     */
    public function __construct($nombre, $apellido, $organismo, $area, User $user)
    {
        parent::__construct($nombre, $apellido, $user);
        $this->area = $area;
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
     * @return UserArea
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
     * @return UserArea
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
     * @return UserArea
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
     * Set area
     *
     * @param \ApiV1Bundle\Entity\Area $area
     *
     * @return UserArea
     */
    public function setArea(\ApiV1Bundle\Entity\Area $area = null)
    {
        $this->area = $area;

        return $this;
    }

    /**
     * Get area
     *
     * @return \ApiV1Bundle\Entity\Area
     */
    public function getArea()
    {
        return $this->area;
    }

    /**
     * Set user
     *
     * @param \ApiV1Bundle\Entity\User $user
     *
     * @return UserArea
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
            'id' => $this->area->getOrganismo()->getId(),
            'nombre' => $this->area->getOrganismo()->getNombre()
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
            'id' => $this->area->getId(),
            'nombre' => $this->area->getNombre()
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
        return $this->getArea()->getId();
    }

    public function getOrganismoId()
    {
        return $this->getArea()->getOrganismo()->getId();
    }
}
