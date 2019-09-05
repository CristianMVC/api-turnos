<?php
namespace ApiV1Bundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use ApiV1Bundle\Entity\GrupoTramite;
use ApiV1Bundle\Entity\Disponibilidad;

/**
 * Class QA_SntSeederDisponibilidadPdaCommand
 * @package ApiV1Bundle\Command
 *
 * Seeder que genera disponibilidad para un grupo de tramite que pertenece a un punto de atencion
 *
 */

class QA_SntSeederDisponibilidadPdaCommand extends ContainerAwareCommand{

    /**
     * Método de configuración del Seeder
     *
     */
    protected function configure(){
        $this->setName('snt:seeder:qa:disponibilidad:grupotramite');
        $this->setDescription('Seeder que genera disponibilidad para un grupo de tramite');
        $this->setHelp('Genera disponibilidad para un grupo de tramite');
        $this->addArgument('nombreGrupoTramite', InputArgument::REQUIRED);
    }

    /**
     * Método de ejecución del seeder
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output){
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
      
        $puntoAtencion = $grupoTramite->getPuntoAtencion();
        $horarios = $puntoAtencion->getHorariosAtencion();
        if (count($horarios)) {                
            foreach ($horarios as $horario) {
                $disponibilidad = new Disponibilidad($puntoAtencion, $grupoTramite, $horario, rand(10, 20));
                $em->persist($disponibilidad);
            }
            $em->flush();
            $io->text('Se genero: DISPONIBILIDAD');
            $io->text('     Nombre Grupo Tramite: '.$grupoTramite->getNombre());
            $io->text('     Para el Punto Atencion: '.$puntoAtencion->getNombre());
            $io->text('');
        } else {
            $io->text('No hay HORARIOS ');
            $io->text('     para el PUNTO DE ATENCION: '.$puntoAtencion->getNombre());
        }
    }
}
