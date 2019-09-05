<?php
namespace ApiV1Bundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use ApiV1Bundle\Entity\GrupoTramite;
use ApiV1Bundle\Entity\Tramite;

/**
 * Class QA_SntSeederTramiteGrupoDeTramieCommand
 * @package ApiV1Bundle\Command
 *
 * Seeder que crea relacion entre tramite y grupo de tramite
 *
 */

class QA_SntSeederTramiteGrupoDeTramieCommand extends ContainerAwareCommand{

    /**
     * Método de configuración del Seeder
     *
     */
    protected function configure(){
        $this->setName('snt:seeder:qa:tramite:grupotramite');
        $this->setDescription('Seeder que crea relacion entre tramite y grupo de tramite');
        $this->setHelp('Crea una relacion entre tramite y grupo de tramite');
        $this->addArgument('nombreTramite', InputArgument::REQUIRED);
        $this->addArgument('nombreGrupoTramite', InputArgument::REQUIRED);
    }

    /**
     * Método de ejecución del seeder
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output){
        $nombreTramite = $input->getArgument('nombreTramite');
        $nombreGrupoTramite = $input->getArgument('nombreGrupoTramite');

        $io = new SymfonyStyle($input, $output);
        $doctrine = $this->getContainer()->get('doctrine');
        $em = $doctrine->getManager();

        $grupoTramite = false;
        $grupoTramiteRepository = $em->getRepository('ApiV1Bundle:GrupoTramite');
        foreach ($grupoTramiteRepository->findAll() as $grupoTramiteData) {
            if($grupoTramiteData->getNombre() == $nombreGrupoTramite){
                $grupoTramite = $grupoTramiteData;
            }
        }

        $tramite = false;
        $TramiteRepository = $em->getRepository('ApiV1Bundle:Tramite');
        foreach ($TramiteRepository->findAll() as $TramiteData) {
            if($TramiteData->getNombre() == $nombreTramite){
                $tramite = $TramiteData;
            }
        }

        if($grupoTramite && $tramite){
            $grupoTramite->addTramite($tramite);
            $em->persist($grupoTramite);

            $io->text('Se genero relacion entre: GRUPO DE TRAMITE Y TRAMITE');
            $io->text('     Nombre Grupo Tramite: '.$nombreGrupoTramite);
            $io->text('     Tramite: '.$nombreTramite);
            $io->text('');
        }

        $em->flush();
    }
}
