<?php
namespace ApiV1Bundle\Tests\Entity;

use ApiV1Bundle\Entity\HorarioAtencion;
use ApiV1Bundle\Repository\PuntoAtencionRepository;

/**
 * Class HorarioAtencionEntityTest
 * @package ApiV1Bundle\Tests\Entity
 */
class HorarioAtencionEntityTest extends EntityTestCase
{
    /** @var PuntoAtencionRepository */
    private $puntoAtencionRepo;

    public function setUp()
    {
        parent::setUp();
        $this->puntoAtencionRepo = $this->em->getRepository('ApiV1Bundle:PuntoAtencion');
        $this->horarioAtencionRepo = $this->em->getRepository('ApiV1Bundle:HorarioAtencion');
    }

    /**
     * Test CREATE
     * @return number
     */
    public function testCreate()
    {
        $horaInicio = new \DateTime('09:00');
        $horaFin = new \DateTime('12:00');
        $diaSemana  = 1;
        $idRow = 1000;
        $puntoAtencion = $this->puntoAtencionRepo->findOneBy(['direccion' => 'Calle falsa 123']);

        // crear horario de atencion
        $horarioAtencion = new HorarioAtencion(
            $puntoAtencion,
            $diaSemana,
            $horaInicio,
            $horaFin,
            $idRow
        );

        $this->em->persist($horarioAtencion);
        // test
        $this->assertEquals($horaInicio->format('H:i'), $horarioAtencion->getHoraInicio()->format('H:i'));
        $this->assertEquals($horaFin->format('H:i'), $horarioAtencion->getHoraFin()->format('H:i'));
        $this->assertEquals(1, $horarioAtencion->getDiaSemana());
        $this->assertEquals($puntoAtencion->getId(), $horarioAtencion->getPuntoAtencion()->getId());
        $this->assertEquals(1000, $horarioAtencion->getIdRow());
        // save
        $this->em->flush();
        // return
        return $horarioAtencion->getId();
    }

    /**
     * Test READ
     * @depends testCreate
     */
    public function testRead($id)
    {
        $horaInicio = new \DateTime('09:00');
        $horaFin = new \DateTime('12:00');
        $horarioAtencion = $this->horarioAtencionRepo->find($id);
        // test
        $this->assertEquals($horaInicio->format('H:i'), $horarioAtencion->getHoraInicio()->format('H:i'));
        $this->assertEquals($horaFin->format('H:i'), $horarioAtencion->getHoraFin()->format('H:i'));
        $this->assertEquals(1, $horarioAtencion->getDiaSemana());
        return $id;
    }

    /**
     * Test UPDATE
     * @depends testRead
     */
    public function testUpdate($id)
    {
        $horaInicio = new \DateTime('08:00');
        $horaFin = new \DateTime('11:00');
        $horarioAtencion = $this->horarioAtencionRepo->find($id);
        // update
        $horarioAtencion->setHoraInicio($horaInicio);
        $horarioAtencion->setHoraFin($horaFin);
        $horarioAtencion->setDiaSemana(2);
        $this->em->flush();
        // recover again
        $horarioAtencion = $this->horarioAtencionRepo->find($id);
        $this->assertEquals($horaInicio->format('H:i'), $horarioAtencion->getHoraInicio()->format('H:i'));
        $this->assertEquals($horaFin->format('H:i'), $horarioAtencion->getHoraFin()->format('H:i'));
        $this->assertEquals(2, $horarioAtencion->getDiaSemana());
        return $id;
    }

    /**
     * Test DELETE
     * @depends testUpdate
     */
    public function testDelete($id)
    {
        $horarioAtencion = $this->horarioAtencionRepo->find($id);
        $this->em->remove($horarioAtencion);
        // save
        $this->em->flush();
        // recover again
        $horarioAtencion = $this->horarioAtencionRepo->find($id);
        $this->assertNotEquals(null, $horarioAtencion->getFechaBorrado());
    }
}

