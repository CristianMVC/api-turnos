<?php
namespace ApiV1Bundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class SntIOCommand
 * @package ApiV1Bundle\Command
 *
 * IO Command sive para mostrar un Texto o un Titulo dentro de un script de seedes SH
 *
 */

class SntIOCommand extends ContainerAwareCommand{

    /**
     * Método de configuración del Command
     *
     */
    protected function configure(){
        $this->setName('snt:seeder:io');
        $this->setDescription('Command para imprimir en pantalla');
        $this->setHelp('Comando para imprimir por pantalla');
        $this->addArgument('tipo', InputArgument::REQUIRED, 'titulo | texto');
        $this->addArgument('texto', InputArgument::REQUIRED);
    }

    /**
     * Método de ejecución del seeder
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output){
        $tipo = $input->getArgument('tipo');
        $texto = $input->getArgument('texto');

        $io = new SymfonyStyle($input, $output);

        if($tipo == "titulo"){
            $io->title($texto);            
        }
        else{
            $io->text($texto);
        }
    }
}
