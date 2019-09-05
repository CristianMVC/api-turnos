<?php
namespace ApiV1Bundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use ApiV1Bundle\Entity\PuntoAtencion;
use ApiV1Bundle\Entity\HorarioAtencion;

/**
 * Class QA_SntSeederHorariosPdaCommand
 * @package ApiV1Bundle\Command
 *
 * Seeder que genera horarios para un punto de atencion
 *
 */

class QA_SntSeederHorariosPdaCommand extends ContainerAwareCommand{

    /**
     * Método de configuración del Seeder
     *
     */
    protected function configure(){
        $this->setName('snt:seeder:qa:horarios:pda');
        $this->setDescription('Seeder que genera horarios para un punto de atencion');
        $this->setHelp('Genera horarios para un punto de atencion');
        $this->addArgument('nombrePda', InputArgument::REQUIRED);
    }

    /**
     * Método de ejecución del seeder
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output){
        $nombrePda = $input->getArgument('nombrePda');

        $io = new SymfonyStyle($input, $output);
        $doctrine = $this->getContainer()->get('doctrine');
        $em = $doctrine->getManager();



        $puntoAtencion = false;
        $puntoAtencionRepository = $em->getRepository('ApiV1Bundle:PuntoAtencion');
        foreach ($puntoAtencionRepository->findAll() as $puntoAtencionData) {
            if($puntoAtencionData->getNombre() == $nombrePda){
                $puntoAtencion = $puntoAtencionData;
            }
        }

        if($puntoAtencion){

            $tramites = $puntoAtencion->getTramites();
        
            // de lunes a viernes
            for ($i = 1; $i <= 5; $i++) {
                $horario = $this->getHorario();

                // horario mañana
                $horarioManana = new HorarioAtencion(
                    $puntoAtencion,
                    $i,
                    new \DateTime($horario[0][0]),
                    new \DateTime($horario[0][1]),
                    $puntoAtencion->getId()
                );
                $em->persist($horarioManana);
                
                // horario tarde
                $horarioTarde = new HorarioAtencion(
                    $puntoAtencion,
                    $i,
                    new \DateTime($horario[1][0]),
                    new \DateTime($horario[1][1]),
                    $puntoAtencion->getId()
                );

                $em->persist($horarioTarde);
            }

            $em->flush();
            $io->text('Se genero: HORARIOS');
            $io->text('     Para el Punto Atencion: '.$puntoAtencion->getNombre());
            $io->text('');
            $io->text('Done!');

        } else {
            $io->text('No se encuentra el PUNTO DE ATENCION ');
            $io->text('     Nombre Puento Atenncio: '.$puntoAtencion->getNombre());
        }
    }
    
    private function getHorario(){
        $horario = [
            [['09:00', '12:00'], ['13:00', '16:00']],
            [['09:00', '13:00'], ['14:00', '17:00']],
            [['10:00', '13:00'], ['15:00', '17:00']],
            [['10:00', '14:00'], ['15:00', '17:00']],
        ];
        return $horario[rand(0, 3)];
    }
}
