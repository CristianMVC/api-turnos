<?php
namespace ApiV1Bundle\Command;

use ApiV1Bundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use ApiV1Bundle\Entity\Admin;

class CreateUserCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this->setName('snt:user:create');
        $this->setDescription('Crear usuario Administrador');
        $this->setHelp('Comando para crear un usuario con rol Administrador');
        $this->addArgument('username', InputArgument::REQUIRED, 'Email del usuario');
        $this->addArgument('password', InputArgument::REQUIRED, 'Contraseña');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $encoder = $container->get('security.password_encoder');
        $em = $container->get('doctrine')->getManager();
        $userRepository = $em->getRepository('ApiV1Bundle:User');
        // username
        $username = $input->getArgument('username');
        // password
        $password = $input->getArgument('password');

        // find the user
        $user = $userRepository->findOneByUsername($username);
        if ($user) {
            $output->writeln("El usuario {$username} ya existe.");
            exit(1);
        }

        if(! $this->emailValido($username)) {
            $output->writeln('Debe ser una direccion de mail valida');
            exit(1);
        }

        $user = new User($username, USER::ROL_ADMIN);
        // encode and update the password
        $user->setPassword($encoder->encodePassword($user, $password));
        $admin = new Admin('Admin SNT','Admin SNT',$user);
        $em->persist($admin);
        $em->persist($user);
        $em->flush();

        $output->writeln("Usuario {$username} creado con exito!");
    }

    private function emailValido($email)
    {
        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }
        return true;
    }
}
