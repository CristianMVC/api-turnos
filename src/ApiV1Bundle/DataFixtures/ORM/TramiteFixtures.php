<?php

namespace ApiV1Bundle\DataFixtures\ORM;

use ApiV1Bundle\Entity\Tramite;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class TramiteFixtures extends Fixture
{
    
    public $tramite1;
    public $tramite2;
    
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $this->tramite1 = new Tramite('Trámite 1', 1, $this->getReference('area1'));
        $this->tramite1->setDuracion(100);
        $this->tramite1->setRequisitos('requisitos');
        $this->tramite1->setExcepcional(1);
        
        $this->addReference('tramite1', $this->tramite1);
    
        $manager->persist($this->tramite1);
        
        $this->tramite2 = new Tramite('Trámite 2', 1, $this->getReference('area2'));
        $this->tramite2->setDuracion(100);
        $this->tramite2->setRequisitos('requisitos');
        $this->tramite2->setExcepcional(1);
        
        $this->addReference('tramite2', $this->tramite2);
    
        $manager->persist($this->tramite2);
    
        $manager->flush();
        
    }
    
    public function getDependencies()
    {
        return [
            'ApiV1Bundle\DataFixtures\ORM\AreaFixtures',
        ];
    }
    
}