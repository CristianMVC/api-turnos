<?php

namespace ApiV1Bundle\DataFixtures\ORM;

use ApiV1Bundle\Entity\Disponibilidad;
use ApiV1Bundle\Entity\GrupoTramite;
use ApiV1Bundle\Entity\HorarioAtencion;
use ApiV1Bundle\Entity\PuntoAtencion;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class PuntoAtencionFixtures extends Fixture
{
    
    public $puntoAtencion;
    public $puntoAtencion2;
    
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
    
        $this->puntoAtencion = new PuntoAtencion('nombre', 'direccion');
    
        $area = $this->getReference('area1');
        $area2 = $this->getReference('area2');
        $tramite1 = $this->getReference('tramite1');
        $tramite2 = $this->getReference('tramite2');
        $provincia = $this->getReference('provincia');
        $localidad = $this->getReference('localidad');
    
        $this->puntoAtencion->setProvincia($provincia);
        $this->puntoAtencion->setLocalidad($localidad);
        $this->puntoAtencion->setArea($area);
        $this->puntoAtencion->setLatitud(1);
        $this->puntoAtencion->setLongitud(2);
        $this->puntoAtencion->setEstado(1);
        
        $grupo = new GrupoTramite($this->puntoAtencion, 'Grupo 1', 1, 1,5);
        $grupo->addTramite($tramite1);
        
        $horario = new HorarioAtencion($this->puntoAtencion, 1, new \DateTime('now'), new \DateTime('now'), 1);
    
        $disponibilidad = new Disponibilidad($this->puntoAtencion, $grupo, $horario, 10);
        
        $this->addReference('punto', $this->puntoAtencion);
    
        $manager->persist($this->puntoAtencion);
        $manager->persist($grupo);
        $manager->persist($horario);
        $manager->persist($disponibilidad);
    
        
        $this->puntoAtencion2 = new PuntoAtencion('PTO 2', 'Dirección 123');
        $this->puntoAtencion2->setProvincia($provincia);
        $this->puntoAtencion2->setLocalidad($localidad);
        $this->puntoAtencion2->setArea($area2);
        $this->puntoAtencion2->setLatitud(100);
        $this->puntoAtencion2->setLongitud(200);
        $this->puntoAtencion2->setEstado(1);
    
        $grupo2 = new GrupoTramite($this->puntoAtencion2, 'Grupo 1', 1, 1,5);
        $grupo2->addTramite($tramite2);
    
        $disponibilidad2 = new Disponibilidad($this->puntoAtencion2, $grupo2, $horario, 20);
    
        $manager->persist($grupo2);
        $manager->persist($horario);
        $manager->persist($disponibilidad2);
        $manager->persist($this->puntoAtencion2);
    
        $this->addReference('punto-2', $this->puntoAtencion2);
        
    
        $manager->flush();
        
    }
    
    public function getDependencies()
    {
        return [
            'ApiV1Bundle\DataFixtures\ORM\AreaFixtures',
            'ApiV1Bundle\DataFixtures\ORM\TramiteFixtures',
            'ApiV1Bundle\DataFixtures\ORM\ProvinciaFixtures',
        ];
    }
    
}