<?php

namespace ApiV1Bundle\DataFixtures\ORM;

use ApiV1Bundle\Entity\Provincia;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectManager;

class ProvinciaFixtures extends Fixture
{
    
    public function load(ObjectManager $manager)
    {
        $localidad = $this->getReference('localidad');
        $provincia = new Provincia();
        $provincia->setNombre('provincia' . uniqid());
        $provincia->setLocalidades(new ArrayCollection([$localidad]));
        
        $this->addReference('provincia', $provincia);
        
        $manager->persist($provincia);
        
        $manager->flush();
    }
    
    public function getDependencies()
    {
        return [
            'ApiV1Bundle\DataFixtures\ORM\LocalidadFixtures',
        ];
    }
    
}