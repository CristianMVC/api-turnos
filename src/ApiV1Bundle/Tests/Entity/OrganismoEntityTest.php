<?php
namespace ApiV1Bundle\Tests\Entity;

use ApiV1Bundle\Entity\Organismo;
use ApiV1Bundle\Repository\OrganismoRepository;

/**
 * Class OrganismoEntityTest
 * @package ApiV1Bundle\Tests\Entity
 */
class OrganismoEntityTest extends EntityTestCase
{
    /** @var OrganismoRepository */
    private $organismoRepo;

    public function setUp()
    {
        parent::setUp();
        $this->organismoRepo = $this->em->getRepository('ApiV1Bundle:Organismo');
    }

    /**
     * Test organismo CREATE
     * @return number
     */
    public function testCreate()
    {
        // create
        $organismo = new Organismo('Organismo de prueba', 'ODP');
        $this->em->persist($organismo);
        // test
        $this->assertEquals('Organismo de prueba', trim($organismo->getNombre()));
        $this->assertEquals('ODP', trim($organismo->getAbreviatura()));
        // save
        $this->em->flush();
        // return
        $id = $organismo->getId();
        return $id;
    }

    /**
     * Test organismo READ
     * @depends testCreate
     */
    public function testRead($id)
    {
        $organismo = $this->organismoRepo->find($id);
        $this->assertEquals('Organismo de prueba', trim($organismo->getNombre()));
        $this->assertEquals('ODP', trim($organismo->getAbreviatura()));
        return $id;
    }

    /**
     * Test organismo UPDATE
     * @depends testRead
     */
    public function testUpdate($id)
    {
        // update
        $organismo = $this->organismoRepo->find($id);
        $organismo->setNombre('Otro organismo de prueba');
        $organismo->setAbreviatura('OODP');
        // save
        $this->em->flush();
        // recover again
        $organismo = $this->organismoRepo->find($id);
        $this->assertEquals('Otro organismo de prueba', trim($organismo->getNombre()));
        $this->assertEquals('OODP', trim($organismo->getAbreviatura()));
        return $id;
    }

    /**
     * Test organismo DELETE
     * @depends testUpdate
     */
    public function testDelete($id)
    {
        $organismo = $this->organismoRepo->find($id);
        $this->em->remove($organismo);
        // save
        $this->em->flush();
        // recover again
        $organismo = $this->organismoRepo->find($id);
        $this->assertNotEquals(null, $organismo->getFechaBorrado());
    }
}
