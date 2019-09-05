<?php
namespace ApiV1Bundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use ApiV1Bundle\Entity\User;
use ApiV1Bundle\Entity\Admin;

/**
 * Class SntSeederUserAdminCommand
 * @package ApiV1Bundle\Command
 *
 * Seeder que crea un usurio administrador
 *
 */

class SntSeederUserAdminCommand extends ContainerAwareCommand{

    /**
     * Método de configuración del Seeder
     *
     */
    protected function configure(){
        $this->setName('snt:seeder:user:admin');
        $this->setDescription('Seeder que crea un usurio administrador');
        $this->setHelp('Crea usuario adminsmitrador');
        $this->addArgument('usuario', InputArgument::REQUIRED);
        $this->addArgument('pass', InputArgument::REQUIRED);
        $this->addArgument('nombre', InputArgument::REQUIRED);
        $this->addArgument('apellido', InputArgument::REQUIRED);
    }

    /**
     * Método de ejecución del seeder
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output){

        $usuario = $input->getArgument('usuario');
        $pass = $input->getArgument('pass');
        $nombre = $input->getArgument('nombre');
        $apellido = $input->getArgument('apellido');

        $io = new SymfonyStyle($input, $output);
        $doctrine = $this->getContainer()->get('doctrine');
        $em = $doctrine->getManager();

        $container = $this->getContainer();
        $encoder = $container->get('security.password_encoder');

        if (!$em->getRepository('ApiV1Bundle:User')->findOneByUsername($usuario)){

            $user = new User($usuario, USER::ROL_ADMIN);
            $user->setPassword($encoder->encodePassword($user, $pass));
            $em->persist($user);

            $userRol = new Admin($nombre, $apellido, $user);
            $em->persist($userRol);

            $this->printUserCreate($io, $usuario, $pass, false);
        }
        else{
            $this->printUserCreate($io, $usuario, $pass, true);
        }

        $em->flush();
    }

    protected function printUserCreate($io, $user, $pass, $exist){

        if($exist){
            $io->text('El USUARIO: '.$user.' ya existe.');
            $io->text('     rol: ROL_ADMIN');
        }
        else{
            $io->text('Se genero: USUARIO ROL_ADMIN');
            $io->text('     usuario: '.$user);
        }
        $io->text('     password: '.$pass);
        $io->text('');
    }
}
