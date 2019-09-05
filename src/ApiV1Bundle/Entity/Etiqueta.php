<?php

namespace ApiV1Bundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Categoria
 *
 * @ORM\Table(name="etiqueta")
 * @ORM\Entity(repositoryClass="ApiV1Bundle\Repository\EtiquetaRepository")
 * @ORM\HasLifecycleCallbacks()
 * */

class Etiqueta
{

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
     * @ORM\Column(name="nombre", type="string", length=255,unique=true)
     */
    private $nombre;


    /**
    @ORM\ManyToMany(targetEntity="Tramite", mappedBy="etiquetas")
     **/
    private $tramites;



    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * @param string $nombre
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }


}