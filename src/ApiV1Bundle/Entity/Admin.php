<?php

namespace ApiV1Bundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Admin
 *
 * @ORM\Table(name="user_admin")
 * @ORM\Entity(repositoryClass="ApiV1Bundle\Repository\AdminRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Admin extends Usuario
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

    public function __construct($nombre, $apellido, $user)
    {
        parent::__construct($nombre, $apellido, $user);
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
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
     * @return \DateTime
     */
    public function getFechaBorrado()
    {
        return $this->fechaBorrado;
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
            'id' => null,
            'nombre' => null
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

    public function getOrganismoId()
    {
        return null;
    }

    public function getAreaId()
    {
        return null;
    }

    public function getPuntoAtencionId()
    {
        return null;
    }
}
