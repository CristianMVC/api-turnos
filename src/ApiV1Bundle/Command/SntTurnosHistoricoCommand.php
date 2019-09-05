<?php

namespace ApiV1Bundle\Command;

use ApiV1Bundle\Entity\DatosTurnoHistorico;
use ApiV1Bundle\Entity\TurnoHistorico;
use ApiV1Bundle\Repository\TurnoRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SntTurnosHistoricoCommand extends ContainerAwareCommand
{
    /** @var TurnoRepository */
    private $turnoRepository;

    /** @var TurnoRepository */
    private $turnoHistoricoRepository;

    protected function configure()
    {
        $this
            ->setName('snt:turnos:historico')
            ->setDescription('Pasa los turnos pasados a la tabla de históricos');
    }

    /**
     * Ejecuta el borrado físico de los datos de un turno
     *
     * @param EntityManager $em
     * @param $id
     * @throws \Doctrine\DBAL\DBALException
     */
    private function deleteDatosTurnos(EntityManager $em, $id)
    {
        $query = $em->getConnection();
        $query->exec("DELETE FROM datos_turno WHERE id = $id");
    }

    /**
     * Ejecuta el borrado físico de un turno
     *
     * @param EntityManager $em
     * @param $id
     * @throws \Doctrine\DBAL\DBALException
     */
    private function deleteTurno(EntityManager $em, $id)
    {
        $query = $em->getConnection();
        $query->exec("DELETE FROM turno WHERE id = $id");
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        /** @var EntityManager $em */
        $em = $container->get('doctrine')->getManager();

        $em->transactional(function (EntityManager $em) use ($output) {
            $this->turnoRepository = $em->getRepository('ApiV1Bundle:Turno');
            $this->turnoHistoricoRepository = $em->getRepository('ApiV1Bundle:TurnoHistorico');
            $turnosPasados = $this->turnoRepository->findTurnosPasados();

            foreach ($turnosPasados as $turno) {
                if ($this->turnoHistoricoRepository->findOneById($turno->getId())) {
                    continue;
                }

                $datos = $turno->getDatosTurno();
                $historico = new TurnoHistorico($turno);
                $em->persist($historico);
                $this->deleteTurno($em, $turno->getId());

                if ($datos) {
                    $datosHistorico = new DatosTurnoHistorico($turno->getDatosTurno(), $historico);
                    $historico->setDatosTurno($datosHistorico);
                    $em->persist($datos);

                    $this->deleteDatosTurnos($em, $datos->getId());
                }
            }
        });

        $output->writeln("Done");
    }
}
