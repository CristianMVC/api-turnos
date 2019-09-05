<?php
namespace ApiV1Bundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use ApiV1Bundle\Entity\PuntoAtencion;
use ApiV1Bundle\Entity\Tramite;

/**
 * Class SntSeederPuntoatencionTramiteCommand
 * @package ApiV1Bundle\Command
 *
 * Seeder de la realción de trámites con puntos de atención
 *
 */

class SntSeederPuntosatencionTramitesCommand extends ContainerAwareCommand
{
    /**
     * Método de configuración del Seeder
     */
    protected function configure()
    {
        $this->setName('snt:seeder:tramites:puntoatencion');
        $this->setDescription('Seeder de la relación tramites con puntos de atención');
        $this->setHelp('Este comando llena la base de datos con relaciones entre los tramites y puntos de atención');
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

        $io->title('Relacionando tramites con puntos de atención');
        // los tramites
        $tramites = [];
        $tramiteRepository = $em->getRepository('ApiV1Bundle:Tramite');
        foreach ($tramiteRepository->findAll() as $tramite) {
            $organismoId = $tramite->getArea()->getOrganismo()->getId();
            $tramites[$organismoId][] = $tramite;
        }
        // los puntos de atencion
        $puntoAtencionRepository = $em->getRepository('ApiV1Bundle:PuntoAtencion');
        foreach ($puntoAtencionRepository->findAll() as $puntoAtencion) {
            $io->text('Punto de atención: ' . $puntoAtencion->getId());
            $puntoTramites = [];
            $area = $puntoAtencion->getArea();
            $organismo = $area->getOrganismo();
            for ($i = 0; $i < 5; $i++) {
                $tramite = $this->getTramiteRandom($tramites[$organismo->getId()]);
                if ($tramite && ! in_array($tramite->getId(), $puntoTramites)) {
                    $puntoAtencion->addTramite($tramite);
                    $puntoTramites[] = $tramite->getId();
                }
            }
        }
        $em->flush();
        $io->text('Done!');
    }

    /**
     * Obtine trámites en forma aleatoria
     *
     * @param $total
     *
     * @return mixed
     */

    private function getTramiteRandom($tramites)
    {
        return $tramites[rand(0, count($tramites) - 1)];
    }
}
