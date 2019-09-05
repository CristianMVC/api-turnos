<?php

namespace ApiV1Bundle\DataFixtures\ORM;

use ApiV1Bundle\Entity\Area;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AreaFixtures extends Fixture
{
    
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $area1 = new Area('Área 1', 'A1');
        $area2 = new Area('Área 1', 'A2');
        
        $this->addReference('area1', $area1);
        $this->addReference('area2', $area2);
    
        $manager->persist($area1);
        $manager->persist($area2);
    
        $manager->flush();
        
    }
    
}