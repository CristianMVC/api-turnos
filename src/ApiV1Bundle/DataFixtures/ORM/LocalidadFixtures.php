<?php

namespace ApiV1Bundle\DataFixtures\ORM;

use ApiV1Bundle\Entity\Localidad;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class LocalidadFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $localidad = new Localidad();
        $localidad->setNombre('localidad');
        $this->addReference('localidad', $localidad);
        $manager->persist($localidad);
        
        $manager->flush();
    }
}