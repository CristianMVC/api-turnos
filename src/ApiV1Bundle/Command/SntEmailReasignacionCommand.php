<?php

namespace ApiV1Bundle\Command;

use ApiV1Bundle\Entity\Reasignacion;
use ApiV1Bundle\ExternalServices\NotificationsExternalService;
use ApiV1Bundle\Repository\ReasignacionRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SntEmailReasignacionCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('snt:email:reasignacion');
        $this->setDescription('Envía mails de reasignación de turnos');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $notificationServices = new NotificationsExternalService($container);
        $logger = $container->get('logger');

        /** @var EntityManager $em */
        $em = $container->get('doctrine')->getManager();

        /** @var ReasignacionRepository $repository */
        $repository = $em->getRepository('ApiV1Bundle:Reasignacion');

        $pendientes = $repository->findByEnviada(0);
        foreach ($pendientes as $pendiente) {
            $campos = (array) $pendiente->getCampos();
            $response = $notificationServices->enviarNotificacion(
                $notificationServices->getEmailTemplate('reasignacion'),
                $campos['email'],
                $campos['cuil'],
                $campos
            );
            if (isset($response->id)) {
                $pendiente->setEnviada(1);
                $pendiente->setIdNotificacion($response->id);
                // log the response
                $output->writeln("Notification id {$response->id}");
            }
            // log the response
            $logger->info('Mensaje de reasignación enviado', (array) $response);
            sleep(1);
        }
        $em->flush();
    }
}
