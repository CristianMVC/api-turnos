<?php

namespace ApiV1Bundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use ApiV1Bundle\Entity\PuntoAtencion;
use ApiV1Bundle\Entity\GrupoTramite;

/**
 * Class SntSeederGrupoTramiteCommand
 * @package ApiV1Bundle\Command
 *
 * Seeder para carga de grupos de trámites
 *
 */

class SntSeederGrupoTramiteCommand extends ContainerAwareCommand
{
    /**
     * Método de configuración del Seeder
     */
    protected function configure()
    {
        $this->setName('snt:seeder:tramites:grupo');
        $this->setDescription('Seeder de los grupos de tramites');
        $this->setHelp('Este comando llena la base de datos con grupos de tramites de prueba');
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

        $io->title('Relacionando tramites con grupo de tramites');
        // repositories
        $puntoAtencionRepository = $em->getRepository('ApiV1Bundle:PuntoAtencion');

        foreach ($puntoAtencionRepository->findAll() as $puntoAtencion) {
            $io->text('Punto de atención: ' . $puntoAtencion->getNombre());
            // obtengo los tramites
            $tramites = $puntoAtencion->getTramites();
            // los puntos de atención que tienen tramites
            if (count($tramites)) {
                $tramitesArr = [];
                foreach ($tramites as $tramiteRaw) {
                    $tramitesArr[] = $tramiteRaw;
                }
                // dividimos los tramites en grupos de 3
                $tramitesChunks = array_chunk($tramitesArr, 3);
                foreach ($tramitesChunks as $key => $tramitesGroup) {
                    $index = $key + 1;
                    # $io->text('>> Grupo de tramites ' . $index . ' con ' . count($tramitesGroup) . ' tramites');
                    $intervalo = $this->getIntervalo();

                    $grupoTramites = new GrupoTramite($puntoAtencion,
                        'gp::' . $puntoAtencion->getId() . '::' . str_pad($index, 2, 0, STR_PAD_LEFT),
                        $this->getHorizonte()
                    );
                    $grupoTramites->setPuntoAtencion($puntoAtencion);
                    $grupoTramites->setIntervaloTiempo($intervalo['intervalo']);

                    foreach ($tramitesGroup as $tramite) {
                        $grupoTramites->addTramite($tramite);
                    }
                    $em->persist($grupoTramites);
                }
                $em->flush();
            }
        }
        $io->text('Done!');
    }

    /**
     * Obtener un horizonte random
     *
     * @return number
     */
    private function getHorizonte()
    {
        $horizonte = [30, 60];
        return $horizonte[rand(0, 1)];
    }

    private function getIntervalo()
    {
        $intervalo = [
            ['intervalo' => 0.25, 'string' => '15 minutes'],
            ['intervalo' => 0.5, 'string' => '30 minutes'],
            ['intervalo' => 1, 'string' => '60 minutes']
        ];
        return $intervalo[rand(0, 2)];
    }
}
