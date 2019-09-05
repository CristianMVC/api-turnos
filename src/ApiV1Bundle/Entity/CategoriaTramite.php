<?php
/**
 * Created by PhpStorm.
 * User: cristian
 * Date: 03/06/19
 * Time: 12:10
 */

namespace ApiV1Bundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * CategoriaTramite
 * @ORM\Entity(repositoryClass="ApiV1Bundle\Repository\CategoriaTramiteRepository")
 * @ORM\Table(name="categoria_del_tramite")
 */
class CategoriaTramite
{


    /**
     * @var int
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;



    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255)
     *
     */
    private $nombre;


    /**
     * Campo para referenciar un tramite con una categoría
     * @ORM\OneToMany(targetEntity="Tramite", mappedBy="categoriaTramite")
     *@ORM\JoinColumn(nullable=true)
     **/
    private $tramite;


    public function __construct()
    {
        $this->tramite = new ArrayCollection();
    }

    public function getTramite()
    {
        return $this->tramite;
    }



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
     * @param string $categoria
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }
    

}