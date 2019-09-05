<?php
namespace ApiV1Bundle\Tests\Entity;

use ApiV1Bundle\Entity\Area;
use ApiV1Bundle\Repository\AreaRepository;
use ApiV1Bundle\Repository\OrganismoRepository;

/**
 * Class AreaEntityTest
 * @package ApiV1Bundle\Tests\Entity
 */
class AreaEntityTest extends EntityTestCase
{
    /** @var AreaRepository */
    private $areaRepo;
    /** @var OrganismoRepository */
    private $organismoRepo;

    public function setUp()
    {
        parent::setUp();
        $this->organismoRepo = $this->em->getRepository('ApiV1Bundle:Organismo');
        $this->areaRepo = $this->em->getRepository('ApiV1Bundle:Area');
    }

    /**
     * Test area CREATE
     * @return number
     */
    public function testCreate()
    {
        // create organismo
        $organismo = $this->organismoRepo->find(1);
        // create area
        $area = new Area('Area de prueba', 'ADP');
        $area->setOrganismo($organismo);
        $this->em->persist($area);
        // test
        $this->assertEquals('Area de prueba', trim($area->getNombre()));
        $this->assertEquals('ADP', trim($area->getAbreviatura()));
        $this->assertEquals($organismo->getId(), $area->getOrganismo()->getId());
        // save
        $this->em->flush();
        // return
        return $area->getId();
    }

    /**
     * Test organismo READ
     * @depends testCreate
     */
    public function testRead($id)
    {
        $area = $this->areaRepo->find($id);
        $this->assertEquals('Area de prueba', trim($area->getNombre()));
        $this->assertEquals('ADP', trim($area->getAbreviatura()));
        return $id;
    }

    /**
     * Test organismo UPDATE
     * @depends testRead
     */
    public function testUpdate($id)
    {
        // update
        $area = $this->areaRepo->find($id);
        $area->setNombre('Otra area de prueba');
        $area->setAbreviatura('OADP');
        // save
        $this->em->flush();
        // recover again
        $area = $this->areaRepo->find($id);
        $this->assertEquals('Otra area de prueba', trim($area->getNombre()));
        $this->assertEquals('OADP', trim($area->getAbreviatura()));
        return $id;
    }

    /**
     * Test area DELETE
     * @depends testUpdate
     */
    public function testDelete($id)
    {
        $area = $this->areaRepo->find($id);
        $this->em->remove($area);
        // save
        $this->em->flush();
        // recover again
        $area = $this->areaRepo->find($id);

        $this->assertNotEquals(null, $area->getFechaBorrado());
    }
}
