<?php
namespace ApiV1Bundle\Command;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\toArray;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use ApiV1Bundle\Entity\User;
use ApiV1Bundle\Entity\UserOrganismo;
use ApiV1Bundle\Entity\UserArea;
use ApiV1Bundle\Entity\UserPuntoAtencion;
use ApiV1Bundle\Entity\Admin;
use ApiV1Bundle\Entity\Organismo;
use ApiV1Bundle\Entity\Area;
use ApiV1Bundle\Entity\PuntoAtencion;
use ApiV1Bundle\Entity\Tramite;
use ApiV1Bundle\Entity\Formulario;
use ApiV1Bundle\Entity\HorarioAtencion;
use ApiV1Bundle\Entity\Disponibilidad;
use ApiV1Bundle\Entity\GrupoTramite;
use ApiV1Bundle\ExternalServices\PuntoAtencionIntegration;
use ApiV1Bundle\ExternalServices\SNCExternalService;
use ApiV1Bundle\Mocks\SNCExternalServiceMock;

/**
 * Class SntDatabaseOrganismosCommand
 * @package ApiV1Bundle\Command
 *
 * Seeder de los organismos y areas
 *
 */

class SntSeederUsersTestCommand extends ContainerAwareCommand{

    /**
     * Método de configuración del Seeder
     *
     */
    protected function configure()
    {
        $this->setName('snt:seeder:usersTest');
        $this->setDescription('Seeder que crea usuarios, con area y organismo de pruebas para la automatizacion de test');
        $this->setHelp('Este comando llena la base de datos con los datos de pruebas para test');
    }

