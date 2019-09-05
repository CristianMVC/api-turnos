<?php
namespace ApiV1Bundle\Tests\Entity;

use ApiV1Bundle\ApplicationServices\PuntoAtencionServices;
use ApiV1Bundle\Entity\GrupoTramite;
use ApiV1Bundle\Entity\Disponibilidad;
use ApiV1Bundle\Repository\GrupoTramiteRepository;
use ApiV1Bundle\Repository\PuntoAtencionRepository;
use ApiV1Bundle\Repository\TramiteRepository;

/**
 * Class GrupoTramiteEntityTest
 * @package ApiV1Bundle\Tests\Entity
 */
class GrupoTramiteEntityTest extends EntityTestCase
{
    /** @var PuntoAtencionRepository */
    private $puntoAtencionRepo;
    /** @var GrupoTramiteRepository */
    private $grupoTramiteRepo;
    /** @var TramiteRepository */
    private $TramiteRepo;

    public function setUp()
    {
        parent::setUp();
        $this->puntoAtencionRepo = $this->em->getRepository('ApiV1Bundle:PuntoAtencion');
        $this->grupoTramiteRepo = $this->em->getRepository('ApiV1Bundle:GrupoTramite');
        $this->TramiteRepo = $this->em->getRepository('ApiV1Bundle:Tramite');
    }

    /**
     * Test CREATE
     * @return number
     */
    public function testCreate()
    {
        $puntoAtencion = $this->puntoAtencionRepo->find(1);
        $tramite = $this->TramiteRepo->find(2);
        // crear grupo de tramites
        $grupoTramites = new GrupoTramite($puntoAtencion, 'Grupo de tramites de prueba', 30, 15);
        $grupoTramites->addTramite($tramite);
        $this->em->persist($grupoTramites);
        // test
        $this->assertEquals('Grupo de tramites de prueba', trim($grupoTramites->getNombre()));
        $this->assertEquals(30, $grupoTramites->getHorizonte());
        $this->assertEquals($puntoAtencion->getId(), $grupoTramites->getPuntoAtencion()
            ->getId());
        // save
        $this->em->flush();
        // return
        return $grupoTramites->getId();
    }

    /**
     * Test READ
     * @depends testCreate
     */
    public function testRead($id)
    {
        $grupoTramites = $this->grupoTramiteRepo->find($id);
        // test
        $this->assertEquals('Grupo de tramites de prueba', trim($grupoTramites->getNombre()));
        $this->assertEquals(30, $grupoTramites->getHorizonte());
        return $id;
    }

    /**
     * Test UPDATE
     * @depends testRead
     */
    public function testUpdate($id)
    {
        $grupoTramites = $this->grupoTramiteRepo->find($id);
        // update
        $grupoTramites->setNombre('Grupo de tramites de prueba');
        $grupoTramites->setHorizonte(30);
        $grupoTramites->setIntervaloTiempo(30);
        // save
        $this->em->flush();
        // recover again
        $grupoTramites = $this->grupoTramiteRepo->find($id);
        $this->assertEquals('Grupo de tramites de prueba', trim($grupoTramites->getNombre()));
        $this->assertEquals(30, $grupoTramites->getHorizonte());
        return $id;
    }

    /**
     * Test DELETE
     * @depends testUpdate
     */
    public function testDelete($id)
    {
        $grupoTramites = $this->grupoTramiteRepo->find($id);
        $this->em->remove($grupoTramites);
        // save
        $this->em->flush();
        // recover again
        $grupoTramites = $this->grupoTramiteRepo->find($id);
        $this->assertNotEquals(null, $grupoTramites->getFechaBorrado());
    }
    
    /**
     Test Disponibilidad
     * @return number
     */
    public function testDisponibilidad() {
        $puntoAtencion = $this->puntoAtencionRepo->find(3);
        $tramite = $this->TramiteRepo->find(6);
        
        $grupoTramite = new GrupoTramite($puntoAtencion, 'Grupo de tramites de prueba', 30, 30);
        $grupoTramite->addTramite($tramite);
        $this->em->persist($grupoTramite);
        
        $horariosAtencion = $puntoAtencion->getHorariosAtencion();
        foreach ($horariosAtencion as $horarioAtencion){
            $disponibilidad = new Disponibilidad($puntoAtencion, $grupoTramite, $horarioAtencion, 0);
            $this->em->persist($disponibilidad);
        }
        //save
        $this->em->flush();
        
        return $disponibilidad->getCantidadTurnos();
    }
    }
