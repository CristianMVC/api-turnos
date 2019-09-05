<?php
namespace ApiV1Bundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use ApiV1Bundle\Entity\PuntoAtencion;
use ApiV1Bundle\Entity\GrupoTramite;
use ApiV1Bundle\ExternalServices\GrupoTramitesIntegration;
use Symfony\Component\DependencyInjection\Container;
use ApiV1Bundle\ExternalServices\SNCExternalService;
use ApiV1Bundle\Mocks\SNCExternalServiceMock;

/**
 * Class QA_SntSeederGrupoTramiteCommand
 * @package ApiV1Bundle\Command
 *
 * Seeder que crea el Grupo de Tramite para un punto de atencion
 *
 */

class QA_SntSeederGrupoTramiteCommand extends ContainerAwareCommand{

    /**
     * Método de configuración del Seeder
     *
     */
    protected function configure(){
        $this->setName('snt:seeder:qa:grupotramite');
        $this->setDescription('Seeder que crea el Grupo de Tramite para un punto de atencion');
        $this->setHelp('Crea un grupo de tramite para un Punto de Atencion');
        $this->addArgument('nombrePda', InputArgument::REQUIRED);
        $this->addArgument('nombreGrupoTramite', InputArgument::REQUIRED);
        $this->addArgument('horizonte', InputArgument::REQUIRED);
        $this->addArgument('intervalo', InputArgument::REQUIRED);
    }

    /**
     * Método de ejecución del seeder
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output){
        $nombrePda = $input->getArgument('nombrePda');
        $nombreGrupoTramite = $input->getArgument('nombreGrupoTramite');
        $horizonte = $input->getArgument('horizonte');
        $intervalo = $input->getArgument('intervalo');

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

        $grupoTramiteExiste = false;
        $grupoTramiteRepository = $em->getRepository('ApiV1Bundle:GrupoTramite');
        foreach ($grupoTramiteRepository->findAll() as $grupoTramiteData) {
            if($grupoTramiteData->getNombre() == $nombreGrupoTramite){
                $grupoTramiteExiste = $nombreGrupoTramite;
            }
        }

        if($pda && !$grupoTramiteExiste){

            # $puntoAtencion, $nombre, $horizonte, $intervalo)
            $grupoDeTramite = new GrupoTramite($pda, $nombreGrupoTramite, $horizonte, $intervalo);
            $em->persist($grupoDeTramite);
            $em->flush();

            $container = $this->getContainer();
            $sncexternal = new SNCExternalService($container);
            $sncMock = new SNCExternalServiceMock($container);
            $grupoTramiteIntegrationService = new GrupoTramitesIntegration($container, $sncexternal, $sncMock);
            $grupoTramiteIntegrationService->agregarCola($grupoDeTramite, $pda->getId());
            

            $io->text('Se genero: GRUPO DE TRAMITE');
            $io->text('     Nombre: '.$nombreGrupoTramite);
            $io->text('     Horizonte: '.$horizonte);
            $io->text('     Intervalo: '.$intervalo);
            $io->text('Para el punto de atención: '.$nombrePda);
            $io->text('');
        }
        else{
            if(!$pda){
                $io->text('No Se encuentra el Punto de Atencion:  '.$nombrePda);
                $io->text('     Para la creacion del GRUPO DE TRAMITE: '.$nombreGrupoTramite);
                $io->text('');
            }
            if($grupoTramiteExiste){
                $io->text('El GRUPO de TRAMITE:  '.$nombreGrupoTramite);
                $io->text('     Ya existe!');
                $io->text('');
            }
        }
        
        $em->flush();
    }
}
