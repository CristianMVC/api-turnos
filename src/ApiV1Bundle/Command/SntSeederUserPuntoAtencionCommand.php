<?php
namespace ApiV1Bundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use ApiV1Bundle\Entity\User;
use ApiV1Bundle\Entity\Area;
use ApiV1Bundle\Entity\Organismo;
use ApiV1Bundle\Entity\PuntoAtencion;
use ApiV1Bundle\Entity\UserPuntoAtencion;

/**
 * Class SntSeederUserPuntoAtencionCommand
 * @package ApiV1Bundle\Command
 *
 * Seeder que crea un usurio Responsable de Punto de Atencion.  
 *
 */

class SntSeederUserPuntoAtencionCommand extends ContainerAwareCommand{

    /**
     * Método de configuración del Seeder
     *
     */
    protected function configure(){
        $this->setName('snt:seeder:user:puntoatencion');
        $this->setDescription('Seeder que crea un usurio Responsable de Punto de Atencion.  ');
        $this->setHelp('Crea usuario puntoatencion');
        $this->addArgument('usuario', InputArgument::REQUIRED);
        $this->addArgument('pass', InputArgument::REQUIRED);
        $this->addArgument('nombre', InputArgument::REQUIRED);
        $this->addArgument('apellido', InputArgument::REQUIRED);
        $this->addArgument('nombreOrganismo', InputArgument::REQUIRED);
        $this->addArgument('nombreArea', InputArgument::REQUIRED);
        $this->addArgument('nombrePda', InputArgument::REQUIRED);
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
        $nombreArea = $input->getArgument('nombreArea');
        $nombrePda = $input->getArgument('nombrePda');

        $io = new SymfonyStyle($input, $output);
        $doctrine = $this->getContainer()->get('doctrine');
        $em = $doctrine->getManager();

        $container = $this->getContainer();
        $encoder = $container->get('security.password_encoder');

        $rol = USER::ROL_PUNTOATENCION;

        $areaRepository = $em->getRepository('ApiV1Bundle:Area');
        foreach ($areaRepository->findAll() as $areaData) {
            if($areaData->getNombre() == $nombreArea){
                $area = $areaData;
            }
        }

        $organismoRepository = $em->getRepository('ApiV1Bundle:Organismo');
        foreach ($organismoRepository->findAll() as $organismoData) {
            if($organismoData->getNombre() == $nombreOrganismo){
                $organismo = $organismoData;
            }
        }

        $puntoAtencionRepository = $em->getRepository('ApiV1Bundle:PuntoAtencion');
        foreach ($puntoAtencionRepository->findAll() as $puntoAtencionData) {
            if($puntoAtencionData->getNombre() == $nombrePda){
                $pda = $puntoAtencionData;
            }
        }

        if (!$em->getRepository('ApiV1Bundle:User')->findOneByUsername($usuario)){

            $user = new User($usuario, $rol);
            $user->setPassword($encoder->encodePassword($user, $pass));
            $em->persist($user);

            $usuarioPda = new UserPuntoAtencion($nombre, $apellido, $organismo, $area, $pda, $user);
            $em->persist($usuarioPda);

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
            $io->text('     rol: ROL_PUNTOATENCION');
        }
        else{
            $io->text('Se genero: USUARIO ROL_PUNTOATENCION');
            $io->text('     usuario: '.$user);
        }
        $io->text('     password: '.$pass);
        $io->text('');
    }
}
