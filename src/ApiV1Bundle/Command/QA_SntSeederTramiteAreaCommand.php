<?php
namespace ApiV1Bundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use ApiV1Bundle\Entity\Formulario;
use ApiV1Bundle\Entity\Organismo;
use ApiV1Bundle\Entity\Tramite;
use ApiV1Bundle\Entity\Area;

/**
 * Class QA_SntSeederTramiteAreaCommand
 * @package ApiV1Bundle\Command
 *
 * Seeder que crea un tramite para un area especifica
 *
 */

class QA_SntSeederTramiteAreaCommand extends ContainerAwareCommand{

    /**
     * Método de configuración del Seeder
     *
     */
    protected function configure(){
        $this->setName('snt:seeder:qa:tramite:area');
        $this->setDescription('Seeder que crea un tramite para un area de QA');
        $this->setHelp('Crea un tramite para un area para QA');
        $this->addArgument('nombreTramite', InputArgument::REQUIRED);
        $this->addArgument('nombreArea', InputArgument::REQUIRED);
    }

    /**
     * Método de ejecución del seeder
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output){
        $nombreTramite = $input->getArgument('nombreTramite');
        $nombreArea = $input->getArgument('nombreArea');

        $io = new SymfonyStyle($input, $output);
        $doctrine = $this->getContainer()->get('doctrine');
        $em = $doctrine->getManager();

        $areaRepository = $em->getRepository('ApiV1Bundle:Area');
        foreach ($areaRepository->findAll() as $areaData) {
            if($areaData->getNombre() == $nombreArea){
                $area = $areaData;
            }
        }

        // create formulario
        $formulario = new Formulario($this->getFormulario());
        // create tramite
        $tramite = new Tramite($nombreTramite, 1);
        $areas[] = $area;
        $tramite->addArea($areas);
        $tramite->setDuracion(15);
        $tramite->setRequisitos($this->getRequisitos());
        $tramite->setFormulario($formulario);
        $tramite->setExcepcional(1);
        $em->persist($tramite);

        $io->text('Se genero: TRAMITE');
        $io->text('     Nombre: '.$nombreTramite);
        $io->text('     para el Area: '.$nombreArea);
        $io->text('');

        $em->flush();
    }

    private function getRequisitos(){
        $requisitos = 'Pellentesque pellentesque tincidunt facilisis. Maecenas dictum aliquet tortor id pharetra.|';
        $requisitos .= 'Cras porttitor sollicitudin augue, vel aliquam eros volutpat non.|';
        $requisitos .= 'Vivamus viverra tristique eros vel feugiat.';
        return $requisitos;
    }

    private function getFormulario(){
        $campos = '[{"description":"","formComponent":{"typeValue":"text"},"key":"cuil","label":"CUIT\/CUIL","order":0,"required":true,"type":"textbox","mandatory":true,"inicial":true},{"description":"","formComponent":{"typeValue":"text"},"key":"nombre","label":"Nombre","order":1,"required":true,"type":"textbox","mandatory":true,"inicial":true},{"description":"","formComponent":{"typeValue":"text"},"key":"apellido","label":"Apellido","order":2,"required":true,"type":"textbox","mandatory":true,"inicial":true},{"description":"","formComponent":{"typeValue":"text"},"key":"email","label":"Email","order":3,"required":true,"type":"textbox","mandatory":true,"inicial":true}]';
        return json_decode($campos, true);
    }
}
