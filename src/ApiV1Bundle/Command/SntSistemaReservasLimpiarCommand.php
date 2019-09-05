<?php

namespace ApiV1Bundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SntSistemaReservasLimpiarCommand extends ContainerAwareCommand
{
    /**
     * Intervalo de tiempo máximo de reserva: 5 minutos
     * @var string
     */
    private $interval = 'PT5M';

    /**
     * Método de configuración del comando
     */
    protected function configure()
    {
        $this->setName('snt:sistema:reservas:limpiar');
        $this->setDescription('Comando para limpiar las reservar caidas');
        $this->setHelp('Este comando limpia la base de datos de reservas caidas');
    }

    /**
     * Método de ejecución del comando
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $doctrine = $this->getContainer()->get('doctrine');
        $em = $doctrine->getManager();
        // repository
        $turnoRepository = $em->getRepository('ApiV1Bundle:Turno');

        $turnoRepository->deleteTurnosExpirados();

        $output->writeln("Se eliminaron los turnos reservados que tienen mas de 5 minutos de vida");
    }
}
