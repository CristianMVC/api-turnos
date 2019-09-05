<?php

namespace ApiV1Bundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use ApiV1Bundle\Entity\Disponibilidad;

class SntSeederPuntosAtencionDisponibilidadCommand extends ContainerAwareCommand
{
    /**
     *  Método de configuración del Seeder
     */
    protected function configure()
    {
        $this->setName('snt:seeder:puntosatencion:disponibilidad');
        $this->setDescription('Seeder de disponibilidad de los puntos de atención');
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

        $io->title('Generando disponibilidad');
        // repositories
        $grupoRepository = $em->getRepository('ApiV1Bundle:GrupoTramite');
        foreach ($grupoRepository->findAll() as $grupoTramite) {
            $puntoAtencion = $grupoTramite->getPuntoAtencion();
            $horarios = $puntoAtencion->getHorariosAtencion();
            $io->text('Generando disponibilidad para ' . $puntoAtencion->getNombre());
            if (count($horarios)) {                
                foreach ($horarios as $horario) {
                    $disponibilidad = new Disponibilidad($puntoAtencion, $grupoTramite, $horario, rand(10, 20));
                    $em->persist($disponibilidad);
                }
                $em->flush();
            } else {
                $io->text('No hay horarios');
            }
        }
        $io->text('Done!');
    }
}
