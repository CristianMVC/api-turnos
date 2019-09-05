<?php

namespace ApiV1Bundle\Command;

use ApiV1Bundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SntUserMigrateCommand extends ContainerAwareCommand
{

    private $excepciones = [
        'cferreiros@snr.gob.ar' => [
            'discr' => 'organismo',
            'id' => 6,
            'rol_id' => User::ROL_ORGANISMO
        ]
    ];

    protected function configure()
    {
        $this->setName('snt:user:migrate');
        $this->setDescription('Ejecuta la miración de usuarios al nuevo esquema');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $em = $container->get('doctrine')->getManager();
        $areaRepo = $em->getRepository('ApiV1Bundle:Area');
        $pdaRepo = $em->getRepository('ApiV1Bundle:PuntoAtencion');
        $userRepo = $em->getRepository('ApiV1Bundle:User');

        $query = $em->createQuery("SELECT u FROM ApiV1Bundle\\Entity\\User u");
        $users = $query->getResult();

        $conn = $em->getConnection();

        foreach ($users as $user) {
            $id = $user->getId();
            $username = $user->getUsername();

            if (array_key_exists($username, $this->excepciones)) {
                $excepcion = $this->excepciones[$username];
                $user->setRol($excepcion['rol_id']);

                $data = [
                    'user_id' => $id,
                    'discr' => $excepcion['discr']
                ];

                switch ($excepcion['discr']) {
                    case 'organismo':
                        $data['organismo_id'] = $excepcion['id'];
                        break;
                    case 'area':
                        $data['area_id'] = $excepcion['id'];
                        $area = $areaRepo->findOneById($excepcion['id']);
                        $data['organismo_id'] = $area->getOrganismo()->getId();
                        break;
                    case 'puntoatencion':
                        $data['puntoatencion_id'] = $excepcion['id'];
                        $pda = $pdaRepo->findOneById($excepcion['id']);
                        $data['area_id'] = $pda->getArea()->getId();
                        $data['organismo_id'] = $pda->getArea()->getOrganismo()->getId();
                        break;
                }
            } else {
                $data = [
                    'user_id' => $id,
                    'discr' => 'admin'
                ];
            }

            $data['nombre'] = '';
            $data['apellido'] = '';
            $conn->insert('usuario', $data);
            $usuarioId = $this->getUsuarioId($id);

            $now = date("Y-m-d H:i:s");
            $dataUsuario = [
                'id' => $usuarioId,
                'fecha_creado' => $now,
                'fecha_modificado' => $now
            ];

            switch ($data['discr']) {
                case 'organismo':
                    $conn->insert('user_organismo', $dataUsuario);
                    break;
                case 'area':
                    $conn->insert('user_area', $dataUsuario);
                    break;
                case 'puntoatencion':
                    $conn->insert('user_punto_atencion', $dataUsuario);
                    break;
                case 'admin':
                    $conn->insert('user_admin', $dataUsuario);
                    break;
            }
        }

        $userRepo->flush();
    }

    private function getUsuarioId($id)
    {
        $container = $this->getContainer();
        $em = $container->get('doctrine')->getManager();

        $query = $em->createQuery("SELECT u.id FROM ApiV1Bundle\\Entity\\Usuario u JOIN u.user a WHERE a.id = $id");
        $usuarioId = $query->getSingleScalarResult();
        return $usuarioId;
    }
}
