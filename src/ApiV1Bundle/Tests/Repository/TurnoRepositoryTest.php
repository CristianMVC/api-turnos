<?php

namespace ApiV1Bundle\Tests\Repository;

use ApiV1Bundle\DataFixtures\ORM\TurnoFixtures;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TurnoRepositoryTest extends KernelTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;
    
    /**
     * @var \ApiV1Bundle\Repository\TurnoRepository
     */
    private $turnoRepository;
    
    /**
     * @var \ApiV1Bundle\Repository\PuntoAtencionRepository
     */
    private $puntoAtencionRepository;
    
    
    /**
     * @var \Doctrine\Common\DataFixtures\Executor\ORMExecutor
     */
    private $executor;
    
    /**
     * @var \ApiV1Bundle\DataFixtures\ORM\TurnoFixtures
     */
    private $turnoFixtures;
    
    /**
     * @var \Doctrine\Common\DataFixtures\Loader
     */
    private $loader;
    
    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        self::bootKernel();
        parent::setUp();
        
        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager()
        ;
    
        $this->executor = new ORMExecutor($this->entityManager, new ORMPurger());
        $this->loader = new Loader();
        
        $this->turnoRepository = $this->entityManager
            ->getRepository('ApiV1Bundle:Turno');
        
        $this->turnoFixtures = new TurnoFixtures();
    
        $this->loader->addFixture($this->turnoFixtures);
    }
    
    public function testFiltrarPorArea()
    {
        $this->executor->execute($this->loader->getFixtures(), true);
        
        $where = ['areaId' => $this->turnoFixtures->turno1->getPuntoAtencion()->getArea()->getId()];
        
        $turnos = $this->turnoRepository->findAllPaginate(0, 20, $where);

        $this->assertCount(1, $turnos);
        $this->assertEquals($this->turnoFixtures->turno1->getId(), current($turnos)->getId());
    }
    
    public function testFiltrarDesdeFecha()
    {
        $this->executor->execute($this->loader->getFixtures(), true);
        
        $where = ['fechaDesde' => $this->turnoFixtures->turno2->getFecha()->format('Y-m-d')];
        
        $resultados = $this->turnoRepository->findAllPaginate(0, 5000, $where);
    
        $turnos = array_filter($resultados, function($turno) {
        
            return $turno->getId() == $this->turnoFixtures->turno2->getId();
        
        });
    
        $this->assertCount(1, $turnos);
    
        $this->assertEquals($this->turnoFixtures->turno2->getId(), current($turnos)->getId());
    }
    
    public function testFiltrarHastaFecha()
    {
        $this->executor->execute($this->loader->getFixtures(), true);
        
        $where = ['fechaHasta' => $this->turnoFixtures->turno1->getFecha()->format('Y-m-d')];
        
        $resultados = $this->turnoRepository->findAllPaginate(0, 5000, $where);
        
        $turnos = array_filter($resultados, function($turno) {
            
            return $turno->getId() == $this->turnoFixtures->turno1->getId();
            
        });
        
        $this->assertCount(1, $turnos);
        $this->assertEquals($this->turnoFixtures->turno1->getId(), current($turnos)->getId());
    }
    
    public function testFiltrarPorFechaYArea()
    {
        $this->executor->execute($this->loader->getFixtures(), true);
        
        $where = [
            'areaId' => $this->turnoFixtures->turno1->getPuntoAtencion()->getArea()->getId(),
            'fechaDesde' => $this->turnoFixtures->turno1->getFecha()->format('Y-m-d'),
        ];
        
        $turnos = $this->turnoRepository->findAllPaginate(0, 5000, $where);
        
        $this->assertCount(1, $turnos);
        $this->assertEquals($this->turnoFixtures->turno1->getId(), current($turnos)->getId());
        $this->assertEquals($this->turnoFixtures->turno1->getPuntoAtencion()->getArea()->getId(), current($turnos)->getPuntoAtencion()->getArea()->getId());
    }
    
    public function testFiltrarPorEstado()
    {
        $this->executor->execute($this->loader->getFixtures(), true);
        
        $where = ['estado' => $this->turnoFixtures->turno1->getEstado()];
        
        $resultados = $this->turnoRepository->findAllPaginate(0, 5000, $where);
        
        $turnos = array_filter($resultados, function($turno) {
            
            return $turno->getId() == $this->turnoFixtures->turno1->getId();
            
        });
        
        $this->assertCount(1, $turnos);
        $this->assertEquals($this->turnoFixtures->turno1->getId(), current($turnos)->getId());
    
        $where = ['estado' => 2];
    
        $resultados = $this->turnoRepository->findAllPaginate(0, 5000, $where);
    
        $turnos = array_filter($resultados, function($turno) {
        
            return $turno->getId() == $this->turnoFixtures->turno1->getId();
        
        });
    
        // El turno no debe estar entre los resultados por lo cual se espera que el tamaño del array sea 0
        $this->assertCount(0, $turnos);
    
        $where = ['estado' => 0];
    
        $resultados = $this->turnoRepository->findAllPaginate(0, 5000, $where);
    
        $turnos = array_filter($resultados, function($turno) {
        
            return $turno->getId() == $this->turnoFixtures->turno1->getId();
        
        });
    
        // El turno no debe estar entre los resultados por lo cual se espera que el tamaño del array sea 0
        $this->assertCount(0, $turnos);
    }
    
    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();
        
        $this->entityManager->close();
        $this->entityManager = null; // avoid memory leaks
    }
}