<?php
namespace ApiV1Bundle\Tests\Entity;

use ApiV1Bundle\Entity\Provincia;
use ApiV1Bundle\Repository\ProvinciaRepository;

/**
 * Class ProvinciaEntityTest
 * @package ApiV1Bundle\Tests\Entity
 */
class ProvinciaEntityTest extends EntityTestCase
{
    /** @var ProvinciaRepository */
    private $provinciaRepo;
    /** @var string */
    private $randomName;

    public function setUp()
    {
        parent::setUp();
        $this->provinciaRepo = $this->em->getRepository('ApiV1Bundle:Provincia');
        $this->randomName = 'Provincia Test ' . date('Y-m-d H');
    }

    /**
     * Test provincia CREATE
     * @return number
     */
    public function testCreate()
    {
        // create
        $provincia = new Provincia();
        $provincia->setNombre($this->randomName);
        $this->em->persist($provincia);
        // test
        $this->assertEquals($this->randomName, trim($provincia->getNombre()));
        // save
        $this->em->flush();
        // return
        $id = $provincia->getId();
        return $id;
    }

    /**
     * Test provincia READ
     * @depends testCreate
     */
    public function testRead($id)
    {
        $provincia = $this->provinciaRepo->find($id);
        $this->assertEquals($this->randomName, trim($provincia->getNombre()));
        return $id;
    }

    /**
     * Test provincia UPDATE
     * @depends testRead
     */
    public function testUpdate($id)
    {
        // update
        $provincia = $this->provinciaRepo->find($id);
        $provincia->setNombre($this->randomName . '2');
        // save
        $this->em->flush();
        // recover again
        $provincia = $this->provinciaRepo->find($id);
        $this->assertEquals($this->randomName . '2', trim($provincia->getNombre()));
        return $id;
    }

    /**
     * Test provincia DELETE
     * @depends testUpdate
     */
    public function testDelete($id)
    {
        $provincia = $this->provinciaRepo->find($id);
        $this->em->remove($provincia);
        // save
        $this->em->flush();
        // recover again
        $provincia = $this->provinciaRepo->find($id);
        $this->assertEquals(null, $provincia);
    }
}
