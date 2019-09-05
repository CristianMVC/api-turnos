<?php

namespace ApiV1Bundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class SntNotificacionesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('snt:notificaciones');
        $this->setDescription('Acciones a ejecutar con la API de notificaciones');
        $this->setHelp('Esto comando permite realizar tareas basicas con la API de notificaciones');
        $this->addArgument('action', InputArgument::REQUIRED, 'La acción a ejecutar. Usar ayuda para la lista completa');
        $this->addOption('template', null, InputOption::VALUE_OPTIONAL, 'El nombre del template');
        $this->addOption('data', null, InputOption::VALUE_OPTIONAL, 'El json con los datos de prueba');
        $this->addOption('filename', null, InputOption::VALUE_OPTIONAL, 'El archivo donde está el template');
        $this->addOption('notificacion', null, InputOption::VALUE_OPTIONAL, 'Verifica el estado de una notificacion');
        $this->addOption('email', null, InputOption::VALUE_OPTIONAL, 'A quien le va a llegar el mail');
        $this->addOption('cuil', null, InputOption::VALUE_OPTIONAL, 'El cuil del ciudadano para validacion');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $action = $input->getArgument('action');
        $template = $input->getOption('template');
        $data = $input->getOption('data');
        $filename = $input->getOption('filename');
        $idNotificacion = $input->getOption('notificacion');
        $email = $input->getOption('email');
        $cuil = $input->getOption('cuil');

        $notificaciones = $this->getContainer()->get('snt.notificaciones.service');
        // acciones
        switch ($action) {
            case 'config':
                $this->config($notificaciones, $output);
                break;
            case 'token':
                $this->token($notificaciones, $output);
                break;
            case 'templates':
                $this->templates($notificaciones, $output);
                break;
            case 'template:obtener':
                $this->templates($notificaciones, $output, $template);
                break;
            case 'template:crear':
                $this->crearTemplate($notificaciones, $output, $template, $filename);
                break;
            case 'template:editar':
                $this->editarTemplate($notificaciones, $output, $template, $filename);
                break;
            case 'notif:enviar':
                $this->enviarNotificacion($notificaciones, $output, $template, $email, $cuil, $data);
                break;
            case 'notif:verificar':
                $this->verificarNotificacion($notificaciones, $output, $idNotificacion);
                break;
            case 'test':
                $this->templateTest($notificaciones, $output, $template, $data);
                break;
            default:
                $this->help($output);
        }
    }

    private function help($output)
    {
        $this->title('Listado de acciones', $output);
        $output->writeln('ayuda');
        $output->writeln('    muestra este menu');
        $output->writeln('config');
        $output->writeln('    muestra la configuración actual que usa el servicio');
        $output->writeln('token');
        $output->writeln('    genera un token para conectarse con la API');
        $output->writeln('templates');
        $output->writeln('    lista los templates');
        $output->writeln('template:crear --template=templateName --filename=path_to_file');
        $output->writeln('    genera un nuevo template');
        $output->writeln('template:editar --template=templateName --filename=path_to_file');
        $output->writeln('    edita un template');
        $output->writeln('template:obtener --template=templateName');
        $output->writeln('    devuelve el template por nombre');
        $output->writeln('test --template=templateName --data=\'{"email": "fcarrera@hexacta.com", "nombre": "Fausto Carrera"}\'');
        $output->writeln('    test del template');
        $output->writeln('notif:enviar --template=templateName --email=nobody@example.com --cuil=1234567890 --data=\'{"nombre": "Fausto Carrera"}\'');
        $output->writeln('    enviar notificacion');
        $output->writeln('notif:verificar --notificacion=100');
        $output->writeln('    verificar estado de una notificacion');
        $output->writeln('');
    }

    private function config($service, $output)
    {
        $this->title('Configuracion', $output);
        dump($service->getConfig());
    }

    private function token($service, $output)
    {
        $this->title('Token', $output);
        $response = $service->getToken();
        dump($response);
    }

    private function templates($service, $output, $templateName = null)
    {
        $this->title('Templates', $output);
        $response = $service->getTemplate($templateName);
        dump($response);
    }

    private function templateTest($service, $output, $templateName, $data)
    {
        $this->title('Validación de template', $output);
        $response = $service->testTemplate($templateName, $data);
        dump($response);
    }

    private function crearTemplate($service, $output, $templateName, $filename)
    {
        $this->title('Crear template', $output);
        $template = $this->readFile($filename);
        if ($template) {
            $response = $service->crearTemplate($templateName, $template);
            dump($response);
        }
    }

    private function editarTemplate($service, $output, $templateName, $filename)
    {
        $this->title('Editar template', $output);
        $template = $this->readFile($filename);
        if ($template) {
            $response = $service->editarTemplate($templateName, $template);
            dump($response);
        }
    }

    private function enviarNotificacion($service, $output, $templateName, $email, $cuil, $data)
    {
        $this->title('Enviar notificiación', $output);
        $response = $service->enviarNotificacion($templateName, $email, $cuil, json_decode($data, true));
        dump($response);
    }

    private function verificarNotificacion($service, $output, $idNotificacion)
    {
        $this->title('Verificar notificiación', $output);
        $response = $service->verificarNotificacion($idNotificacion);
        dump($response);
    }

    private function title($title, $output)
    {
        $output->writeln('===================');
        $output->writeln($title);
        $output->writeln('===================');
    }

    private function readFile($filename)
    {
        if (file_exists($filename)) {
            $fileHandler = fopen($filename, 'r');
            $data = fread($fileHandler, filesize($filename));
            fclose($fileHandler);
            return $data;
        }
        return null;
    }
}