    /**
     * Método de ejecución del seeder
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $doctrine = $this->getContainer()->get('doctrine');
        $em = $doctrine->getManager();

        $io->title('Generando Organismo y Area');
        
        // $organismoData = ['Prueba de Modernizacion', 'PM'];
        $organismoData = ['0_'.time(), 'PM'];
        $areaData = ['La Academia Racing Club', 'CACA'];
        $tramitesData = ['0 El trámite más inútil', 'Inscripción', 'Inscripción a la Pileta', 'Venta de entradas'];
        
        $organismo = $this->generarOrganismo($io, $em, $organismoData);
        $area = $this->generarArea($io, $em, $organismo, $areaData);

        $io->title('Generando Puntos de Atención');
        $puntosDeAtencion = $this->generarPuntosAtencion($io, $em, $area);
        
        $io->title('Generando Tramites para '.$organismo->getNombre().'::'.$area->getNombre());
        $tramites = $this->generateTramitesArea($io, $em, $tramitesData, $area);
        $em->flush();
            

        $io->title('Generando horarios');
        $horarios = $this->generateHorarios($io, $em, $puntosDeAtencion);
        $em->flush();

            $io->title('Generando Grupo de Tramites');
            $grupoTramites = $this->generateGrupoTramites($io, $em, $puntosDeAtencion, $tramites);
       

            #todo - lo vera cristian para ver como persistir y poder agregar tramites
            // $io->title('Relacionando Tramites con Puntos de Atención');
            
            // Funcion para termianr => $this->generateRelacionPuntoAtencionTramites($io, $em, $puntosDeAtencion, $tramites);


            // $io->title('Generando disponibilidad');
            // $io->title('Generando turnos');

        $io->title('Generando Usuarios');
        
        $usuariosPda = [];
        $usuariosPda[] = ['modpda_01@mailinator.com', 'QAsnt2018', 'Qa Sebastian', 'Saja', $puntosDeAtencion[0]];
        $usuariosPda[] = ['modpda_02@mailinator.com', 'QAsnt2018', 'Qa Alfio', 'Basile', $puntosDeAtencion[1]];
        $usuariosPda[] = ['modpda_03@mailinator.com', 'QAsnt2018', 'Qa Roberto', 'D\'Alessandro', $puntosDeAtencion[2]];

        $this->generateUserAdmin($io, 'admin@admin.com', 'admin', 'admin', 'istrador');
        $this->generateUserOrganismo($io, 'modorganismo@mailinator.com', 'QAsnt2018', 'Qa Claudio Fabián', 'Tapia', $organismo);
        $this->generateUserArea($io, 'modarea@mailinator.com', 'QAsnt2018', 'Qa José Néstor', 'Pékerman', $organismo, $area);
        $this->generateUserPda($io, $usuariosPda, $organismo, $area);


            #TODO crear funcion para la creacion de usuarios con el ROL de agente. Obenter esta data desde Filas Agente Entity
            // $this->generateUserAgente();


        $em->flush();
        $io->text('Done!');
    }

    private function generateGrupoTramites($io, $em, $puntosDeAtencion, $tramites){

        $puntoAtencion = $puntosDeAtencion[0];
        $intervalo = $this->getIntervalo();

        $grupoTramites = new GrupoTramite($puntoAtencion, 'gp::' . $puntoAtencion->getId() . '::', $this->getHorizonte(), 30);
        $grupoTramites->setPuntoAtencion($puntoAtencion);
        $grupoTramites->setIntervaloTiempo($intervalo['intervalo']);

        $grupoTramites->addTramite($tramites[0]);


        $io->text('Se genero: GRUPO TRAMITE '.'gp::' . $puntoAtencion->getId() . '::');
        $io->text('     para el Punto Atencion: '.$puntoAtencion->getNombre());
        $io->text('     para el Tramite: '.$tramites[0]->getNombre());
        $io->text('');

        $em->persist($grupoTramites);
    }


    private function getHorizonte(){
        $horizonte = [30, 60];
        return $horizonte[rand(0, 1)];
    }

    private function getIntervalo(){
        $intervalo = [
            ['intervalo' => 0.25, 'string' => '15 minutes'],
            ['intervalo' => 0.5, 'string' => '30 minutes'],
            ['intervalo' => 1, 'string' => '60 minutes']
        ];
        return $intervalo[rand(0, 2)];
    }

    protected function generateUserPda($io, $usuariosPda, $organismo, $area){
        // $usuario, $pass, $nombre, $apellido, $puntoDeAtencion

        $doctrine = $this->getContainer()->get('doctrine');
        $em = $doctrine->getManager();
        $rol = USER::ROL_PUNTOATENCION;

        $container = $this->getContainer();
        $encoder = $container->get('security.password_encoder');

        foreach ($usuariosPda as $upda) {
            $usuario = $upda[0];
            $pass = $upda[1];
            $nombre = $upda[2];
            $apellido = $upda[3];
            $puntoDeAtencion = $upda[4];
    
            if (!$em->getRepository('ApiV1Bundle:User')->findOneByUsername($usuario)){

                $user = new User($usuario, $rol);
                $user->setPassword($encoder->encodePassword($user, $pass));
                $em->persist($user);

                $usuarioPda = new UserPuntoAtencion($nombre, $apellido, $organismo, $area, $puntoDeAtencion, $user);
                $em->persist($usuarioPda);

                $this->printUserCreate($io, $usuario, $pass, $rol, false);
            }
            else{
                $this->printUserCreate($io, $usuario, $pass, $rol, true);
            }
        }
    }


    protected function generateUserArea($io, $usuario, $pass, $nombre, $apellido, $organismo, $area){
        $doctrine = $this->getContainer()->get('doctrine');
        $em = $doctrine->getManager();
        $rol = USER::ROL_AREA;

        $container = $this->getContainer();
        $encoder = $container->get('security.password_encoder');

        if (!$em->getRepository('ApiV1Bundle:User')->findOneByUsername($usuario)){

            $user = new User($usuario, $rol);
            $user->setPassword($encoder->encodePassword($user, $pass));
            $em->persist($user);

            $userRol = new UserArea($nombre, $apellido, $organismo, $area, $user);
            $em->persist($userRol);

            $this->printUserCreate($io, $usuario, $pass, $rol, false);
        }
        else{
            $this->printUserCreate($io, $usuario, $pass, $rol, true);
        }
    }

    protected function generateUserOrganismo($io, $usuario, $pass, $nombre, $apellido, $organismo){
        $doctrine = $this->getContainer()->get('doctrine');
        $em = $doctrine->getManager();
        $rol = USER::ROL_ORGANISMO;

        $container = $this->getContainer();
        $encoder = $container->get('security.password_encoder');

        if (!$em->getRepository('ApiV1Bundle:User')->findOneByUsername($usuario)){

            $user = new User($usuario, $rol);
            $user->setPassword($encoder->encodePassword($user, $pass));
            $em->persist($user);

            $userRol = new UserOrganismo($nombre, $apellido, $organismo, $user);
            $em->persist($userRol);

            $this->printUserCreate($io, $usuario, $pass, $rol, false);
        }
        else{
            $this->printUserCreate($io, $usuario, $pass, $rol, true);
        }
    }

    protected function generateUserAdmin($io, $usuario, $pass, $nombre, $apellido){
        $doctrine = $this->getContainer()->get('doctrine');
        $em = $doctrine->getManager();
        $rol = USER::ROL_ADMIN;
        
        $container = $this->getContainer();
        $encoder = $container->get('security.password_encoder');

        if (!$em->getRepository('ApiV1Bundle:User')->findOneByUsername($usuario)){

            $user = new User($usuario, $rol);
            $user->setPassword($encoder->encodePassword($user, $pass));
            $em->persist($user);

            $userRol = new Admin($nombre, $apellido, $user);
            $em->persist($userRol);

            $this->printUserCreate($io, $usuario, $pass, $rol, false);
        }
        else{
            $this->printUserCreate($io, $usuario, $pass, $rol, true);
        }
    }

    protected function printUserCreate($io, $user, $pass, $rol, $exist){

        if($exist){
            $io->text('El USUARIO: '.$user.' ya existe.');
            $io->text('     rol: '.$this->getRole($rol));
        }
        else{
            $io->text('Se genero: USUARIO '.$this->getRole($rol));
            $io->text('     usuario: '.$user);
        }
        $io->text('     password: '.$pass);
        $io->text('');
    }

    protected function getRole($id){
        $roles = [
            1 => 'ROL_ADMIN',
            2 => 'ROL_ORGANISMO',
            3 => 'ROL_AREA',
            4 => 'ROL_PUNTOATENCION'
        ];

        return $roles[$id];
    }

    protected function generateHorarios($io, $em, $puntosDeAtencion){
        
        $horarios = [];
        foreach ($puntosDeAtencion as $puntoAtencion) {
            // obtengo los tramites
            $tramites = $puntoAtencion->getTramites();
            // los puntos de atención que tienen tramites
        
            // $io->text('Punto de atención: ' . $puntoAtencion->getNombre());
            // de lunes a viernes
            for ($i = 1; $i <= 5; $i++) {
                $horario = $this->getHorario();

                // horario mañana
                $horarioManana = new HorarioAtencion(
                    $puntoAtencion,
                    $i,
                    new \DateTime($horario[0][0]),
                    new \DateTime($horario[0][1]),
                    $puntoAtencion->getId()
                );
                $horarios[] = $horarioManana;
                $em->persist($horarioManana);
                
                // horario tarde
                $horarioTarde = new HorarioAtencion(
                    $puntoAtencion,
                    $i,
                    new \DateTime($horario[1][0]),
                    new \DateTime($horario[1][1]),
                    $puntoAtencion->getId()
                );

                $horarios[] = $horarioTarde;
                $em->persist($horarioTarde);
            }

            $io->text('Se generaron horarios para el Punto de Atencion: '.$puntoAtencion->getNombre());
        }

        $io->text('');
        $em->flush();

        return $horarios;
    }



    protected function checkPuntoAtencionTest($pdatencion){
        return $pdatencion->getNombre() == "En la cancha" || $pdatencion->getNombre() == "Ticketek Argentina" || $pdatencion->getNombre() == "Sede Capital Federal";
    }

    protected function generarOrganismo($io, $em, $organismoData){

        $organismo = new Organismo($organismoData[0], $organismoData[1]);
        $em->persist($organismo);
        $io->text('Se genero: ORGANISMO');
        $io->text('     Nombre: '.$organismoData[0]);
        $io->text('     Alias: '.$organismoData[1]);
        $io->text('');

        return $organismo;
    }

    protected function generarArea($io, $em, $organismo, $areaData){

        $area = new Area($areaData[0], $areaData[1]);
        $area->setOrganismo($organismo);
        $em->persist($area);
        $io->text('Se genero: AREA');
        $io->text('     Nombre: '.$areaData[0]);
        $io->text('     Alias: '.$areaData[1]);
        $io->text('');

        return $area;
    }

    protected function generarPuntosAtencion($io, $em, $area){

        $pdaTest = [];
        $provincias = [];
        $localidades = [];

        $provinciaRepository = $em->getRepository('ApiV1Bundle:Provincia');
        foreach ($provinciaRepository->findAll() as $provincia) {
            if($provincia->getNombre() == "Buenos Aires" || $provincia->getNombre() == "Capital Federal"){
                $provincias[] = $provincia;
            }
        }
        
        // pda (punto de atencion) = Nombre, Provincia, Localidad (149 avellaneda,  31 san nicolas)
        $pdaTest[] = ["En la cancha", $provincias[0], $provincias[0]->getLocalidades()[149]]; 
        $pdaTest[] = ["Ticketek Argentina", $provincias[1], $provincias[1]->getLocalidades()[31]]; 
        $pdaTest[] = ["Sede Capital Federal", $provincias[1], $provincias[1]->getLocalidades()[31]]; 

        $puntosDeAtencion = [];

        foreach ($pdaTest as $pda) {
            // provincia
            $provincia = $pda[1];
            // localidad
            $localidad = $pda[2];
            // nombre pda
            $nombrePda = $pda[0];
            // punto de atención
            $puntoAtencion = new PuntoAtencion($nombrePda, 'Calle falsa 123');
            $puntoAtencion->setArea($area);
            $puntoAtencion->setLatitud(-34.6033);
            $puntoAtencion->setLongitud(-58.3816);
            $puntoAtencion->setProvincia($provincia);
            $puntoAtencion->setLocalidad($localidad);
            $puntoAtencion->setEstado(1);

            $container = $this->getContainer(); 
            $sncexternal = new SNCExternalService($container);
            $sncMock = new SNCExternalServiceMock($container);
            $pdaIntegration = new PuntoAtencionIntegration($container, $sncexternal, $sncMock);
            $pdaIntegration->agregarPuntoAtencion($puntoAtencion);








            $em->persist($puntoAtencion);
            $puntosDeAtencion[] = $puntoAtencion;

            $io->text('Se genero: PUNTO DE ATENCION');
            $io->text('     Nombre: '.$nombrePda);
            $io->text('     Area: '.$area->getNombre());
            $io->text('     Provincia: '.$provincia->getNombre());
            $io->text('     Localidad: '.$localidad->getNombre());
            $io->text('');
        }

        return $puntosDeAtencion;
    }

    private function getRequisitos(){
        $requisitos = 'Pellentesque pellentesque tincidunt facilisis. Maecenas dictum aliquet tortor id pharetra.|';
        $requisitos .= 'Cras porttitor sollicitudin augue, vel aliquam eros volutpat non.|';
        $requisitos .= 'Vivamus viverra tristique eros vel feugiat.';
        return $requisitos;
    }

    private function getFormulario(){
        $campos = '[{"description": "", "formComponent": {"typeValue": "text"}, "key": "nombre", "label": "Nombre", "order": 1, "required": true, "type": "textbox"}, {"description": "", "formComponent": {"typeValue": "text"}, "key": "apellido", "label": "Apellido", "order": 2, "required": true, "type": "textbox"}, {"description": "Puedes ingresar hasta 140 caracteres", "formComponent": {"rows": 4 }, "key": "comentarios", "label": "Comentarios", "order": 5, "required": false, "type": "textarea"}, {"description": "", "formComponent": {"options": [{"key": "option1", "value": "DNI"}, {"key": "option2", "value": "Pasaporte"}, {"key": "option3", "value": "CUIT"} ] }, "key": "tipo-documento", "label": "Tipo Documento", "order": 3, "required": false, "type": "dropdown"}, {"description": "", "formComponent": {"options": [{"key": "radio1", "value": "Femenino"}, {"key": "radio3", "value": "Masculino"} ] }, "key": "sexo", "label": "Sexo", "order": 4, "required": false, "type": "radio"}]';
        return json_decode($campos, true);
    }

    protected function generateTramitesArea($io, $em, $tramitesData, $area){

        $tramitesArea = [];

        foreach ($tramitesData as $tramiteNombre) {
            
            // create formulario
            $formulario = new Formulario($this->getFormulario());
            // create tramite
            $tramite = new Tramite($tramiteNombre, 1, $area);
            $tramite->setDuracion(15);
            $tramite->setRequisitos($this->getRequisitos());
            $tramite->setFormulario($formulario);
            $tramite->setExcepcional(1);
            $em->persist($tramite);
            $tramitesArea[] = $tramite;

            $io->text('Se genero: TRAMITE');
            $io->text('     Nombre: '.$tramiteNombre);
            $io->text('');
        }

        return $tramitesArea;
    }

    protected function generateRelacionPuntoAtencionTramites($io, $em, $puntosDeAtencion, $tramites){

        // $tramites
            // 0 tramite inutil
            // 1 incripcion
            // 2 inscripcion pileta
            // 3 venta de entradas

        // los puntos de atencion
            // 0 cancha
            // 1 ticketeck
            // 2 cap fed

        // $puntosDeAtencion[0]->addTramite($tramites[0]);
        // $puntosDeAtencion[0]->addTramite($tramites[1]);
        // $puntosDeAtencion[0]->addTramite($tramites[2]);
        // $puntosDeAtencion[0]->addTramite($tramites[3]);

        $io->text('Para el punto de atención: '.$puntosDeAtencion[0]->getNombre());
        $io->text('     Se relaciono el tramite: '.$tramites[0]->getNombre());
        $io->text('     Se relaciono el tramite: '.$tramites[1]->getNombre());
        $io->text('     Se relaciono el tramite: '.$tramites[2]->getNombre());
        $io->text('     Se relaciono el tramite: '.$tramites[3]->getNombre());
        $io->text('');

        // $puntosDeAtencion[1]->addTramite($tramites[3]);

        $io->text('Para el punto de atención: '.$puntosDeAtencion[1]->getNombre());
        $io->text('     Se relaciono el tramite: '.$tramites[3]->getNombre());
        $io->text('');

        // $puntosDeAtencion[2]->addTramite($tramites[1]);
        // $puntosDeAtencion[2]->addTramite($tramites[2]);
        // $puntosDeAtencion[2]->addTramite($tramites[3]);

        $io->text('Para el punto de atención: '.$puntosDeAtencion[2]->getNombre());
        $io->text('     Se relaciono el tramite: '.$tramites[1]->getNombre());
        $io->text('     Se relaciono el tramite: '.$tramites[2]->getNombre());
        $io->text('     Se relaciono el tramite: '.$tramites[3]->getNombre());
        $io->text('');
    }

    private function getHorario(){
        $horario = [
            [['09:00', '12:00'], ['13:00', '16:00']],
            [['09:00', '13:00'], ['14:00', '17:00']],
            [['10:00', '13:00'], ['15:00', '17:00']],
            [['10:00', '14:00'], ['15:00', '17:00']],
        ];
        return $horario[rand(0, 3)];
    }

    private function randomPassword($len = 8){
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass = [];
        $alphaLength = strlen($alphabet) - 1;
        for ($i = 0; $i < $len; $i ++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass);
    }
}
