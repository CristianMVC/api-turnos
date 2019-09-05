<?php

namespace ApiV1Bundle\DataFixtures\ORM;

use ApiV1Bundle\Entity\Turno;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;


class TurnoFixtures extends Fixture
{
    /**
     * @var $turno
     */
    public $turno1;
    
    public $turno2;
    
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $punto = $this->getReference('punto');
        $punto2 = $this->getReference('punto-2');
        $tramite1 = $this->getReference('tramite1');
        $tramite2 = $this->getReference('tramite2');
        
        $this->turno1 = new Turno($punto, $tramite1, new \DateTime('2018-07-31'), new \DateTime('10:30'));
        $this->turno1->setEstado(1);
        $this->turno1->setAlerta(2);
        
        $manager->persist($this->turno1);
    
        $this->turno2 = new Turno($punto2, $tramite2, new \DateTime('2018-08-07'), new \DateTime('09:30'));
        $this->turno2->setEstado(0);
        $this->turno2->setAlerta(2);
        $manager->persist($this->turno2);
    
        $manager->flush();
        
    }
    
    public function getDependencies()
    {
        return [
            'ApiV1Bundle\DataFixtures\ORM\PuntoAtencionFixtures',
        ];
    }
}