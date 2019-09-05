<?php
namespace ApiV1Bundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use ApiV1Bundle\Entity\Organismo;

/**
 * Class QA_SntSeederOrganismoCommand
 * @package ApiV1Bundle\Command
 *
 * Seeder de los organismo para QA
 *
 */

class QA_SntSeederOrganismoCommand extends ContainerAwareCommand{

    /**
     * Método de configuración del Seeder
     *
     */
    protected function configure(){
        $this->setName('snt:seeder:qa:organismo');
        $this->setDescription('Seeder que crea el Organismo de QA');
        $this->setHelp('Crea un oraganismo para QA');
        $this->addArgument('nombreOrganismo', InputArgument::REQUIRED);
        $this->addArgument('aliasOrganismo', InputArgument::REQUIRED);
    }

    /**
     * Método de ejecución del seeder
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output){
        $nombreOrganismo = $input->getArgument('nombreOrganismo');
        $aliasOrganismo = $input->getArgument('aliasOrganismo');

        $io = new SymfonyStyle($input, $output);
        $doctrine = $this->getContainer()->get('doctrine');
        $em = $doctrine->getManager();


        $organismoRepo = $em->getRepository('ApiV1Bundle:Organismo');
        $organismoData = $organismoRepo->search($nombreOrganismo, 0, 1);

        if(!$organismoData){
            $organismo = new Organismo($nombreOrganismo, $aliasOrganismo);
            $em->persist($organismo);

            $io->text('Se genero: ORGANISMO');
            $io->text('     Nombre: '.$nombreOrganismo);
            $io->text('     Alias: '.$aliasOrganismo);
            $io->text('');
        }
        else{
            $io->text('El ORGANISMO ya existe!');
            $io->text('     Nombre: '.$nombreOrganismo);
            $io->text('     Alias: '.$aliasOrganismo);
            $io->text('');
        }

        $em->flush();
    }
}
