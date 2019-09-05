<?php
namespace ApiV1Bundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use ApiV1Bundle\Entity\User;
use ApiV1Bundle\Entity\Organismo;
use ApiV1Bundle\Entity\UserOrganismo;

/**
 * Class SntSeederUserOrganismoCommand
 * @package ApiV1Bundle\Command
 *
 * Seeder que crea un usurio Responsable de Organismo
 *
 */

class SntSeederUserOrganismoCommand extends ContainerAwareCommand{

    /**
     * Método de configuración del Seeder
     *
     */
    protected function configure(){
        $this->setName('snt:seeder:user:organismo');
        $this->setDescription('Seeder que crea un usurio Responsable de Organismo');
        $this->setHelp('Crea usuario organismo');
        $this->addArgument('usuario', InputArgument::REQUIRED);
        $this->addArgument('pass', InputArgument::REQUIRED);
        $this->addArgument('nombre', InputArgument::REQUIRED);
        $this->addArgument('apellido', InputArgument::REQUIRED);
        $this->addArgument('nombreOrganismo', InputArgument::REQUIRED);
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
        $nombreOrganismo = $input->getArgument('nombreOrganismo');

        $io = new SymfonyStyle($input, $output);
        $doctrine = $this->getContainer()->get('doctrine');
        $em = $doctrine->getManager();

        $container = $this->getContainer();
        $encoder = $container->get('security.password_encoder');

        $organismoRepository = $em->getRepository('ApiV1Bundle:Organismo');
        foreach ($organismoRepository->findAll() as $organismoData) {
            if($organismoData->getNombre() == $nombreOrganismo){
                $organismo = $organismoData;
            }
        }

        if (!$em->getRepository('ApiV1Bundle:User')->findOneByUsername($usuario)){

            $user = new User($usuario, USER::ROL_ORGANISMO);
            $user->setPassword($encoder->encodePassword($user, $pass));
            $em->persist($user);

            $userRol = new UserOrganismo($nombre, $apellido, $organismo, $user);
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
            $io->text('     rol: ROL_ORGANISMO');
        }
        else{
            $io->text('Se genero: USUARIO ROL_ORGANISMO');
            $io->text('     usuario: '.$user);
        }
        $io->text('     password: '.$pass);
        $io->text('');
    }
}
