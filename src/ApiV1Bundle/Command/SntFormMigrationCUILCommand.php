<?php

namespace ApiV1Bundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SntFormMigrationCUILCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('snt:form:cuil:migrate');
        $this->setDescription('Ejecuta la migración de los formularios al nuevo esquema de documento extranjero');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $em = $container->get('doctrine')->getManager();
        $tramiteRepository = $em->getRepository('ApiV1Bundle:Tramite');
        $tramites = $tramiteRepository->findAll();
        foreach ($tramites as $tramite) {
            $formulario = $tramite->getFormulario();
            $campos = $formulario->getCampos();
            // iterate over the form fields
            foreach ($campos as $index => $campo) {
                if ($campo['key'] == 'cuil') {
                    $campos[$index]['formComponent']['typeValue'] = 'text';
                }
            }
            $formulario->setCampos($campos);
        }
        $em->flush();
    }

}
