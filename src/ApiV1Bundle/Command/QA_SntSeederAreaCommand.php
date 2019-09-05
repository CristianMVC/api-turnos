<?php
namespace ApiV1Bundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use ApiV1Bundle\Entity\Area;
use ApiV1Bundle\Entity\Organismo;

/**
 * Class QA_SntSeederAreaCommand
 * @package ApiV1Bundle\Command
 *
 * Seeder para la creacion de un area para QA
 *
 */

class QA_SntSeederAreaCommand extends ContainerAwareCommand{

    /**
     * Método de configuración del Seeder
     *
     */
    protected function configure(){
        $this->setName('snt:seeder:qa:area');
        $this->setDescription('Seeder que crea el Area de QA');
        $this->setHelp('Crea un area para QA');
        $this->addArgument('nombreArea', InputArgument::REQUIRED);
        $this->addArgument('aliasArea', InputArgument::REQUIRED);
        $this->addArgument('nombreOrganismo', InputArgument::REQUIRED);
    }

    /**
     * Método de ejecución del seeder
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output){

        $nombreArea = $input->getArgument('nombreArea');
        $aliasArea = $input->getArgument('aliasArea');
        $nombreOrganismo = $input->getArgument('nombreOrganismo');

        $io = new SymfonyStyle($input, $output);
        $doctrine = $this->getContainer()->get('doctrine');
        $em = $doctrine->getManager();

        $organismoRepo = $em->getRepository('ApiV1Bundle:Organismo');
        $organismoData = $organismoRepo->search($nombreOrganismo, 0, 1);
        $organismoId = $organismoData[0]["id"];

        $orgaData = false;
        foreach ($organismoRepo->findAll() as $orga) {
            if($orga->getId() == $organismoId){
                $orgaData = $orga;
            }
        }

        $areaValidate = false;
        $areaRepository = $em->getRepository('ApiV1Bundle:Area');
        foreach ($areaRepository->findAll() as $areaData) {
            if($areaData->getNombre() == $nombreArea){
                $areaValidate = $areaData;
            }
        }

        if(!$areaValidate){
            $area = new Area($nombreArea, $aliasArea);
            $area->setOrganismo($orgaData);
            $em->persist($area);

            $io->text('Se genero: AREA');
            $io->text('     Nombre: '.$nombreArea);
            $io->text('     Alias: '.$aliasArea);
            $io->text('');
        }
        else{
            $io->text('El AREA ya existe!');
            $io->text('     Nombre: '.$nombreArea);
            $io->text('     Alias: '.$aliasArea);
            $io->text('');   
        }
        
        $em->flush();
    }
}
