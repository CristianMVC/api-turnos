<?php
namespace ApiV1Bundle\Tests\Entity;

use ApiV1Bundle\Entity\Localidad;
use ApiV1Bundle\Repository\LocalidadRepository;
use ApiV1Bundle\Repository\ProvinciaRepository;

/**
 * Class LocalidadEntityTest
 * @package ApiV1Bundle\Tests\Entity
 */
class LocalidadEntityTest extends EntityTestCase
{
    /** @var ProvinciaRepository */
    private $provinciaRepo;
    /** @var LocalidadRepository */
    private $localidadRepo;

    public function setUp()
    {
        parent::setUp();
        $this->provinciaRepo = $this->em->getRepository('ApiV1Bundle:Provincia');
        $this->localidadRepo = $this->em->getRepository('ApiV1Bundle:Localidad');
    }

    /**
     * Test localidad CREATE
     * @return number
     */
    public function testCreate()
    {
        $provincia = $this->provinciaRepo->find(1);
        // create
        $localidad = new Localidad();
        $localidad->setProvincia($provincia);
        $localidad->setNombre('Azul');
        $this->em->persist($localidad);
        // test
        $this->assertEquals('Azul', trim($localidad->getNombre()));
        // save
        $this->em->flush();
        // return
        $id = $localidad->getId();
        return $id;
    }

    /**
     * Test localidad READ
     * @depends testCreate
     */
    public function testRead($id)
    {
        $localidad = $this->localidadRepo->find($id);
        $this->assertEquals('Azul', trim($localidad->getNombre()));
        return $id;
    }

    /**
     * Test localidad UPDATE
     * @depends testRead
     */
    public function testUpdate($id)
    {
        // update
        $localidad = $this->localidadRepo->find($id);
        $localidad->setNombre('Bragado');
        // save
        $this->em->flush();
        // recover again
        $localidad = $this->localidadRepo->find($id);
        $this->assertEquals('Bragado', trim($localidad->getNombre()));
        return $id;
    }

    /**
     * Test localidad DELETE
     * @depends testUpdate
     */
    public function testDelete($id)
    {
        $localidad = $this->localidadRepo->find($id);
        $this->em->remove($localidad);
        // save
        $this->em->flush();
        // recover again
        $localidad = $this->localidadRepo->find($id);
        $this->assertEquals(null, $localidad);
    }
}
