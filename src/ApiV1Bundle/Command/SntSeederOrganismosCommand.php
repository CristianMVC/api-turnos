<?php
namespace ApiV1Bundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use ApiV1Bundle\Entity\Organismo;
use ApiV1Bundle\Entity\Area;

/**
 * Class SntDatabaseOrganismosCommand
 * @package ApiV1Bundle\Command
 *
 * Seeder de los organismos y areas
 *
 */

class SntSeederOrganismosCommand extends ContainerAwareCommand
{

    /**
     * Método de configuración del Seeder
     *
     */
    protected function configure()
    {
        $this->setName('snt:seeder:organismos');
        $this->setDescription('Seeder de los organismos y areas');
        $this->setHelp('Este comando llena la base de datos con organismos y areas de prueba');
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

        $io->title('Generando organismos y areas');
        // los organismos
        $organismos = $this->getOrganismos();
        foreach ($organismos as $entry) {
            $organismoNombre = $entry[0];
            $organismoAbr = $entry[1];

            $io->text('Guardando organismo: ' . $organismoNombre);
            $organismo = new Organismo($organismoNombre, $organismoAbr);
            $em->persist($organismo);

            // generar 5 areas de prueba por organismo
            $io->text('Generando areas...');
            for ($i = 1; $i <= 5; $i++) {
                $areaNombre = $organismoAbr . '::' . str_pad($i, 3, 0, STR_PAD_LEFT);
                $areaAbbr = strtoupper(substr(md5($areaNombre), 0, 3));
                $area = new Area($areaNombre, $areaAbbr);
                $area->setOrganismo($organismo);
                $em->persist($area);
            }
        }
        $em->flush();
        $io->text('Done!');
    }

    /**
     * Obtiene los organismos
     *
     * @return array
     */
    private function getOrganismos()
    {
        $organismos = [];
        $organismos[] = ['Administración Federal de Ingresos Públicos', 'AFIP'];
        $organismos[] = ['Administración Nacional de Medicamentos, Alimentos y Tecnología Médica', 'ANMATM'];
        $organismos[] = ['Administración Nacional de Seguridad Social', 'ANSES'];
        $organismos[] = ['Dirección Nacional del Registro Nacional de las Personas', 'RENAPER'];
        $organismos[] = ['Instituto Nacional Central Unico Coordinación de Ablación e Implante', 'INCUCAI'];
        return $organismos;
    }
}
