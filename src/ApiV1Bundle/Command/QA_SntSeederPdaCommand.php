<?php
namespace ApiV1Bundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use ApiV1Bundle\Entity\Area;
use ApiV1Bundle\Entity\Provincia;
use ApiV1Bundle\Entity\PuntoAtencion;
use ApiV1Bundle\ExternalServices\PuntoAtencionIntegration;
use Symfony\Component\DependencyInjection\Container;
use ApiV1Bundle\ExternalServices\SNCExternalService;
use ApiV1Bundle\Mocks\SNCExternalServiceMock;
/**
 * Class QA_SntSeederPdaCommand
 * @package ApiV1Bundle\Command
 *
 * Seeder de creacion de Punto de Atencion
 *
 */

class QA_SntSeederPdaCommand extends ContainerAwareCommand{

    /**
     * Método de configuración del Seeder
     *
     */
    protected function configure(){
        $this->setName('snt:seeder:qa:pda');
        $this->setDescription('Seeder que crea los pda de QA');
        $this->setHelp('Crea los pda para QA');
        $this->addArgument('nombreArea', InputArgument::REQUIRED);
        $this->addArgument('nombrePda', InputArgument::REQUIRED);
        $this->addArgument('nombreProvincia', InputArgument::REQUIRED);
        $this->addArgument('localidadId', InputArgument::REQUIRED);
    }

    /**
     * Método de ejecución del seeder
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output){

        $nombreArea = $input->getArgument('nombreArea');
        $nombrePda = $input->getArgument('nombrePda');
        $nombreProvincia = $input->getArgument('nombreProvincia');
        $localidadId = $input->getArgument('localidadId');

        $io = new SymfonyStyle($input, $output);
        $doctrine = $this->getContainer()->get('doctrine');
        $em = $doctrine->getManager();


        $provincia = false;
        $provinciaRepository = $em->getRepository('ApiV1Bundle:Provincia');
        foreach ($provinciaRepository->findAll() as $provinciaData) {
            if($provinciaData->getNombre() == $nombreProvincia){
                $provincia = $provinciaData;
            }
        }

        $areaRepository = $em->getRepository('ApiV1Bundle:Area');
        foreach ($areaRepository->findAll() as $areaData) {
            if($areaData->getNombre() == $nombreArea){
                $area = $areaData;
            }
        }

        $puntoAtencionExist = false;
        $puntoAtencionRepository = $em->getRepository('ApiV1Bundle:PuntoAtencion');
        foreach ($puntoAtencionRepository->findAll() as $puntoAtencionData) {
            if($puntoAtencionData->getNombre() == $nombrePda){
                $puntoAtencionExist = $puntoAtencionData;
            }
        }
 
        if(!$puntoAtencionExist){

            $localidad = $provincia->getLocalidades()[$localidadId];
     
            $puntoAtencion = new PuntoAtencion($nombrePda, 'Calle falsa 123');
            $puntoAtencion->setArea($area);
            $puntoAtencion->setLatitud(-34.6033);
            $puntoAtencion->setLongitud(-58.3816);
            $puntoAtencion->setProvincia($provincia);
            $puntoAtencion->setLocalidad($localidad);
            $puntoAtencion->setEstado(1);

            $em->persist($puntoAtencion);
            $em->flush();
           
            $container = $this->getContainer();
            $sncexternal = new SNCExternalService($container);
            $sncMock = new SNCExternalServiceMock($container);
            $pdaIntegrationService = new puntoAtencionIntegration($container, $sncexternal, $sncMock);
            $pdaIntegrationService->agregarPuntoAtencion($puntoAtencion);

            $io->text('Se genero: PUNTO DE ATENCION');
            $io->text('     Nombre: '.$nombrePda);
            $io->text('     Area: '.$area->getNombre());
            $io->text('     Provincia: '.$provincia->getNombre());
            $io->text('     Localidad: '.$localidad->getNombre());
            $io->text('');
            $io->text('Done!');
        }
        else{
            $io->text('El PUNTO DE ATENCION');
            $io->text('     Nombre: '.$nombrePda);
            $io->text('     Ya existe!');
            $io->text('');
        }
    }
}
