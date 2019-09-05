<?php
namespace ApiV1Bundle\Tests\Entity;

use ApiV1Bundle\Entity\Turno;
use ApiV1Bundle\Entity\DatosTurno;
use ApiV1Bundle\Repository\PuntoAtencionRepository;
use ApiV1Bundle\Repository\TramiteRepository;
use ApiV1Bundle\Repository\TurnoRepository;

/**
 * Class TurnoEntityTest
 * @package ApiV1Bundle\Tests\Entity
 */
class TurnoEntityTest extends EntityTestCase
{
    /** @var PuntoAtencionRepository */
    private $puntoatencionRepo;
    /** @var TramiteRepository */
    private $tramiteRepo;
    /** @var TurnoRepository */
    private $turnoRepo;

    public function setUp()
    {
        parent::setUp();
        $this->puntoatencionRepo = $this->em->getRepository('ApiV1Bundle:PuntoAtencion');
        $this->tramiteRepo = $this->em->getRepository('ApiV1Bundle:Tramite');
        $this->turnoRepo = $this->em->getRepository('ApiV1Bundle:Turno');
    }

    /**
     * Test turno CREATE
     * @return number
     */
    public function testCreate()
    {
        $puntoAtencion = $this->puntoatencionRepo->find(1);
        $tramite = $this->tramiteRepo->find(2);
        $fecha = new \DateTime('2017-01-01');
        $hora = new \DateTime('19:00');
        // create datos turno
        $datosTurno = new DatosTurno(
            'Juan',
            'Perez',
            93941676,
            'nowhere@example.com',
            123456
        );
        $datosTurno->setCampos(['lorem' => 'ipsum', 'dolor' => 'sit amet']);

        // create turno
        $turno = new Turno($puntoAtencion, $tramite, $fecha, $hora);
        $turno->setAlerta(1);
        $turno->setDatosTurno($datosTurno);

        $this->em->persist($turno);
        // test
        $this->assertNotEquals(null, $turno->getCodigo());
        $this->assertEquals($fecha->format('Y-m-d'), $turno->getFecha()->format('Y-m-d'));
        $this->assertEquals($hora->format('H:i:s'), $turno->getHora()->format('H:i:s'));
        $this->assertEquals(0, trim($turno->getEstado()));
        $this->assertEquals(1, trim($turno->getAlerta()));
        $this->assertEquals('Juan', trim($turno->getDatosturno()->getNombre()));
        $this->assertEquals('Perez', trim($turno->getDatosturno()->getApellido()));
        $this->assertEquals(93941676, trim($turno->getDatosturno()->getCuil()));
        $this->assertEquals('nowhere@example.com', trim($turno->getDatosturno()->getEmail()));
        $this->assertEquals(['lorem' => 'ipsum', 'dolor' => 'sit amet'], $turno->getDatosturno()->getCampos());
        // save
        $this->em->flush();
        // return
        $id = $turno->getId();
        return $id;
    }

    /**
     * Test turno READ
     * @depends testCreate
     */
    public function testRead($id)
    {
        $fecha = new \DateTime('2017-01-01');
        $hora = new \DateTime('19:00');
        // read
        $turno = $this->turnoRepo->find($id);
        $this->assertEquals($fecha->format('Y-m-d'), $turno->getFecha()->format('Y-m-d'));
        $this->assertEquals($hora->format('H:i:s'), $turno->getHora()->format('H:i:s'));
        $this->assertEquals(0, $turno->getEstado());
        $this->assertEquals(1, $turno->getAlerta());
        $this->assertEquals('Juan', trim($turno->getDatosturno()->getNombre()));
        $this->assertEquals('Perez', trim($turno->getDatosturno()->getApellido()));
        $this->assertEquals(93941676, trim($turno->getDatosturno()->getCuil()));
        $this->assertEquals('nowhere@example.com', trim($turno->getDatosturno()->getEmail()));
        $this->assertEquals(['lorem' => 'ipsum', 'dolor' => 'sit amet'], $turno->getDatosturno()->getCampos());
        return $id;
    }

    /**
     * Test turno UPDATE
     * @depends testRead
     */
    public function testUpdate($id)
    {
        $fecha = new \DateTime('2017-12-12');
        $hora = new \DateTime('08:00');
        // update
        $turno = $this->turnoRepo->find($id);
        $turno->setFecha($fecha);
        $turno->setHora($hora);
        $turno->setEstado(1);
        $turno->setAlerta(2);
        // save
        $this->em->flush();
        // recover again
        $turno = $this->turnoRepo->find($id);
        $this->assertEquals($fecha->format('Y-m-d'), $turno->getFecha()->format('Y-m-d'));
        $this->assertEquals($hora->format('H:i:s'), $turno->getHora()->format('H:i:s'));
        $this->assertEquals(1, $turno->getEstado());
        $this->assertEquals(2, $turno->getAlerta());
        $this->assertEquals('Juan', trim($turno->getDatosturno()->getNombre()));
        $this->assertEquals('Perez', trim($turno->getDatosturno()->getApellido()));
        $this->assertEquals(93941676, trim($turno->getDatosturno()->getCuil()));
        $this->assertEquals('nowhere@example.com', trim($turno->getDatosturno()->getEmail()));
        $this->assertEquals(['lorem' => 'ipsum', 'dolor' => 'sit amet'], $turno->getDatosturno()->getCampos());
        return $id;
    }

    /**
     * Test turno DELETE
     * @depends testUpdate
     */
    public function testDelete($id)
    {
        $turno = $this->turnoRepo->find($id);
        $this->em->remove($turno);
        // save
        $this->em->flush();
        // recover again
        $turno = $this->turnoRepo->find($id);
        $this->assertNotEquals(null, $turno->getFechaBorrado());
    }
}
