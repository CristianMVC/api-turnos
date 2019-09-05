<?php
namespace ApiV1Bundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use ApiV1Bundle\ExternalServices\PuntoAtencionIntegration;
use Symfony\Component\DependencyInjection\Container;
use ApiV1Bundle\ExternalServices\SNCExternalService;
use ApiV1Bundle\Mocks\SNCExternalServiceMock;
use ApiV1Bundle\Entity\PuntoAtencion;
use ApiV1Bundle\Entity\PuntoTramite;
use ApiV1Bundle\Entity\Tramite;


/**
 * Class QA_SntSeederRelacionTramitePdaCommand
 * @package ApiV1Bundle\Command
 *
 * Seeder que crea relacion entre Tramite y Pda
 *
 */

class QA_SntSeederRelacionTramitePdaCommand extends ContainerAwareCommand{

    /**
     * Método de configuración del Seeder
     *
     */
    protected function configure(){
        $this->setName('snt:seeder:qa:relacion:tramite:pda');
        $this->setDescription('Seeder que crea relacion entre Tramite y Pda');
        $this->setHelp('Crea relacion entre Tramite y Pda');
        $this->addArgument('nombreTramite', InputArgument::REQUIRED);
        $this->addArgument('nombrePda', InputArgument::REQUIRED);
    }

    /**
     * Método de ejecución del seeder
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output){
        $nombreTramite = $input->getArgument('nombreTramite');
        $nombrePda = $input->getArgument('nombrePda');

        $io = new SymfonyStyle($input, $output);
        $doctrine = $this->getContainer()->get('doctrine');
        $em = $doctrine->getManager();

        $pda = false;
        $pdaRepository = $em->getRepository('ApiV1Bundle:PuntoAtencion');
        foreach ($pdaRepository->findAll() as $puntoAtencionData) {
            if($puntoAtencionData->getNombre() == $nombrePda){
                $pda = $puntoAtencionData;
            }
        }

        $tramite = [];
        $uniqTramite = false;
        $tramiteRepository = $em->getRepository('ApiV1Bundle:Tramite');
        foreach ($tramiteRepository->findAll() as $tramiteData) {
            if($tramiteData->getNombre() == $nombreTramite){
                $tramite[] = $tramiteData->getId();
                $uniqTramite = $tramiteData;
            }
        }

        if($pda && $tramite != []){

            $params = [];
            $params["nombre"] = $pda->getNombre();
            $params["provincia"] = $pda->getProvincia();
            $params["localidad"] = $pda->getLocalidad();
            $params["direccion"] = $pda->getDireccion();
            $params["area"] = $pda->getArea();
            $params["tramites"] = $tramite;
            $params["estado"] = $pda->getEstado();
            $params["id"] = $pda->getId();

            $relacion = new PuntoTramite(
                $pda,
                $uniqTramite
            );
            $em->persist($relacion);
            $em->flush();

            $container = $this->getContainer();
            $sncexternal = new SNCExternalService($container);
            $sncMock = new SNCExternalServiceMock($container);
            $pdaIntegrationService = new puntoAtencionIntegration($container, $sncexternal, $sncMock);
            $pdaIntegrationService->editarPuntoAtencion((string) $pda->getId(), $params);

            $pda->addTramite($relacion);
            $em->persist($relacion);
            $em->flush();

            $io->text('Se genero relacion: TRAMITE con PDA');
            $io->text('     Nombre Tramite: '.$nombreTramite);
            $io->text('     Nomnre Punto Atencion: '.$nombrePda);
            $io->text('');
        }
        else{
            if(!$pda){
                $io->text('RELACION TRAMITE con PUNTO DE ATENCION');
                $io->text('     No Se encuentra el Punto de Atencion:  '.$nombrePda);
                $io->text('     Para la relacion con el TRAMITE: '.$nombreTramite);
                $io->text('');
            }
            if($tramite == []){
                $io->text('RELACION TRAMITE con PUNTO DE ATENCION');
                $io->text('El TRAMITE:  '.$nombreTramite);
                $io->text('     No existe!');
                $io->text('');
            }
        }
        
        $em->flush();
    }
}
