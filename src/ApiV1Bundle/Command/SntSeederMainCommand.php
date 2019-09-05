<?php
namespace ApiV1Bundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Helper\ProgressBar;

class SntSeederMainCommand extends ContainerAwareCommand
{

    /**
     * Método de configuración del Seeder
     */
    protected function configure()
    {
        $this->setName('snt:seeder:main');
        $this->setDescription('Seeder principal del sistema nacional de turnos');
        $this->setHelp('Este comando llena la base de datos de prueba');
        $this->addArgument('entidades', InputArgument::REQUIRED, 'organismos | tramites');
    }

    /**
     * Método de ejecución del seeder
     * 
     * @param InputInterface $input 
     * @param OutputInterface $output 
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $entitites = $input->getArgument('entidades');
        $commands = [
            'organismos' => [                
                'snt:seeder:provincias',
                'snt:seeder:organismos',
                'snt:seeder:puntosatencion'
            ],
            'tramites' => [
                'snt:seeder:tramites',
                'snt:seeder:tramites:puntoatencion',
                'snt:seeder:puntosatencion:horarios',
                'snt:seeder:tramites:grupo',
                'snt:seeder:puntosatencion:disponibilidad',
                'snt:seeder:turnos'                
            ]
        ];
        
        $progress = new ProgressBar($output, count($commands[$entitites]));
        $progress->start();
        foreach ($commands[$entitites] as $command) {
            $progress->advance();
            $this->runCommand($command);
            usleep(100);
        }
        $progress->finish();
    }
    
    /**
     * Run the command
     * 
     * @param unknown $command
     * @param unknown $outut
     */
    private function runCommand($commandName)
    {
        $output = new NullOutput();
        $command = $this->getApplication()->find($commandName);
        $greetInput = new ArrayInput([]);
        return $command->run($greetInput, $output);
    }
}
