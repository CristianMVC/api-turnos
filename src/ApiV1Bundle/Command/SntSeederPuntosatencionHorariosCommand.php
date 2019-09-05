<?php
namespace ApiV1Bundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use ApiV1Bundle\Entity\HorarioAtencion;

/**
 * Class SntDatabasePuntoAtencionHorariosCommand
 * @package ApiV1Bundle\Command
 *
 * Seeder de horarios del punto de atención
 */
class SntSeederPuntosatencionHorariosCommand extends ContainerAwareCommand
{

    /**
     * * Método de configuración del Seeder
     */
    protected function configure()
    {
        $this->setName('snt:seeder:puntosatencion:horarios');
        $this->setDescription('Seeder de horarios de los puntos de atención');
        $this->setHelp('Este comando llena la base de datos con horarios de prueba para cada punto de atención');
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

        $io->title('Generando horarios');
        // repositories
        $puntoAtencionRepository = $em->getRepository('ApiV1Bundle:PuntoAtencion');
        foreach ($puntoAtencionRepository->findAll() as $puntoAtencion) {
            // obtengo los tramites
            $tramites = $puntoAtencion->getTramites();
            // los puntos de atención que tienen tramites
            if (count($tramites)) {
                $io->text('Punto de atención: ' . $puntoAtencion->getNombre());
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
            }
        }
        $em->flush();
        $io->text('Done!');
    }

    /**
     * Obtener una hora de inicio y fin random
     *
     * @return array
     */
    private function getHorario()
    {
        $horario = [
            [['09:00', '12:00'], ['13:00', '16:00']],
            [['09:00', '13:00'], ['14:00', '17:00']],
            [['10:00', '13:00'], ['15:00', '17:00']],
            [['10:00', '14:00'], ['15:00', '17:00']],
        ];
        return $horario[rand(0, 3)];
    }
}
