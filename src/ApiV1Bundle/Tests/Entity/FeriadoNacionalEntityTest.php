<?php
namespace ApiV1Bundle\Tests\Entity;

use ApiV1Bundle\Entity\FeriadoNacional;
use ApiV1Bundle\Repository\AreaRepository;
use ApiV1Bundle\Repository\OrganismoRepository;

/**
 * Class FeriadoNacionalEntityTest
 * @package ApiV1Bundle\Tests\Entity
 */
class FeriadoNacionalEntityTest extends EntityTestCase
{
    /** @var AreaRepository */
    private $areaRepo;
    /** @var OrganismoRepository */
    private $organismoRepo;

    public function setUp()
    {
        parent::setUp();
        $this->feriadoNacionalRepo = $this->em->getRepository('ApiV1Bundle:FeriadoNacional');
    }

    /**
     * Test Feriado Nacional CREATE
     * @return \DateTime
     */
    public function testCreate()
    {
        $fecha = new \DateTime('now');
        // create feriado nacional
        //$feriado = $this->feriadoNacionalRepo->findOneBy(array('fecha' => $fecha));

        $feriado = new FeriadoNacional($fecha);

        // save
        $this->em->persist($feriado);
        $this->em->flush();

        //get feriado persisted
        $feriadoPersisted = $this->feriadoNacionalRepo->findOneBy(['fecha' => $fecha]);

        // test
        $this->assertEquals($feriadoPersisted->getFecha()->format('Y-m-d'), $feriado->getFecha()->format('Y-m-d'));
        // return
        return $feriado->getFecha();
    }

    /**
     * Test Feriado Nacional Delete
     * @depends testCreate
     */
    public function testDelete($fecha)
    {
        $feriado = $this->feriadoNacionalRepo->findOneBy(['fecha' => $fecha]);
        $this->em->remove($feriado);
        // save
        $this->em->flush();
        // recover again
        $feriado = $this->feriadoNacionalRepo->findOneBy(['fecha' => $fecha]);
        $this->assertEquals(null, $feriado);
    }
}