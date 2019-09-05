<?php
namespace ApiV1Bundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use ApiV1Bundle\Entity\Tramite;
use ApiV1Bundle\Entity\Turno;
use ApiV1Bundle\Entity\DatosTurno;
use Ramsey\Uuid\Uuid;

/**
 * Class SntDatabaseTurnosCommand
 * @package ApiV1Bundle\Command
 *
 * Seeder de turnos
 */

class SntSeederTurnosCommand extends ContainerAwareCommand
{
    private $nombres = [
        'Santiago',
        'Mateo',
        'Juan',
        'Matias',
        'Nicolas',
        'Benjamin',
        'Pedro',
        'Tomas',
        'Thiago',
        'Santino',
        'Sofia',
        'Martina',
        'Valentina',
        'Mia',
        'Isabella',
        'Maria',
        'Zoe',
        'Catalina',
        'Emma',
        'Alma'
    ];

    private $apellidos = [
        'Castro',
        'Díaz',
        'Fernández',
        'García',
        'González',
        'Gómez',
        'López',
        'Martínez',
        'Pérez',
        'Rodríguez',
        'Romero',
        'Suárez',
        'Sánchez',
        'Vásquez',
        'Álvarez'
    ];
    /**
     * Método de configuración del Seeder
     */
    protected function configure()
    {
        $this->setName('snt:seeder:turnos');
        $this->setDescription('Seeder de turnos');
        $this->setHelp('Este comando llena la base de datos con turnos de prueba');
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
        $io->title('Generando turnos');
        // repositories
        $disponibilidadRepository = $em->getRepository('ApiV1Bundle:Disponibilidad');
        $turnoRepository = $em->getRepository('ApiV1Bundle:Turno');

        // generamos 25 turnos
        $total = $this->getTotal($disponibilidadRepository);
        for ($i = 0; $i < 25; $i++) {
            $disponibilidad = $this->getDisponibilidad($disponibilidadRepository, $total);
            if ($disponibilidad) {
                $puntoAtencion = $disponibilidad->getPuntoAtencion();
                $horario = $disponibilidad->getHorarioAtencion();
                $grupoTramites = $disponibilidad->getGrupoTramite();
                $tramite = $this->randTramite($grupoTramites->getTramites());
                $maxTurnos = $disponibilidad->getCantidadTurnos();
                $totalTurnos = $this->getTotalTurnos($turnoRepository, $puntoAtencion->getId(), $tramite->getId());

                if ($totalTurnos < $maxTurnos) {
                    $io->text('Generando turno para ' . $tramite->getNombre());
                    // turno
                    $turno = new Turno(
                        $puntoAtencion,
                        $tramite,
                        $this->randDate(),
                        $this->randTime($horario->getHoraInicio(), $horario->getHoraFin())
                    );
                    $turno->setEstado(Turno::ESTADO_ASIGNADO);
                    $turno->setAlerta(rand(1, 2));
                    // datos turno
                    $nombre = $this->getRandName();
                    $apellido = $this->getRandLast();
                    $cuil = rand(2012345676, 2093941676);
                    $email = $this->getRandEmail();
                    $telefono = rand(45698745, 48225466);
                    $campos = json_encode([
                        'lorem' => 'ipsum',
                        'dolor' => 'sit amet',
                        'consectetur' => 'adipiscing elit'
                    ]);
                    $datosTurno = new DatosTurno($nombre, $apellido, $cuil, $email, $telefono);
                    $datosTurno->setCampos($campos);
                    $datosTurno->setTurno($turno);
                    $turno->setDatosTurno($datosTurno);
                    $em->persist($turno);
                    $em->persist($datosTurno);
                    // guardar
                    $em->flush();
                }
            }
        }
        $io->text('Done!');
    }

    /**
     * Obtiene el total de entradas en la disponibilidad
     *
     * @param $repository
     * @return number
     */
    private function getTotal($repository)
    {
        $query = $repository->createQueryBuilder('d');
        $query->select('count(d.id)');
        $total = $query->getQuery()->getSingleScalarResult();
        return (int) $total;
    }

    /**
     * Obtiene una disponibilidad random
     *
     * @param $repository
     * @param $total
     * @return mixed
     */
    private function getDisponibilidad($repository, $total)
    {
        $offset = rand(1, $total);

        $query = $repository->createQueryBuilder('d');
        $query->setFirstResult($offset);
        $query->setMaxResults(1);
        $query->orderBy('d.id', 'ASC');
        return $query->getQuery()->getOneOrNullResult();
    }

    /**
     * Obtiene un tramite random
     *
     * @param $tramites
     * @return Tramite
     */
    private function randTramite($tramites)
    {
        $tramitesArr = [];
        foreach ($tramites as $tramite) {
            $tramitesArr[] = $tramite;
        }
        return $tramitesArr[rand(0, count($tramitesArr) - 1)];
    }

    /**
     * Total turnos dados
     *
     * @param $repository
     * @param $puntoAtencionId
     * @param $tramiteId
     * @return number
     */
    private function getTotalTurnos($repository, $puntoAtencionId, $tramiteId)
    {
        $query = $repository->createQueryBuilder('t');
        $query->select('count(t.id)');
        $query->join('t.puntoAtencion', 'pa');
        $query->join('t.tramite', 'tr');
        $query->where('pa.id = :puntoAtencionId')->setParameter('puntoAtencionId', $puntoAtencionId);
        $query->andWHere('tr.id = :tramiteId')->setParameter('tramiteId', $tramiteId);
        $total = $query->getQuery()->getSingleScalarResult();
        return (int) $total;
    }

    /**
     * Nombre random
     *
     * @return string
     */
    private function getRandName()
    {
        return $this->nombres[rand(0, count($this->nombres) - 1)];
    }

    /**
     * Apellido random
     *
     * @return string
     */
    private function getRandLast()
    {
        return $this->apellidos[rand(0, count($this->apellidos) - 1)];
    }

    /**
     * Obtener un email random
     *
     * @return string
     */
    private function getRandEmail()
    {
        $domains = ['@gmail.com', '@hotmail.com', '@yahoo.com', '@gmx.com', '@outlook.com'];
        $email = substr(uniqid('', true), -8);
        $email .= $domains[rand(0, 4)];
        return $email;
    }

    /**
     * Obtiene una fecha random
     *
     * @return \DateTime
     */
    private function randDate()
    {
        $minDate = time();
        $maxDate = $minDate + 2592000; // now plus 30 days
        $randDate = rand($minDate, $maxDate);
        return new \DateTime(date('c', $randDate));
    }

    /**
     * Obtiene una hora random
     * @param $minTime
     * @param $maxTime
     * @return \DateTime
     */
    private function randTime($minTime, $maxTime)
    {
        $minutes = ['00', '15', '30', '45'];
        $minTime = (int) $minTime->format('H');
        $maxTime = (int) $maxTime->format('H');
        $randHour = rand($minTime, $maxTime);
        $randMinute = $minutes[rand(0, count($minutes) - 1)];
        return new \DateTime($randHour . ':' . $randMinute . ':00');
    }

    /**
     * Retorna un md5 de un microtime()
     *
     * @return string
     */
    private function genCodigo()
    {
        return Uuid::uuid4();
    }
}
