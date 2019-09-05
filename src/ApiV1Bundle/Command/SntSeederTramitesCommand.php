<?php
namespace ApiV1Bundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use ApiV1Bundle\Entity\Tramite;
use ApiV1Bundle\Entity\Formulario;

/**
 * Class SntDatabaseTramitesCommand
 * @package ApiV1Bundle\Command
 *
 *  Seeder para carga de trámites
 */

class SntSeederTramitesCommand extends ContainerAwareCommand
{

    /**
     * * Método de configuración del Seeder
     */
    protected function configure()
    {
        $this->setName('snt:seeder:tramites');
        $this->setDescription('Seeder de los tramites');
        $this->setHelp('Este comando llena la base de datos con tramites de prueba');
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
        // organismos
        $organismoRepository = $em->getRepository('ApiV1Bundle:Organismo');
        $organismos = [];
        foreach ($organismoRepository->findAll() as $organismo) {
            $organismos[] = $organismo;
        }
        // generar 10 tramites random
        $io->title('Generando tramites');
        // repositories
        $count = 0;
        while ($count < 50) {
            $organismo = $this->getOrganismo($organismos);
            $area = $this->getArea($organismo->getAreas());
            if ($area) {
                $io->text('Generando tramite para ' . $organismo->getNombre() . '::' . $area->getNombre());
                // create formulario
                $formulario = new Formulario($this->getFormulario());
                // create tramite
                $tramite = new Tramite($this->getNombreRand(), 1, $area);
                $tramite->setDuracion(rand(15, 60));
                $tramite->setRequisitos($this->getRequisitos());
                $tramite->setFormulario($formulario);
                $em->persist($tramite);
                $count++;
            }
        }
        $em->flush();
        $io->text('Done!');
    }

    /**
     * Obtener un organismo random
     * @param $repository
     * @param $total
     * @return mixed
     */
    private function getOrganismo($organismos)
    {
        return $organismos[rand(0, count($organismos) - 1)];
    }

    /**
     * Obtener un area random
     *
     * @param $areas
     * @return mixed
     */
    private function getArea($areas)
    {
        return $areas[rand(0, count($areas) - 1)];
    }

    /**
     * Obtener un nombre random
     *
     * @return string
     */
    private function getNombreRand()
    {
        $names = [
            'lorem ipsum dolor sit amet',
            'consectetur adipiscing elit',
            'nullam eleifend risus',
            'et dui accumsan',
            'id viverra nibh dictum',
            'donec in leo sit amet tortor',
            'maximus dapibus vel sed ante',
            'mauris a sapien a diam',
            'dapibus posuere',
            'morbi ornare ligula id mauris luctus',
            'vitae fermentum dolor commodo',
            'suspendisse accumsan mauris',
            'sed arcu malesuada',
            'eget rhoncus turpis tincidunt',
            'praesent ultrices purus in',
            'ultricies tincidunt',
            'nam fringilla risus vitae',
            'blandit aliquet',
            'aenean tempor enim',
            'vitae justo mollis',
            'ac feugiat mi rutrum',
            'vestibulum lacinia turpis',
            'in ipsum imperdiet',
            'quis mattis lectus pulvinar',
            'aenean in enim ac nulla',
            'consequat aliquam',
            'sed gravida magna sit amet',
            'odio blandit imperdiet',
            'nullam a nibh vitae',
            'est vulputate elementum',
        ];
        return ucfirst($names[rand(0, count($names) - 1)]);
    }

    /**
     * Obtener los requisitos
     *
     * @return string
     */
    private function getRequisitos()
    {
        $requisitos = 'Pellentesque pellentesque tincidunt facilisis. Maecenas dictum aliquet tortor id pharetra.|';
        $requisitos .= 'Cras porttitor sollicitudin augue, vel aliquam eros volutpat non.|';
        $requisitos .= 'Vivamus viverra tristique eros vel feugiat.';
        return $requisitos;
    }

    /**
     * Obtener el formulario
     *
     * @return string
     */
    private function getFormulario()
    {
        $campos = '[{"description": "", "formComponent": {"typeValue": "text"}, "key": "nombre", "label": "Nombre", "order": 1, "required": true, "type": "textbox"}, {"description": "", "formComponent": {"typeValue": "text"}, "key": "apellido", "label": "Apellido", "order": 2, "required": true, "type": "textbox"}, {"description": "Puedes ingresar hasta 140 caracteres", "formComponent": {"rows": 4 }, "key": "comentarios", "label": "Comentarios", "order": 5, "required": false, "type": "textarea"}, {"description": "", "formComponent": {"options": [{"key": "option1", "value": "DNI"}, {"key": "option2", "value": "Pasaporte"}, {"key": "option3", "value": "CUIT"} ] }, "key": "tipo-documento", "label": "Tipo Documento", "order": 3, "required": false, "type": "dropdown"}, {"description": "", "formComponent": {"options": [{"key": "radio1", "value": "Femenino"}, {"key": "radio3", "value": "Masculino"} ] }, "key": "sexo", "label": "Sexo", "order": 4, "required": false, "type": "radio"}]';
        return json_decode($campos, true);
    }
}
