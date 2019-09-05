<?php
namespace ApiV1Bundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use ApiV1Bundle\Entity\PuntoAtencion;

/**
 * Seeder para la carga de puntos de atención
 *
 * Class SntDatabasePuntosatencionCommand
 * @package ApiV1Bundle\Command
 */

class SntSeederPuntosatencionCommand extends ContainerAwareCommand
{
    /**
     *  Método de configuración del Seeder
     */
    protected function configure()
    {
        $this->setName('snt:seeder:puntosatencion');
        $this->setDescription('Seeder de los puntos de atención');
        $this->setHelp('Este comando llena la base de datos con puntos de atención de prueba');
    }
    
    /**
     * Método de ejecución del seeder
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $doctrine = $this->getContainer()->get('doctrine');
        $em = $doctrine->getManager();
        
        $io->title('Generando puntos de atención');
        // provincias
        $provincias = [];
        $provinciaRepository = $em->getRepository('ApiV1Bundle:Provincia');
        foreach ($provinciaRepository->findAll() as $provincia) {
            $provincias[] = $provincia;
        }
        // areas
        $areas = [];
        $areaRepository = $em->getRepository('ApiV1Bundle:Area');
        foreach ($areaRepository->findAll() as $area) {
            $areas[] = $area;
        }
        // generamos 25 puntos de atención random
        for ($i = 1; $i <= 25; $i++) {
            // provincia
            $provincia = $this->getRandProvincia($provincias);
            // localidad
            $localidad = $this->getLocalidad($provincia->getLocalidades());
            // area
            $area = $this->getArea($areas);
            // punto de atención
            $io->text('Generando punto de atención para ' . $area->getNombre());
            $puntoAtencion = new PuntoAtencion(
                'pda::' . $area->getNombre() . '::' . str_pad($i, 3, 0, STR_PAD_LEFT),
                'Calle falsa 123',
                '9 a 18'
            );
            $puntoAtencion->setArea($area);
            $puntoAtencion->setLatitud(-34.6033);
            $puntoAtencion->setLongitud(-58.3816);
            $puntoAtencion->setProvincia($provincia);
            $puntoAtencion->setLocalidad($localidad);
            $puntoAtencion->setEstado(1);
            $em->persist($puntoAtencion);
        }
        $em->flush();
        $io->text('Done!');
    }
    
    /**
     * Obtener una provincia random
     * 
     * @param $repository
     * @param $total
     * @return unknown
     */
    private function getRandProvincia($provincias)
    {
        return $provincias[rand(0, count($provincias) - 1)];
    }
    
    /**
     * Obtener una localidad random en base a una provincia
     * 
     * @param $repository
     * @param $provinciaId
     * @param $total
     * @return unknown
     */
    private function getLocalidad($localidades)
    {
        return $localidades[rand(0, count($localidades) - 1)];
    }
    
    /**
     * Obtener una area random
     * 
     * @param $repository
     * @param $total
     * @return unknown
     */
    private function getArea($areas)
    {
        return $areas[rand(0, count($areas) - 1)];
    }

}
