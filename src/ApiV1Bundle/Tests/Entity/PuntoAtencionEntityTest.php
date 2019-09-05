<?php
namespace ApiV1Bundle\Tests\Entity;

use ApiV1Bundle\Entity\PuntoAtencion;
use ApiV1Bundle\Repository\AreaRepository;
use ApiV1Bundle\Repository\LocalidadRepository;
use ApiV1Bundle\Repository\OrganismoRepository;
use ApiV1Bundle\Repository\ProvinciaRepository;
use ApiV1Bundle\Repository\PuntoAtencionRepository;

/**
 * Class PuntoAtencionEntityTest
 * @package ApiV1Bundle\Tests\Entity
 */
class PuntoAtencionEntityTest extends EntityTestCase
{
    /** @var AreaRepository */
    private $areaRepo;
    /** @var OrganismoRepository */
    private $organismoRepo;
    /** @var PuntoAtencionRepository */
    private $puntoatencionRepo;
    /** @var ProvinciaRepository */
    private $provinciaRepo;
    /** @var LocalidadRepository */
    private $localidadRepo;

    public function setUp()
    {
        parent::setUp();
        $this->organismoRepo = $this->em->getRepository('ApiV1Bundle:Organismo');
        $this->areaRepo = $this->em->getRepository('ApiV1Bundle:Area');
        $this->puntoatencionRepo = $this->em->getRepository('ApiV1Bundle:PuntoAtencion');
        $this->provinciaRepo = $this->em->getRepository('ApiV1Bundle:Provincia');
        $this->localidadRepo = $this->em->getRepository('ApiV1Bundle:Localidad');
    }

    /**
     * Test punto atencion CREATE
     * @return number
     */
    public function testCreate()
    {
        $organismo = $this->organismoRepo->find(1);
        $area = $this->areaRepo->findOneByOrganismo($organismo);
        $provincia = $this->provinciaRepo->find(10);
        $localidad = $this->localidadRepo->find(1308);

        // punto atencion
        $puntoAtencion = new PuntoAtencion(
            'Punto de atencion de Test',
            'Calle falsa 123'
        );
        $puntoAtencion->setArea($area);
        $puntoAtencion->setLatitud(-34.6033);
        $puntoAtencion->setLongitud(-58.3816);
        $puntoAtencion->setProvincia($provincia);
        $puntoAtencion->setLocalidad($localidad);
        $puntoAtencion->setEstado(1);
        $this->em->persist($puntoAtencion);
        // test
        $this->assertEquals('Calle falsa 123', trim($puntoAtencion->getDireccion()));
        $this->assertEquals(-34.6033, $puntoAtencion->getLatitud());
        $this->assertEquals(-58.3816, $puntoAtencion->getLongitud());
        $this->assertEquals($provincia->getId(), $puntoAtencion->getProvincia()->getId());
        $this->assertEquals($localidad->getId(), $puntoAtencion->getLocalidad()->getId());
        $this->assertEquals(1, trim($puntoAtencion->getEstado()));
        // save
        $this->em->flush();
        // return
        return $puntoAtencion->getId();
    }

    /**
     * Test punto atencion READ
     * @depends testCreate
     */
    public function testRead($id)
    {
        $puntoAtencion = $this->puntoatencionRepo->find($id);
        // test
        $this->assertEquals('Calle falsa 123', trim($puntoAtencion->getDireccion()));
        $this->assertEquals(-34.6033, $puntoAtencion->getLatitud());
        $this->assertEquals($puntoAtencion->getLongitud(), -58.3816);
        $this->assertEquals(1, trim($puntoAtencion->getEstado()));
        // return
        return $id;
    }

    /**
     * Test punto atencion UPDATE
     * @depends testRead
     */
    public function testUpdate($id)
    {
        $provincia = $this->provinciaRepo->find(10);
        $localidad = $this->localidadRepo->find(1308);
        // update
        $puntoAtencion = $this->puntoatencionRepo->find($id);
        $puntoAtencion->setDireccion('Calle falsa 456');
        $puntoAtencion->setLatitud(-34.9213);
        $puntoAtencion->setLongitud(-57.9543);
        $puntoAtencion->setProvincia($provincia);
        $puntoAtencion->setLocalidad($localidad);
        $puntoAtencion->setEstado(0);
        // save
        $this->em->flush();
        // recover again
        $puntoAtencion = $this->puntoatencionRepo->find($id);
        $this->assertEquals('Calle falsa 456', trim($puntoAtencion->getDireccion()));
        $this->assertEquals(-34.9213, $puntoAtencion->getLatitud());
        $this->assertEquals(-57.9543, $puntoAtencion->getLongitud());
        $this->assertEquals($provincia->getId(), $puntoAtencion->getProvincia()->getId());
        $this->assertEquals($localidad->getId(), $puntoAtencion->getLocalidad()->getId());
        $this->assertEquals(0, trim($puntoAtencion->getEstado()));
        // return
        return $id;
    }

    /**
     * Test punto atencion DELETE
     * @depends testUpdate
     */
    public function testDelete($id)
    {
        $puntoAtencion = $this->puntoatencionRepo->find($id);
        $this->em->remove($puntoAtencion);
        // save
        $this->em->flush();
        // recover again
        $puntoAtencion = $this->puntoatencionRepo->find($id);
        $this->assertNotEquals(null, $puntoAtencion->getFechaBorrado());
    }
}
