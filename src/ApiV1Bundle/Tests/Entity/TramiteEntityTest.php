<?php
namespace ApiV1Bundle\Tests\Entity;

use ApiV1Bundle\Entity\Tramite;
use ApiV1Bundle\Entity\Formulario;
use ApiV1Bundle\Repository\AreaRepository;
use ApiV1Bundle\Repository\FormularioRepository;
use ApiV1Bundle\Repository\PuntoAtencionRepository;
use ApiV1Bundle\Repository\TramiteRepository;

/**
 * Class TramiteEntityTest
 * @package ApiV1Bundle\Tests\Entity
 */
class TramiteEntityTest extends EntityTestCase
{
    /** @var AreaRepository */
    private $areaRepo;
    /** @var PuntoAtencionRepository */
    private $puntoatencionRepo;
    /** @var FormularioRepository */
    private $formularioRepo;
    /** @var TramiteRepository */
    private $tramiteRepo;

    public function setUp()
    {
        parent::setUp();
        $this->areaRepo = $this->em->getRepository('ApiV1Bundle:Area');
        $this->puntoatencionRepo = $this->em->getRepository('ApiV1Bundle:PuntoAtencion');
        $this->formularioRepo = $this->em->getRepository('ApiV1Bundle:Formulario');
        $this->tramiteRepo = $this->em->getRepository('ApiV1Bundle:Tramite');
    }

    /**
     * Test tramite CREATE
     * @return number
     */
    public function testCreate()
    {
        $area = $this->areaRepo->find(1);
        $formulario = $this->formularioRepo->find(1);
        $puntoAtencion = $this->puntoatencionRepo->find(1);
        // create
        $formulario = new Formulario(['lorem' => 'ipsum']);
        // create
        $tramite = new Tramite('Tramite de prueba', 1, $area);
        $tramite->setFormulario($formulario);
        $tramite->addPuntosAtencion($puntoAtencion);
        $tramite->setDuracion(10);
        $tramite->setRequisitos('Lorem ipsum dolor sit amet');
        $tramite->setExcepcional(1);
        $this->em->persist($tramite);
        // test
        $this->assertEquals('Tramite de prueba', trim($tramite->getNombre()));
        $this->assertEquals('Lorem ipsum dolor sit amet', trim($tramite->getRequisitos()));
        $this->assertEquals(10, $tramite->getDuracion());
        $this->assertEquals(1, $tramite->getVisibilidad());
        $this->assertEquals(1, $tramite->getExcepcional());
        $this->assertEquals(['lorem' => 'ipsum'], $tramite->getFormulario()->getCampos());
        // save
        $this->em->flush();
        // return
        $id = $tramite->getId();
        return $id;
    }

    /**
     * Test tramite READ
     * @depends testCreate
     */
    public function testRead($id)
    {
        $tramite = $this->tramiteRepo->find($id);
        $this->assertEquals('Tramite de prueba', trim($tramite->getNombre()));
        $this->assertEquals(10, $tramite->getDuracion());
        $this->assertEquals('Lorem ipsum dolor sit amet', $tramite->getRequisitos());
        $this->assertEquals(1, $tramite->getVisibilidad());
        $this->assertEquals(1, $tramite->getExcepcional());
        $this->assertEquals(['lorem' => 'ipsum'], $tramite->getFormulario()->getCampos());
        return $id;
    }

    /**
     * Test tramite UPDATE
     * @depends testRead
     */
    public function testUpdate($id)
    {
        // update
        $tramite = $this->tramiteRepo->find($id);
        $tramite->setNombre('Otro tramite de prueba');
        $tramite->setDuracion(25);
        $tramite->setRequisitos('Aliquam et augue massa');
        $tramite->setVisibilidad(0);
        $tramite->setExcepcional(0);
        // save
        $this->em->flush();
        // recover again
        $tramite = $this->tramiteRepo->find($id);
        $this->assertEquals('Otro tramite de prueba', trim($tramite->getNombre()));
        $this->assertEquals(25, $tramite->getDuracion());
        $this->assertEquals('Aliquam et augue massa', $tramite->getRequisitos());
        $this->assertEquals(0, $tramite->getVisibilidad());
        $this->assertEquals(0, $tramite->getExcepcional());
        $this->assertEquals(['lorem' => 'ipsum'], $tramite->getFormulario()->getCampos());
        return $id;
    }

    /**
     * Test tramite DELETE
     * @depends testUpdate
     */
    public function testDelete($id)
    {
        $tramite = $this->tramiteRepo->find($id);
        $this->em->remove($tramite);
        // save
        $this->em->flush();
        // recover again
        $tramite = $this->tramiteRepo->find($id);
        $this->assertNotEquals(null, $tramite->getFechaBorrado());
    }

    /**
     * Test no tramites encontrados
     */
    public function testFindZeroTramitePaginate()
    {
        $result = $this->tramiteRepo->findAllTramitePaginate('dfhfjefyjej', 10, 1);
        $this->assertEquals(0, count($result));
    }

    /**
     * Test un solo tramite encontrado
     */
    public function testFindOneTramitePaginate()
    {
        $result = $this->tramiteRepo->findAllTramitePaginate('viudez', 10, 1);
        $this->assertEquals(0, count($result));
    }
}
