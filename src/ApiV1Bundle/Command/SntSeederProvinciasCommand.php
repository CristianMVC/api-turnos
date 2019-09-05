<?php
namespace ApiV1Bundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use ApiV1Bundle\Entity\Provincia;
use ApiV1Bundle\Entity\Localidad;

/**
 * Class SntDatabaseProvinciasCommand
 * @package ApiV1Bundle\Command
 * Seeder de provincias y localidades de argentina
 * @author Fausto Carrera <fcarrera@hexacta.com>
 * Source: https://djjavi707.wordpress.com/2006/10/15/listado-de-provincias-y-localidades-de-argentina/
 *
 */


class SntSeederProvinciasCommand extends ContainerAwareCommand
{
    private $provincias = [];

    /**
     * Método de configuración del Seeder
     */
    protected function configure()
    {
        $this->setName('snt:seeder:provincias');
        $this->setDescription('Seeder de las provincias');
        $this->setHelp('Este comando llena la base de datos con las provincias y localidades');
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

        $io->title('Generando provincias y localidades');
        // locations
        $locations = $this->getLocations();
        foreach ($locations as $entry) {
            $provinciaNombre = $entry[0];
            $localidadNombre = $entry[1];
            // chequeamos si la provincia fue agregada a la base de datos
            if (! array_key_exists($provinciaNombre, $this->provincias)) {
                $io->text('Guardando provincia: ' . $provinciaNombre);
                $provincia = new Provincia();
                $provincia->setNombre($provinciaNombre);
                $em->persist($provincia);
                $this->provincias[$provinciaNombre] = $provincia;
            } else {
                $provincia = $this->provincias[$provinciaNombre];
            }
            // guardar la localidad
            $localidad = new Localidad();
            $localidad->setProvincia($provincia);
            $localidad->setNombre($localidadNombre);
            $em->persist($localidad);
        }
        $em->flush();
        $io->text('Done!');
    }

    /**
     * Obtiene las localidades
     *
     * @return array
     */

    private function getLocations()
    {
        $location = [];
        $location[] = [
            'Buenos Aires',
            '25 de Mayo'
        ];
        $location[] = [
            'Buenos Aires',
            '3 de febrero'
        ];
        $location[] = [
            'Buenos Aires',
            'A. Alsina'
        ];
        $location[] = [
            'Buenos Aires',
            'A. Gonzáles Cháves'
        ];
        $location[] = [
            'Buenos Aires',
            'Aguas Verdes'
        ];
        $location[] = [
            'Buenos Aires',
            'Alberti'
        ];
        $location[] = [
            'Buenos Aires',
            'Arrecifes'
        ];
        $location[] = [
            'Buenos Aires',
            'Ayacucho'
        ];
        $location[] = [
            'Buenos Aires',
            'Azul'
        ];
        $location[] = [
            'Buenos Aires',
            'Bahía Blanca'
        ];
        $location[] = [
            'Buenos Aires',
            'Balcarce'
        ];
        $location[] = [
            'Buenos Aires',
            'Baradero'
        ];
        $location[] = [
            'Buenos Aires',
            'Benito Juárez'
        ];
        $location[] = [
            'Buenos Aires',
            'Berisso'
        ];
        $location[] = [
            'Buenos Aires',
            'Bolívar'
        ];
        $location[] = [
            'Buenos Aires',
            'Bragado'
        ];
        $location[] = [
            'Buenos Aires',
            'Brandsen'
        ];
        $location[] = [
            'Buenos Aires',
            'Campana'
        ];
        $location[] = [
            'Buenos Aires',
            'Cañuelas'
        ];
        $location[] = [
            'Buenos Aires',
            'Capilla del Señor'
        ];
        $location[] = [
            'Buenos Aires',
            'Capitán Sarmiento'
        ];
        $location[] = [
            'Buenos Aires',
            'Carapachay'
        ];
        $location[] = [
            'Buenos Aires',
            'Carhue'
        ];
        $location[] = [
            'Buenos Aires',
            'Cariló'
        ];
        $location[] = [
            'Buenos Aires',
            'Carlos Casares'
        ];
        $location[] = [
            'Buenos Aires',
            'Carlos Tejedor'
        ];
        $location[] = [
            'Buenos Aires',
            'Carmen de Areco'
        ];
        $location[] = [
            'Buenos Aires',
            'Carmen de Patagones'
        ];
        $location[] = [
            'Buenos Aires',
            'Castelli'
        ];
        $location[] = [
            'Buenos Aires',
            'Chacabuco'
        ];
        $location[] = [
            'Buenos Aires',
            'Chascomús'
        ];
        $location[] = [
            'Buenos Aires',
            'Chivilcoy'
        ];
        $location[] = [
            'Buenos Aires',
            'Colón'
        ];
        $location[] = [
            'Buenos Aires',
            'Coronel Dorrego'
        ];
        $location[] = [
            'Buenos Aires',
            'Coronel Pringles'
        ];
        $location[] = [
            'Buenos Aires',
            'Coronel Rosales'
        ];
        $location[] = [
            'Buenos Aires',
            'Coronel Suarez'
        ];
        $location[] = [
            'Buenos Aires',
            'Costa Azul'
        ];
        $location[] = [
            'Buenos Aires',
            'Costa Chica'
        ];
        $location[] = [
            'Buenos Aires',
            'Costa del Este'
        ];
        $location[] = [
            'Buenos Aires',
            'Costa Esmeralda'
        ];
        $location[] = [
            'Buenos Aires',
            'Daireaux'
        ];
        $location[] = [
            'Buenos Aires',
            'Darregueira'
        ];
        $location[] = [
            'Buenos Aires',
            'Del Viso'
        ];
        $location[] = [
            'Buenos Aires',
            'Dolores'
        ];
        $location[] = [
            'Buenos Aires',
            'Don Torcuato'
        ];
        $location[] = [
            'Buenos Aires',
            'Ensenada'
        ];
        $location[] = [
            'Buenos Aires',
            'Escobar'
        ];
        $location[] = [
            'Buenos Aires',
            'Exaltación de la Cruz'
        ];
        $location[] = [
            'Buenos Aires',
            'Florentino Ameghino'
        ];
        $location[] = [
            'Buenos Aires',
            'Garín'
        ];
        $location[] = [
            'Buenos Aires',
            'Gral. Alvarado'
        ];
        $location[] = [
            'Buenos Aires',
            'Gral. Alvear'
        ];
        $location[] = [
            'Buenos Aires',
            'Gral. Arenales'
        ];
        $location[] = [
            'Buenos Aires',
            'Gral. Belgrano'
        ];
        $location[] = [
            'Buenos Aires',
            'Gral. Guido'
        ];
        $location[] = [
            'Buenos Aires',
            'Gral. Lamadrid'
        ];
        $location[] = [
            'Buenos Aires',
            'Gral. Las Heras'
        ];
        $location[] = [
            'Buenos Aires',
            'Gral. Lavalle'
        ];
        $location[] = [
            'Buenos Aires',
            'Gral. Madariaga'
        ];
        $location[] = [
            'Buenos Aires',
            'Gral. Pacheco'
        ];
        $location[] = [
            'Buenos Aires',
            'Gral. Paz'
        ];
        $location[] = [
            'Buenos Aires',
            'Gral. Pinto'
        ];
        $location[] = [
            'Buenos Aires',
            'Gral. Pueyrredón'
        ];
        $location[] = [
            'Buenos Aires',
            'Gral. Rodríguez'
        ];
        $location[] = [
            'Buenos Aires',
            'Gral. Viamonte'
        ];
        $location[] = [
            'Buenos Aires',
            'Gral. Villegas'
        ];
        $location[] = [
            'Buenos Aires',
            'Guaminí'
        ];
        $location[] = [
            'Buenos Aires',
            'Guernica'
        ];
        $location[] = [
            'Buenos Aires',
            'Hipólito Yrigoyen'
        ];
        $location[] = [
            'Buenos Aires',
            'Ing. Maschwitz'
        ];
        $location[] = [
            'Buenos Aires',
            'Junín'
        ];
        $location[] = [
            'Buenos Aires',
            'La Plata'
        ];
        $location[] = [
            'Buenos Aires',
            'Laprida'
        ];
        $location[] = [
            'Buenos Aires',
            'Las Flores'
        ];
        $location[] = [
            'Buenos Aires',
            'Las Toninas'
        ];
        $location[] = [
            'Buenos Aires',
            'Leandro N. Alem'
        ];
        $location[] = [
            'Buenos Aires',
            'Lincoln'
        ];
        $location[] = [
            'Buenos Aires',
            'Loberia'
        ];
        $location[] = [
            'Buenos Aires',
            'Lobos'
        ];
        $location[] = [
            'Buenos Aires',
            'Los Cardales'
        ];
        $location[] = [
            'Buenos Aires',
            'Los Toldos'
        ];
        $location[] = [
            'Buenos Aires',
            'Lucila del Mar'
        ];
        $location[] = [
            'Buenos Aires',
            'Luján'
        ];
        $location[] = [
            'Buenos Aires',
            'Magdalena'
        ];
        $location[] = [
            'Buenos Aires',
            'Maipú'
        ];
        $location[] = [
            'Buenos Aires',
            'Mar Chiquita'
        ];
        $location[] = [
            'Buenos Aires',
            'Mar de Ajó'
        ];
        $location[] = [
            'Buenos Aires',
            'Mar de las Pampas'
        ];
        $location[] = [
            'Buenos Aires',
            'Mar del Plata'
        ];
        $location[] = [
            'Buenos Aires',
            'Mar del Tuyú'
        ];
        $location[] = [
            'Buenos Aires',
            'Marcos Paz'
        ];
        $location[] = [
            'Buenos Aires',
            'Mercedes'
        ];
        $location[] = [
            'Buenos Aires',
            'Miramar'
        ];
        $location[] = [
            'Buenos Aires',
            'Monte'
        ];
        $location[] = [
            'Buenos Aires',
            'Monte Hermoso'
        ];
        $location[] = [
            'Buenos Aires',
            'Munro'
        ];
        $location[] = [
            'Buenos Aires',
            'Navarro'
        ];
        $location[] = [
            'Buenos Aires',
            'Necochea'
        ];
        $location[] = [
            'Buenos Aires',
            'Olavarría'
        ];
        $location[] = [
            'Buenos Aires',
            'Partido de la Costa'
        ];
        $location[] = [
            'Buenos Aires',
            'Pehuajó'
        ];
        $location[] = [
            'Buenos Aires',
            'Pellegrini'
        ];
        $location[] = [
            'Buenos Aires',
            'Pergamino'
        ];
        $location[] = [
            'Buenos Aires',
            'Pigüé'
        ];
        $location[] = [
            'Buenos Aires',
            'Pila'
        ];
        $location[] = [
            'Buenos Aires',
            'Pilar'
        ];
        $location[] = [
            'Buenos Aires',
            'Pinamar'
        ];
        $location[] = [
            'Buenos Aires',
            'Pinar del Sol'
        ];
        $location[] = [
            'Buenos Aires',
            'Polvorines'
        ];
        $location[] = [
            'Buenos Aires',
            'Pte. Perón'
        ];
        $location[] = [
            'Buenos Aires',
            'Puán'
        ];
        $location[] = [
            'Buenos Aires',
            'Punta Indio'
        ];
        $location[] = [
            'Buenos Aires',
            'Ramallo'
        ];
        $location[] = [
            'Buenos Aires',
            'Rauch'
        ];
        $location[] = [
            'Buenos Aires',
            'Rivadavia'
        ];
        $location[] = [
            'Buenos Aires',
            'Rojas'
        ];
        $location[] = [
            'Buenos Aires',
            'Roque Pérez'
        ];
        $location[] = [
            'Buenos Aires',
            'Saavedra'
        ];
        $location[] = [
            'Buenos Aires',
            'Saladillo'
        ];
        $location[] = [
            'Buenos Aires',
            'Salliqueló'
        ];
        $location[] = [
            'Buenos Aires',
            'Salto'
        ];
        $location[] = [
            'Buenos Aires',
            'San Andrés de Giles'
        ];
        $location[] = [
            'Buenos Aires',
            'San Antonio de Areco'
        ];
        $location[] = [
            'Buenos Aires',
            'San Antonio de Padua'
        ];
        $location[] = [
            'Buenos Aires',
            'San Bernardo'
        ];
        $location[] = [
            'Buenos Aires',
            'San Cayetano'
        ];
        $location[] = [
            'Buenos Aires',
            'San Clemente del Tuyú'
        ];
        $location[] = [
            'Buenos Aires',
            'San Nicolás'
        ];
        $location[] = [
            'Buenos Aires',
            'San Pedro'
        ];
        $location[] = [
            'Buenos Aires',
            'San Vicente'
        ];
        $location[] = [
            'Buenos Aires',
            'Santa Teresita'
        ];
        $location[] = [
            'Buenos Aires',
            'Suipacha'
        ];
        $location[] = [
            'Buenos Aires',
            'Tandil'
        ];
        $location[] = [
            'Buenos Aires',
            'Tapalqué'
        ];
        $location[] = [
            'Buenos Aires',
            'Tordillo'
        ];
        $location[] = [
            'Buenos Aires',
            'Tornquist'
        ];
        $location[] = [
            'Buenos Aires',
            'Trenque Lauquen'
        ];
        $location[] = [
            'Buenos Aires',
            'Tres Lomas'
        ];
        $location[] = [
            'Buenos Aires',
            'Villa Gesell'
        ];
        $location[] = [
            'Buenos Aires',
            'Villarino'
        ];
        $location[] = [
            'Buenos Aires',
            'Zárate'
        ];
        $location[] = [
            'Buenos Aires',
            '11 de Septiembre'
        ];
        $location[] = [
            'Buenos Aires',
            '20 de Junio'
        ];
        $location[] = [
            'Buenos Aires',
            '25 de Mayo'
        ];
        $location[] = [
            'Buenos Aires',
            'Acassuso'
        ];
        $location[] = [
            'Buenos Aires',
            'Adrogué'
        ];
        $location[] = [
            'Buenos Aires',
            'Aldo Bonzi'
        ];
        $location[] = [
            'Buenos Aires',
            'Área Reserva Cinturón Ecológico'
        ];
        $location[] = [
            'Buenos Aires',
            'Avellaneda'
        ];
        $location[] = [
            'Buenos Aires',
            'Banfield'
        ];
        $location[] = [
            'Buenos Aires',
            'Barrio Parque'
        ];
        $location[] = [
            'Buenos Aires',
            'Barrio Santa Teresita'
        ];
        $location[] = [
            'Buenos Aires',
            'Beccar'
        ];
        $location[] = [
            'Buenos Aires',
            'Bella Vista'
        ];
        $location[] = [
            'Buenos Aires',
            'Berazategui'
        ];
        $location[] = [
            'Buenos Aires',
            'Bernal Este'
        ];
        $location[] = [
            'Buenos Aires',
            'Bernal Oeste'
        ];
        $location[] = [
            'Buenos Aires',
            'Billinghurst'
        ];
        $location[] = [
            'Buenos Aires',
            'Boulogne'
        ];
        $location[] = [
            'Buenos Aires',
            'Burzaco'
        ];
        $location[] = [
            'Buenos Aires',
            'Carapachay'
        ];
        $location[] = [
            'Buenos Aires',
            'Caseros'
        ];
        $location[] = [
            'Buenos Aires',
            'Castelar'
        ];
        $location[] = [
            'Buenos Aires',
            'Churruca'
        ];
        $location[] = [
            'Buenos Aires',
            'Ciudad Evita'
        ];
        $location[] = [
            'Buenos Aires',
            'Ciudad Madero'
        ];
        $location[] = [
            'Buenos Aires',
            'Ciudadela'
        ];
        $location[] = [
            'Buenos Aires',
            'Claypole'
        ];
        $location[] = [
            'Buenos Aires',
            'Crucecita'
        ];
        $location[] = [
            'Buenos Aires',
            'Dock Sud'
        ];
        $location[] = [
            'Buenos Aires',
            'Don Bosco'
        ];
        $location[] = [
            'Buenos Aires',
            'Don Orione'
        ];
        $location[] = [
            'Buenos Aires',
            'El Jagüel'
        ];
        $location[] = [
            'Buenos Aires',
            'El Libertador'
        ];
        $location[] = [
            'Buenos Aires',
            'El Palomar'
        ];
        $location[] = [
            'Buenos Aires',
            'El Tala'
        ];
        $location[] = [
            'Buenos Aires',
            'El Trébol'
        ];
        $location[] = [
            'Buenos Aires',
            'Ezeiza'
        ];
        $location[] = [
            'Buenos Aires',
            'Ezpeleta'
        ];
        $location[] = [
            'Buenos Aires',
            'Florencio Varela'
        ];
        $location[] = [
            'Buenos Aires',
            'Florida'
        ];
        $location[] = [
            'Buenos Aires',
            'Francisco Álvarez'
        ];
        $location[] = [
            'Buenos Aires',
            'Gerli'
        ];
        $location[] = [
            'Buenos Aires',
            'Glew'
        ];
        $location[] = [
            'Buenos Aires',
            'González Catán'
        ];
        $location[] = [
            'Buenos Aires',
            'Gral. Lamadrid'
        ];
        $location[] = [
            'Buenos Aires',
            'Grand Bourg'
        ];
        $location[] = [
            'Buenos Aires',
            'Gregorio de Laferrere'
        ];
        $location[] = [
            'Buenos Aires',
            'Guillermo Enrique Hudson'
        ];
        $location[] = [
            'Buenos Aires',
            'Haedo'
        ];
        $location[] = [
            'Buenos Aires',
            'Hurlingham'
        ];
        $location[] = [
            'Buenos Aires',
            'Ing. Sourdeaux'
        ];
        $location[] = [
            'Buenos Aires',
            'Isidro Casanova'
        ];
        $location[] = [
            'Buenos Aires',
            'Ituzaingó'
        ];
        $location[] = [
            'Buenos Aires',
            'José C. Paz'
        ];
        $location[] = [
            'Buenos Aires',
            'José Ingenieros'
        ];
        $location[] = [
            'Buenos Aires',
            'José Marmol'
        ];
        $location[] = [
            'Buenos Aires',
            'La Lucila'
        ];
        $location[] = [
            'Buenos Aires',
            'La Reja'
        ];
        $location[] = [
            'Buenos Aires',
            'La Tablada'
        ];
        $location[] = [
            'Buenos Aires',
            'Lanús'
        ];
        $location[] = [
            'Buenos Aires',
            'Llavallol'
        ];
        $location[] = [
            'Buenos Aires',
            'Loma Hermosa'
        ];
        $location[] = [
            'Buenos Aires',
            'Lomas de Zamora'
        ];
        $location[] = [
            'Buenos Aires',
            'Lomas del Millón'
        ];
        $location[] = [
            'Buenos Aires',
            'Lomas del Mirador'
        ];
        $location[] = [
            'Buenos Aires',
            'Longchamps'
        ];
        $location[] = [
            'Buenos Aires',
            'Los Polvorines'
        ];
        $location[] = [
            'Buenos Aires',
            'Luis Guillón'
        ];
        $location[] = [
            'Buenos Aires',
            'Malvinas Argentinas'
        ];
        $location[] = [
            'Buenos Aires',
            'Martín Coronado'
        ];
        $location[] = [
            'Buenos Aires',
            'Martínez'
        ];
        $location[] = [
            'Buenos Aires',
            'Merlo'
        ];
        $location[] = [
            'Buenos Aires',
            'Ministro Rivadavia'
        ];
        $location[] = [
            'Buenos Aires',
            'Monte Chingolo'
        ];
        $location[] = [
            'Buenos Aires',
            'Monte Grande'
        ];
        $location[] = [
            'Buenos Aires',
            'Moreno'
        ];
        $location[] = [
            'Buenos Aires',
            'Morón'
        ];
        $location[] = [
            'Buenos Aires',
            'Muñiz'
        ];
        $location[] = [
            'Buenos Aires',
            'Olivos'
        ];
        $location[] = [
            'Buenos Aires',
            'Pablo Nogués'
        ];
        $location[] = [
            'Buenos Aires',
            'Pablo Podestá'
        ];
        $location[] = [
            'Buenos Aires',
            'Paso del Rey'
        ];
        $location[] = [
            'Buenos Aires',
            'Pereyra'
        ];
        $location[] = [
            'Buenos Aires',
            'Piñeiro'
        ];
        $location[] = [
            'Buenos Aires',
            'Plátanos'
        ];
        $location[] = [
            'Buenos Aires',
            'Pontevedra'
        ];
        $location[] = [
            'Buenos Aires',
            'Quilmes'
        ];
        $location[] = [
            'Buenos Aires',
            'Rafael Calzada'
        ];
        $location[] = [
            'Buenos Aires',
            'Rafael Castillo'
        ];
        $location[] = [
            'Buenos Aires',
            'Ramos Mejía'
        ];
        $location[] = [
            'Buenos Aires',
            'Ranelagh'
        ];
        $location[] = [
            'Buenos Aires',
            'Remedios de Escalada'
        ];
        $location[] = [
            'Buenos Aires',
            'Sáenz Peña'
        ];
        $location[] = [
            'Buenos Aires',
            'San Antonio de Padua'
        ];
        $location[] = [
            'Buenos Aires',
            'San Fernando'
        ];
        $location[] = [
            'Buenos Aires',
            'San Francisco Solano'
        ];
        $location[] = [
            'Buenos Aires',
            'San Isidro'
        ];
        $location[] = [
            'Buenos Aires',
            'San José'
        ];
        $location[] = [
            'Buenos Aires',
            'San Justo'
        ];
        $location[] = [
            'Buenos Aires',
            'San Martín'
        ];
        $location[] = [
            'Buenos Aires',
            'San Miguel'
        ];
        $location[] = [
            'Buenos Aires',
            'Santos Lugares'
        ];
        $location[] = [
            'Buenos Aires',
            'Sarandí'
        ];
        $location[] = [
            'Buenos Aires',
            'Sourigues'
        ];
        $location[] = [
            'Buenos Aires',
            'Tapiales'
        ];
        $location[] = [
            'Buenos Aires',
            'Temperley'
        ];
        $location[] = [
            'Buenos Aires',
            'Tigre'
        ];
        $location[] = [
            'Buenos Aires',
            'Tortuguitas'
        ];
        $location[] = [
            'Buenos Aires',
            'Tristán Suárez'
        ];
        $location[] = [
            'Buenos Aires',
            'Trujui'
        ];
        $location[] = [
            'Buenos Aires',
            'Turdera'
        ];
        $location[] = [
            'Buenos Aires',
            'Valentín Alsina'
        ];
        $location[] = [
            'Buenos Aires',
            'Vicente López'
        ];
        $location[] = [
            'Buenos Aires',
            'Villa Adelina'
        ];
        $location[] = [
            'Buenos Aires',
            'Villa Ballester'
        ];
        $location[] = [
            'Buenos Aires',
            'Villa Bosch'
        ];
        $location[] = [
            'Buenos Aires',
            'Villa Caraza'
        ];
        $location[] = [
            'Buenos Aires',
            'Villa Celina'
        ];
        $location[] = [
            'Buenos Aires',
            'Villa Centenario'
        ];
        $location[] = [
            'Buenos Aires',
            'Villa de Mayo'
        ];
        $location[] = [
            'Buenos Aires',
            'Villa Diamante'
        ];
        $location[] = [
            'Buenos Aires',
            'Villa Domínico'
        ];
        $location[] = [
            'Buenos Aires',
            'Villa España'
        ];
        $location[] = [
            'Buenos Aires',
            'Villa Fiorito'
        ];
        $location[] = [
            'Buenos Aires',
            'Villa Guillermina'
        ];
        $location[] = [
            'Buenos Aires',
            'Villa Insuperable'
        ];
        $location[] = [
            'Buenos Aires',
            'Villa José León Suárez'
        ];
        $location[] = [
            'Buenos Aires',
            'Villa La Florida'
        ];
        $location[] = [
            'Buenos Aires',
            'Villa Luzuriaga'
        ];
        $location[] = [
            'Buenos Aires',
            'Villa Martelli'
        ];
        $location[] = [
            'Buenos Aires',
            'Villa Obrera'
        ];
        $location[] = [
            'Buenos Aires',
            'Villa Progreso'
        ];
        $location[] = [
            'Buenos Aires',
            'Villa Raffo'
        ];
        $location[] = [
            'Buenos Aires',
            'Villa Sarmiento'
        ];
        $location[] = [
            'Buenos Aires',
            'Villa Tesei'
        ];
        $location[] = [
            'Buenos Aires',
            'Villa Udaondo'
        ];
        $location[] = [
            'Buenos Aires',
            'Virrey del Pino'
        ];
        $location[] = [
            'Buenos Aires',
            'Wilde'
        ];
        $location[] = [
            'Buenos Aires',
            'William Morris'
        ];
        $location[] = [
            'Capital Federal',
            'Agronomía'
        ];
        $location[] = [
            'Capital Federal',
            'Almagro'
        ];
        $location[] = [
            'Capital Federal',
            'Balvanera'
        ];
        $location[] = [
            'Capital Federal',
            'Barracas'
        ];
        $location[] = [
            'Capital Federal',
            'Belgrano'
        ];
        $location[] = [
            'Capital Federal',
            'Boca'
        ];
        $location[] = [
            'Capital Federal',
            'Boedo'
        ];
        $location[] = [
            'Capital Federal',
            'Caballito'
        ];
        $location[] = [
            'Capital Federal',
            'Chacarita'
        ];
        $location[] = [
            'Capital Federal',
            'Coghlan'
        ];
        $location[] = [
            'Capital Federal',
            'Colegiales'
        ];
        $location[] = [
            'Capital Federal',
            'Constitución'
        ];
        $location[] = [
            'Capital Federal',
            'Flores'
        ];
        $location[] = [
            'Capital Federal',
            'Floresta'
        ];
        $location[] = [
            'Capital Federal',
            'La Paternal'
        ];
        $location[] = [
            'Capital Federal',
            'Liniers'
        ];
        $location[] = [
            'Capital Federal',
            'Mataderos'
        ];
        $location[] = [
            'Capital Federal',
            'Monserrat'
        ];
        $location[] = [
            'Capital Federal',
            'Monte Castro'
        ];
        $location[] = [
            'Capital Federal',
            'Nueva Pompeya'
        ];
        $location[] = [
            'Capital Federal',
            'Núñez'
        ];
        $location[] = [
            'Capital Federal',
            'Palermo'
        ];
        $location[] = [
            'Capital Federal',
            'Parque Avellaneda'
        ];
        $location[] = [
            'Capital Federal',
            'Parque Chacabuco'
        ];
        $location[] = [
            'Capital Federal',
            'Parque Chas'
        ];
        $location[] = [
            'Capital Federal',
            'Parque Patricios'
        ];
        $location[] = [
            'Capital Federal',
            'Puerto Madero'
        ];
        $location[] = [
            'Capital Federal',
            'Recoleta'
        ];
        $location[] = [
            'Capital Federal',
            'Retiro'
        ];
        $location[] = [
            'Capital Federal',
            'Saavedra'
        ];
        $location[] = [
            'Capital Federal',
            'San Cristóbal'
        ];
        $location[] = [
            'Capital Federal',
            'San Nicolás'
        ];
        $location[] = [
            'Capital Federal',
            'San Telmo'
        ];
        $location[] = [
            'Capital Federal',
            'Vélez Sársfield'
        ];
        $location[] = [
            'Capital Federal',
            'Versalles'
        ];
        $location[] = [
            'Capital Federal',
            'Villa Crespo'
        ];
        $location[] = [
            'Capital Federal',
            'Villa del Parque'
        ];
        $location[] = [
            'Capital Federal',
            'Villa Devoto'
        ];
        $location[] = [
            'Capital Federal',
            'Villa Gral. Mitre'
        ];
        $location[] = [
            'Capital Federal',
            'Villa Lugano'
        ];
        $location[] = [
            'Capital Federal',
            'Villa Luro'
        ];
        $location[] = [
            'Capital Federal',
            'Villa Ortúzar'
        ];
        $location[] = [
            'Capital Federal',
            'Villa Pueyrredón'
        ];
        $location[] = [
            'Capital Federal',
            'Villa Real'
        ];
        $location[] = [
            'Capital Federal',
            'Villa Riachuelo'
        ];
        $location[] = [
            'Capital Federal',
            'Villa Santa Rita'
        ];
        $location[] = [
            'Capital Federal',
            'Villa Soldati'
        ];
        $location[] = [
            'Capital Federal',
            'Villa Urquiza'
        ];
        $location[] = [
            'Catamarca',
            'Aconquija'
        ];
        $location[] = [
            'Catamarca',
            'Ancasti'
        ];
        $location[] = [
            'Catamarca',
            'Andalgalá'
        ];
        $location[] = [
            'Catamarca',
            'Antofagasta'
        ];
        $location[] = [
            'Catamarca',
            'Belén'
        ];
        $location[] = [
            'Catamarca',
            'Capayán'
        ];
        $location[] = [
            'Catamarca',
            'Capital'
        ];
        $location[] = [
            'Catamarca',
            'Catamarca'
        ];
        $location[] = [
            'Catamarca',
            'Corral Quemado'
        ];
        $location[] = [
            'Catamarca',
            'El Alto'
        ];
        $location[] = [
            'Catamarca',
            'El Rodeo'
        ];
        $location[] = [
            'Catamarca',
            'F.Mamerto Esquiú'
        ];
        $location[] = [
            'Catamarca',
            'Fiambalá'
        ];
        $location[] = [
            'Catamarca',
            'Hualfín'
        ];
        $location[] = [
            'Catamarca',
            'Huillapima'
        ];
        $location[] = [
            'Catamarca',
            'Icaño'
        ];
        $location[] = [
            'Catamarca',
            'La Puerta'
        ];
        $location[] = [
            'Catamarca',
            'Las Juntas'
        ];
        $location[] = [
            'Catamarca',
            'Londres'
        ];
        $location[] = [
            'Catamarca',
            'Los Altos'
        ];
        $location[] = [
            'Catamarca',
            'Los Varela'
        ];
        $location[] = [
            'Catamarca',
            'Mutquín'
        ];
        $location[] = [
            'Catamarca',
            'Paclín'
        ];
        $location[] = [
            'Catamarca',
            'Poman'
        ];
        $location[] = [
            'Catamarca',
            'Pozo de La Piedra'
        ];
        $location[] = [
            'Catamarca',
            'Puerta de Corral'
        ];
        $location[] = [
            'Catamarca',
            'Puerta San José'
        ];
        $location[] = [
            'Catamarca',
            'Recreo'
        ];
        $location[] = [
            'Catamarca',
            'S.F.V de Catamarca'
        ];
        $location[] = [
            'Catamarca',
            'San Fernando'
        ];
        $location[] = [
            'Catamarca',
            'San Fernando del Valle'
        ];
        $location[] = [
            'Catamarca',
            'San José'
        ];
        $location[] = [
            'Catamarca',
            'Santa María'
        ];
        $location[] = [
            'Catamarca',
            'Santa Rosa'
        ];
        $location[] = [
            'Catamarca',
            'Saujil'
        ];
        $location[] = [
            'Catamarca',
            'Tapso'
        ];
        $location[] = [
            'Catamarca',
            'Tinogasta'
        ];
        $location[] = [
            'Catamarca',
            'Valle Viejo'
        ];
        $location[] = [
            'Catamarca',
            'Villa Vil'
        ];
        $location[] = [
            'Chaco',
            'Aviá Teraí'
        ];
        $location[] = [
            'Chaco',
            'Barranqueras'
        ];
        $location[] = [
            'Chaco',
            'Basail'
        ];
        $location[] = [
            'Chaco',
            'Campo Largo'
        ];
        $location[] = [
            'Chaco',
            'Capital'
        ];
        $location[] = [
            'Chaco',
            'Capitán Solari'
        ];
        $location[] = [
            'Chaco',
            'Charadai'
        ];
        $location[] = [
            'Chaco',
            'Charata'
        ];
        $location[] = [
            'Chaco',
            'Chorotis'
        ];
        $location[] = [
            'Chaco',
            'Ciervo Petiso'
        ];
        $location[] = [
            'Chaco',
            'Cnel. Du Graty'
        ];
        $location[] = [
            'Chaco',
            'Col. Benítez'
        ];
        $location[] = [
            'Chaco',
            'Col. Elisa'
        ];
        $location[] = [
            'Chaco',
            'Col. Popular'
        ];
        $location[] = [
            'Chaco',
            'Colonias Unidas'
        ];
        $location[] = [
            'Chaco',
            'Concepción'
        ];
        $location[] = [
            'Chaco',
            'Corzuela'
        ];
        $location[] = [
            'Chaco',
            'Cote Lai'
        ];
        $location[] = [
            'Chaco',
            'El Sauzalito'
        ];
        $location[] = [
            'Chaco',
            'Enrique Urien'
        ];
        $location[] = [
            'Chaco',
            'Fontana'
        ];
        $location[] = [
            'Chaco',
            'Fte. Esperanza'
        ];
        $location[] = [
            'Chaco',
            'Gancedo'
        ];
        $location[] = [
            'Chaco',
            'Gral. Capdevila'
        ];
        $location[] = [
            'Chaco',
            'Gral. Pinero'
        ];
        $location[] = [
            'Chaco',
            'Gral. San Martín'
        ];
        $location[] = [
            'Chaco',
            'Gral. Vedia'
        ];
        $location[] = [
            'Chaco',
            'Hermoso Campo'
        ];
        $location[] = [
            'Chaco',
            'I. del Cerrito'
        ];
        $location[] = [
            'Chaco',
            'J.J. Castelli'
        ];
        $location[] = [
            'Chaco',
            'La Clotilde'
        ];
        $location[] = [
            'Chaco',
            'La Eduvigis'
        ];
        $location[] = [
            'Chaco',
            'La Escondida'
        ];
        $location[] = [
            'Chaco',
            'La Leonesa'
        ];
        $location[] = [
            'Chaco',
            'La Tigra'
        ];
        $location[] = [
            'Chaco',
            'La Verde'
        ];
        $location[] = [
            'Chaco',
            'Laguna Blanca'
        ];
        $location[] = [
            'Chaco',
            'Laguna Limpia'
        ];
        $location[] = [
            'Chaco',
            'Lapachito'
        ];
        $location[] = [
            'Chaco',
            'Las Breñas'
        ];
        $location[] = [
            'Chaco',
            'Las Garcitas'
        ];
        $location[] = [
            'Chaco',
            'Las Palmas'
        ];
        $location[] = [
            'Chaco',
            'Los Frentones'
        ];
        $location[] = [
            'Chaco',
            'Machagai'
        ];
        $location[] = [
            'Chaco',
            'Makallé'
        ];
        $location[] = [
            'Chaco',
            'Margarita Belén'
        ];
        $location[] = [
            'Chaco',
            'Miraflores'
        ];
        $location[] = [
            'Chaco',
            'Misión N. Pompeya'
        ];
        $location[] = [
            'Chaco',
            'Napenay'
        ];
        $location[] = [
            'Chaco',
            'Pampa Almirón'
        ];
        $location[] = [
            'Chaco',
            'Pampa del Indio'
        ];
        $location[] = [
            'Chaco',
            'Pampa del Infierno'
        ];
        $location[] = [
            'Chaco',
            'Pdcia. de La Plaza'
        ];
        $location[] = [
            'Chaco',
            'Pdcia. Roca'
        ];
        $location[] = [
            'Chaco',
            'Pdcia. Roque Sáenz Peña'
        ];
        $location[] = [
            'Chaco',
            'Pto. Bermejo'
        ];
        $location[] = [
            'Chaco',
            'Pto. Eva Perón'
        ];
        $location[] = [
            'Chaco',
            'Puero Tirol'
        ];
        $location[] = [
            'Chaco',
            'Puerto Vilelas'
        ];
        $location[] = [
            'Chaco',
            'Quitilipi'
        ];
        $location[] = [
            'Chaco',
            'Resistencia'
        ];
        $location[] = [
            'Chaco',
            'Sáenz Peña'
        ];
        $location[] = [
            'Chaco',
            'Samuhú'
        ];
        $location[] = [
            'Chaco',
            'San Bernardo'
        ];
        $location[] = [
            'Chaco',
            'Santa Sylvina'
        ];
        $location[] = [
            'Chaco',
            'Taco Pozo'
        ];
        $location[] = [
            'Chaco',
            'Tres Isletas'
        ];
        $location[] = [
            'Chaco',
            'Villa Ángela'
        ];
        $location[] = [
            'Chaco',
            'Villa Berthet'
        ];
        $location[] = [
            'Chaco',
            'Villa R. Bermejito'
        ];
        $location[] = [
            'Chubut',
            'Aldea Apeleg'
        ];
        $location[] = [
            'Chubut',
            'Aldea Beleiro'
        ];
        $location[] = [
            'Chubut',
            'Aldea Epulef'
        ];
        $location[] = [
            'Chubut',
            'Alto Río Sengerr'
        ];
        $location[] = [
            'Chubut',
            'Buen Pasto'
        ];
        $location[] = [
            'Chubut',
            'Camarones'
        ];
        $location[] = [
            'Chubut',
            'Carrenleufú'
        ];
        $location[] = [
            'Chubut',
            'Cholila'
        ];
        $location[] = [
            'Chubut',
            'Co. Centinela'
        ];
        $location[] = [
            'Chubut',
            'Colan Conhué'
        ];
        $location[] = [
            'Chubut',
            'Comodoro Rivadavia'
        ];
        $location[] = [
            'Chubut',
            'Corcovado'
        ];
        $location[] = [
            'Chubut',
            'Cushamen'
        ];
        $location[] = [
            'Chubut',
            'Dique F. Ameghino'
        ];
        $location[] = [
            'Chubut',
            'Dolavón'
        ];
        $location[] = [
            'Chubut',
            'Dr. R. Rojas'
        ];
        $location[] = [
            'Chubut',
            'El Hoyo'
        ];
        $location[] = [
            'Chubut',
            'El Maitén'
        ];
        $location[] = [
            'Chubut',
            'Epuyén'
        ];
        $location[] = [
            'Chubut',
            'Esquel'
        ];
        $location[] = [
            'Chubut',
            'Facundo'
        ];
        $location[] = [
            'Chubut',
            'Gaimán'
        ];
        $location[] = [
            'Chubut',
            'Gan Gan'
        ];
        $location[] = [
            'Chubut',
            'Gastre'
        ];
        $location[] = [
            'Chubut',
            'Gdor. Costa'
        ];
        $location[] = [
            'Chubut',
            'Gualjaina'
        ];
        $location[] = [
            'Chubut',
            'J. de San Martín'
        ];
        $location[] = [
            'Chubut',
            'Lago Blanco'
        ];
        $location[] = [
            'Chubut',
            'Lago Puelo'
        ];
        $location[] = [
            'Chubut',
            'Lagunita Salada'
        ];
        $location[] = [
            'Chubut',
            'Las Plumas'
        ];
        $location[] = [
            'Chubut',
            'Los Altares'
        ];
        $location[] = [
            'Chubut',
            'Paso de los Indios'
        ];
        $location[] = [
            'Chubut',
            'Paso del Sapo'
        ];
        $location[] = [
            'Chubut',
            'Pto. Madryn'
        ];
        $location[] = [
            'Chubut',
            'Pto. Pirámides'
        ];
        $location[] = [
            'Chubut',
            'Rada Tilly'
        ];
        $location[] = [
            'Chubut',
            'Rawson'
        ];
        $location[] = [
            'Chubut',
            'Río Mayo'
        ];
        $location[] = [
            'Chubut',
            'Río Pico'
        ];
        $location[] = [
            'Chubut',
            'Sarmiento'
        ];
        $location[] = [
            'Chubut',
            'Tecka'
        ];
        $location[] = [
            'Chubut',
            'Telsen'
        ];
        $location[] = [
            'Chubut',
            'Trelew'
        ];
        $location[] = [
            'Chubut',
            'Trevelin'
        ];
        $location[] = [
            'Chubut',
            'Veintiocho de Julio'
        ];
        $location[] = [
            'Córdoba',
            'Achiras'
        ];
        $location[] = [
            'Córdoba',
            'Adelia Maria'
        ];
        $location[] = [
            'Córdoba',
            'Agua de Oro'
        ];
        $location[] = [
            'Córdoba',
            'Alcira Gigena'
        ];
        $location[] = [
            'Córdoba',
            'Aldea Santa Maria'
        ];
        $location[] = [
            'Córdoba',
            'Alejandro Roca'
        ];
        $location[] = [
            'Córdoba',
            'Alejo Ledesma'
        ];
        $location[] = [
            'Córdoba',
            'Alicia'
        ];
        $location[] = [
            'Córdoba',
            'Almafuerte'
        ];
        $location[] = [
            'Córdoba',
            'Alpa Corral'
        ];
        $location[] = [
            'Córdoba',
            'Alta Gracia'
        ];
        $location[] = [
            'Córdoba',
            'Alto Alegre'
        ];
        $location[] = [
            'Córdoba',
            'Alto de Los Quebrachos'
        ];
        $location[] = [
            'Córdoba',
            'Altos de Chipion'
        ];
        $location[] = [
            'Córdoba',
            'Amboy'
        ];
        $location[] = [
            'Córdoba',
            'Ambul'
        ];
        $location[] = [
            'Córdoba',
            'Ana Zumaran'
        ];
        $location[] = [
            'Córdoba',
            'Anisacate'
        ];
        $location[] = [
            'Córdoba',
            'Arguello'
        ];
        $location[] = [
            'Córdoba',
            'Arias'
        ];
        $location[] = [
            'Córdoba',
            'Arroyito'
        ];
        $location[] = [
            'Córdoba',
            'Arroyo Algodon'
        ];
        $location[] = [
            'Córdoba',
            'Arroyo Cabral'
        ];
        $location[] = [
            'Córdoba',
            'Arroyo Los Patos'
        ];
        $location[] = [
            'Córdoba',
            'Assunta'
        ];
        $location[] = [
            'Córdoba',
            'Atahona'
        ];
        $location[] = [
            'Córdoba',
            'Ausonia'
        ];
        $location[] = [
            'Córdoba',
            'Avellaneda'
        ];
        $location[] = [
            'Córdoba',
            'Ballesteros'
        ];
        $location[] = [
            'Córdoba',
            'Ballesteros Sud'
        ];
        $location[] = [
            'Córdoba',
            'Balnearia'
        ];
        $location[] = [
            'Córdoba',
            'Bañado de Soto'
        ];
        $location[] = [
            'Córdoba',
            'Bell Ville'
        ];
        $location[] = [
            'Córdoba',
            'Bengolea'
        ];
        $location[] = [
            'Córdoba',
            'Benjamin Gould'
        ];
        $location[] = [
            'Córdoba',
            'Berrotaran'
        ];
        $location[] = [
            'Córdoba',
            'Bialet Masse'
        ];
        $location[] = [
            'Córdoba',
            'Bouwer'
        ];
        $location[] = [
            'Córdoba',
            'Brinkmann'
        ];
        $location[] = [
            'Córdoba',
            'Buchardo'
        ];
        $location[] = [
            'Córdoba',
            'Bulnes'
        ];
        $location[] = [
            'Córdoba',
            'Cabalango'
        ];
        $location[] = [
            'Córdoba',
            'Calamuchita'
        ];
        $location[] = [
            'Córdoba',
            'Calchin'
        ];
        $location[] = [
            'Córdoba',
            'Calchin Oeste'
        ];
        $location[] = [
            'Córdoba',
            'Calmayo'
        ];
        $location[] = [
            'Córdoba',
            'Camilo Aldao'
        ];
        $location[] = [
            'Córdoba',
            'Caminiaga'
        ];
        $location[] = [
            'Córdoba',
            'Cañada de Luque'
        ];
        $location[] = [
            'Córdoba',
            'Cañada de Machado'
        ];
        $location[] = [
            'Córdoba',
            'Cañada de Rio Pinto'
        ];
        $location[] = [
            'Córdoba',
            'Cañada del Sauce'
        ];
        $location[] = [
            'Córdoba',
            'Canals'
        ];
        $location[] = [
            'Córdoba',
            'Candelaria Sud'
        ];
        $location[] = [
            'Córdoba',
            'Capilla de Remedios'
        ];
        $location[] = [
            'Córdoba',
            'Capilla de Siton'
        ];
        $location[] = [
            'Córdoba',
            'Capilla del Carmen'
        ];
        $location[] = [
            'Córdoba',
            'Capilla del Monte'
        ];
        $location[] = [
            'Córdoba',
            'Capital'
        ];
        $location[] = [
            'Córdoba',
            'Capitan Gral B. O´Higgins'
        ];
        $location[] = [
            'Córdoba',
            'Carnerillo'
        ];
        $location[] = [
            'Córdoba',
            'Carrilobo'
        ];
        $location[] = [
            'Córdoba',
            'Casa Grande'
        ];
        $location[] = [
            'Córdoba',
            'Cavanagh'
        ];
        $location[] = [
            'Córdoba',
            'Cerro Colorado'
        ];
        $location[] = [
            'Córdoba',
            'Chaján'
        ];
        $location[] = [
            'Córdoba',
            'Chalacea'
        ];
        $location[] = [
            'Córdoba',
            'Chañar Viejo'
        ];
        $location[] = [
            'Córdoba',
            'Chancaní'
        ];
        $location[] = [
            'Córdoba',
            'Charbonier'
        ];
        $location[] = [
            'Córdoba',
            'Charras'
        ];
        $location[] = [
            'Córdoba',
            'Chazón'
        ];
        $location[] = [
            'Córdoba',
            'Chilibroste'
        ];
        $location[] = [
            'Córdoba',
            'Chucul'
        ];
        $location[] = [
            'Córdoba',
            'Chuña'
        ];
        $location[] = [
            'Córdoba',
            'Chuña Huasi'
        ];
        $location[] = [
            'Córdoba',
            'Churqui Cañada'
        ];
        $location[] = [
            'Córdoba',
            'Cienaga Del Coro'
        ];
        $location[] = [
            'Córdoba',
            'Cintra'
        ];
        $location[] = [
            'Córdoba',
            'Col. Almada'
        ];
        $location[] = [
            'Córdoba',
            'Col. Anita'
        ];
        $location[] = [
            'Córdoba',
            'Col. Barge'
        ];
        $location[] = [
            'Córdoba',
            'Col. Bismark'
        ];
        $location[] = [
            'Córdoba',
            'Col. Bremen'
        ];
        $location[] = [
            'Córdoba',
            'Col. Caroya'
        ];
        $location[] = [
            'Córdoba',
            'Col. Italiana'
        ];
        $location[] = [
            'Córdoba',
            'Col. Iturraspe'
        ];
        $location[] = [
            'Córdoba',
            'Col. Las Cuatro Esquinas'
        ];
        $location[] = [
            'Córdoba',
            'Col. Las Pichanas'
        ];
        $location[] = [
            'Córdoba',
            'Col. Marina'
        ];
        $location[] = [
            'Córdoba',
            'Col. Prosperidad'
        ];
        $location[] = [
            'Córdoba',
            'Col. San Bartolome'
        ];
        $location[] = [
            'Córdoba',
            'Col. San Pedro'
        ];
        $location[] = [
            'Córdoba',
            'Col. Tirolesa'
        ];
        $location[] = [
            'Córdoba',
            'Col. Vicente Aguero'
        ];
        $location[] = [
            'Córdoba',
            'Col. Videla'
        ];
        $location[] = [
            'Córdoba',
            'Col. Vignaud'
        ];
        $location[] = [
            'Córdoba',
            'Col. Waltelina'
        ];
        $location[] = [
            'Córdoba',
            'Colazo'
        ];
        $location[] = [
            'Córdoba',
            'Comechingones'
        ];
        $location[] = [
            'Córdoba',
            'Conlara'
        ];
        $location[] = [
            'Córdoba',
            'Copacabana'
        ];
        $location[] = [
            'Córdoba',
            'Córdoba'
        ];
        $location[] = [
            'Córdoba',
            'Coronel Baigorria'
        ];
        $location[] = [
            'Córdoba',
            'Coronel Moldes'
        ];
        $location[] = [
            'Córdoba',
            'Corral de Bustos'
        ];
        $location[] = [
            'Córdoba',
            'Corralito'
        ];
        $location[] = [
            'Córdoba',
            'Cosquín'
        ];
        $location[] = [
            'Córdoba',
            'Costa Sacate'
        ];
        $location[] = [
            'Córdoba',
            'Cruz Alta'
        ];
        $location[] = [
            'Córdoba',
            'Cruz de Caña'
        ];
        $location[] = [
            'Córdoba',
            'Cruz del Eje'
        ];
        $location[] = [
            'Córdoba',
            'Cuesta Blanca'
        ];
        $location[] = [
            'Córdoba',
            'Dean Funes'
        ];
        $location[] = [
            'Córdoba',
            'Del Campillo'
        ];
        $location[] = [
            'Córdoba',
            'Despeñaderos'
        ];
        $location[] = [
            'Córdoba',
            'Devoto'
        ];
        $location[] = [
            'Córdoba',
            'Diego de Rojas'
        ];
        $location[] = [
            'Córdoba',
            'Dique Chico'
        ];
        $location[] = [
            'Córdoba',
            'El Arañado'
        ];
        $location[] = [
            'Córdoba',
            'El Brete'
        ];
        $location[] = [
            'Córdoba',
            'El Chacho'
        ];
        $location[] = [
            'Córdoba',
            'El Crispín'
        ];
        $location[] = [
            'Córdoba',
            'El Fortín'
        ];
        $location[] = [
            'Córdoba',
            'El Manzano'
        ];
        $location[] = [
            'Córdoba',
            'El Rastreador'
        ];
        $location[] = [
            'Córdoba',
            'El Rodeo'
        ];
        $location[] = [
            'Córdoba',
            'El Tío'
        ];
        $location[] = [
            'Córdoba',
            'Elena'
        ];
        $location[] = [
            'Córdoba',
            'Embalse'
        ];
        $location[] = [
            'Córdoba',
            'Esquina'
        ];
        $location[] = [
            'Córdoba',
            'Estación Gral. Paz'
        ];
        $location[] = [
            'Córdoba',
            'Estación Juárez Celman'
        ];
        $location[] = [
            'Córdoba',
            'Estancia de Guadalupe'
        ];
        $location[] = [
            'Córdoba',
            'Estancia Vieja'
        ];
        $location[] = [
            'Córdoba',
            'Etruria'
        ];
        $location[] = [
            'Córdoba',
            'Eufrasio Loza'
        ];
        $location[] = [
            'Córdoba',
            'Falda del Carmen'
        ];
        $location[] = [
            'Córdoba',
            'Freyre'
        ];
        $location[] = [
            'Córdoba',
            'Gral. Baldissera'
        ];
        $location[] = [
            'Córdoba',
            'Gral. Cabrera'
        ];
        $location[] = [
            'Córdoba',
            'Gral. Deheza'
        ];
        $location[] = [
            'Córdoba',
            'Gral. Fotheringham'
        ];
        $location[] = [
            'Córdoba',
            'Gral. Levalle'
        ];
        $location[] = [
            'Córdoba',
            'Gral. Roca'
        ];
        $location[] = [
            'Córdoba',
            'Guanaco Muerto'
        ];
        $location[] = [
            'Córdoba',
            'Guasapampa'
        ];
        $location[] = [
            'Córdoba',
            'Guatimozin'
        ];
        $location[] = [
            'Córdoba',
            'Gutenberg'
        ];
        $location[] = [
            'Córdoba',
            'Hernando'
        ];
        $location[] = [
            'Córdoba',
            'Huanchillas'
        ];
        $location[] = [
            'Córdoba',
            'Huerta Grande'
        ];
        $location[] = [
            'Córdoba',
            'Huinca Renanco'
        ];
        $location[] = [
            'Córdoba',
            'Idiazabal'
        ];
        $location[] = [
            'Córdoba',
            'Impira'
        ];
        $location[] = [
            'Córdoba',
            'Inriville'
        ];
        $location[] = [
            'Córdoba',
            'Isla Verde'
        ];
        $location[] = [
            'Córdoba',
            'Italó'
        ];
        $location[] = [
            'Córdoba',
            'James Craik'
        ];
        $location[] = [
            'Córdoba',
            'Jesús María'
        ];
        $location[] = [
            'Córdoba',
            'Jovita'
        ];
        $location[] = [
            'Córdoba',
            'Justiniano Posse'
        ];
        $location[] = [
            'Córdoba',
            'Km 658'
        ];
        $location[] = [
            'Córdoba',
            'L. V. Mansilla'
        ];
        $location[] = [
            'Córdoba',
            'La Batea'
        ];
        $location[] = [
            'Córdoba',
            'La Calera'
        ];
        $location[] = [
            'Córdoba',
            'La Carlota'
        ];
        $location[] = [
            'Córdoba',
            'La Carolina'
        ];
        $location[] = [
            'Córdoba',
            'La Cautiva'
        ];
        $location[] = [
            'Córdoba',
            'La Cesira'
        ];
        $location[] = [
            'Córdoba',
            'La Cruz'
        ];
        $location[] = [
            'Córdoba',
            'La Cumbre'
        ];
        $location[] = [
            'Córdoba',
            'La Cumbrecita'
        ];
        $location[] = [
            'Córdoba',
            'La Falda'
        ];
        $location[] = [
            'Córdoba',
            'La Francia'
        ];
        $location[] = [
            'Córdoba',
            'La Granja'
        ];
        $location[] = [
            'Córdoba',
            'La Higuera'
        ];
        $location[] = [
            'Córdoba',
            'La Laguna'
        ];
        $location[] = [
            'Córdoba',
            'La Paisanita'
        ];
        $location[] = [
            'Córdoba',
            'La Palestina'
        ];
        $location[] = [
            'Córdoba',
            'La Pampa'
        ];
        $location[] = [
            'Córdoba',
            'La Paquita'
        ];
        $location[] = [
            'Córdoba',
            'La Para'
        ];
        $location[] = [
            'Córdoba',
            'La Paz'
        ];
        $location[] = [
            'Córdoba',
            'La Playa'
        ];
        $location[] = [
            'Córdoba',
            'La Playosa'
        ];
        $location[] = [
            'Córdoba',
            'La Población'
        ];
        $location[] = [
            'Córdoba',
            'La Posta'
        ];
        $location[] = [
            'Córdoba',
            'La Puerta'
        ];
        $location[] = [
            'Córdoba',
            'La Quinta'
        ];
        $location[] = [
            'Córdoba',
            'La Rancherita'
        ];
        $location[] = [
            'Córdoba',
            'La Rinconada'
        ];
        $location[] = [
            'Córdoba',
            'La Serranita'
        ];
        $location[] = [
            'Córdoba',
            'La Tordilla'
        ];
        $location[] = [
            'Córdoba',
            'Laborde'
        ];
        $location[] = [
            'Córdoba',
            'Laboulaye'
        ];
        $location[] = [
            'Córdoba',
            'Laguna Larga'
        ];
        $location[] = [
            'Córdoba',
            'Las Acequias'
        ];
        $location[] = [
            'Córdoba',
            'Las Albahacas'
        ];
        $location[] = [
            'Córdoba',
            'Las Arrias'
        ];
        $location[] = [
            'Córdoba',
            'Las Bajadas'
        ];
        $location[] = [
            'Córdoba',
            'Las Caleras'
        ];
        $location[] = [
            'Córdoba',
            'Las Calles'
        ];
        $location[] = [
            'Córdoba',
            'Las Cañadas'
        ];
        $location[] = [
            'Córdoba',
            'Las Gramillas'
        ];
        $location[] = [
            'Córdoba',
            'Las Higueras'
        ];
        $location[] = [
            'Córdoba',
            'Las Isletillas'
        ];
        $location[] = [
            'Córdoba',
            'Las Junturas'
        ];
        $location[] = [
            'Córdoba',
            'Las Palmas'
        ];
        $location[] = [
            'Córdoba',
            'Las Peñas'
        ];
        $location[] = [
            'Córdoba',
            'Las Peñas Sud'
        ];
        $location[] = [
            'Córdoba',
            'Las Perdices'
        ];
        $location[] = [
            'Córdoba',
            'Las Playas'
        ];
        $location[] = [
            'Córdoba',
            'Las Rabonas'
        ];
        $location[] = [
            'Córdoba',
            'Las Saladas'
        ];
        $location[] = [
            'Córdoba',
            'Las Tapias'
        ];
        $location[] = [
            'Córdoba',
            'Las Varas'
        ];
        $location[] = [
            'Córdoba',
            'Las Varillas'
        ];
        $location[] = [
            'Córdoba',
            'Las Vertientes'
        ];
        $location[] = [
            'Córdoba',
            'Leguizamón'
        ];
        $location[] = [
            'Córdoba',
            'Leones'
        ];
        $location[] = [
            'Córdoba',
            'Los Cedros'
        ];
        $location[] = [
            'Córdoba',
            'Los Cerrillos'
        ];
        $location[] = [
            'Córdoba',
            'Los Chañaritos (C.E)'
        ];
        $location[] = [
            'Córdoba',
            'Los Chanaritos (R.S)'
        ];
        $location[] = [
            'Córdoba',
            'Los Cisnes'
        ];
        $location[] = [
            'Córdoba',
            'Los Cocos'
        ];
        $location[] = [
            'Córdoba',
            'Los Cóndores'
        ];
        $location[] = [
            'Córdoba',
            'Los Hornillos'
        ];
        $location[] = [
            'Córdoba',
            'Los Hoyos'
        ];
        $location[] = [
            'Córdoba',
            'Los Mistoles'
        ];
        $location[] = [
            'Córdoba',
            'Los Molinos'
        ];
        $location[] = [
            'Córdoba',
            'Los Pozos'
        ];
        $location[] = [
            'Córdoba',
            'Los Reartes'
        ];
        $location[] = [
            'Córdoba',
            'Los Surgentes'
        ];
        $location[] = [
            'Córdoba',
            'Los Talares'
        ];
        $location[] = [
            'Córdoba',
            'Los Zorros'
        ];
        $location[] = [
            'Córdoba',
            'Lozada'
        ];
        $location[] = [
            'Córdoba',
            'Luca'
        ];
        $location[] = [
            'Córdoba',
            'Luque'
        ];
        $location[] = [
            'Córdoba',
            'Luyaba'
        ];
        $location[] = [
            'Córdoba',
            'Malagueño'
        ];
        $location[] = [
            'Córdoba',
            'Malena'
        ];
        $location[] = [
            'Córdoba',
            'Malvinas Argentinas'
        ];
        $location[] = [
            'Córdoba',
            'Manfredi'
        ];
        $location[] = [
            'Córdoba',
            'Maquinista Gallini'
        ];
        $location[] = [
            'Córdoba',
            'Marcos Juárez'
        ];
        $location[] = [
            'Córdoba',
            'Marull'
        ];
        $location[] = [
            'Córdoba',
            'Matorrales'
        ];
        $location[] = [
            'Córdoba',
            'Mattaldi'
        ];
        $location[] = [
            'Córdoba',
            'Mayu Sumaj'
        ];
        $location[] = [
            'Córdoba',
            'Media Naranja'
        ];
        $location[] = [
            'Córdoba',
            'Melo'
        ];
        $location[] = [
            'Córdoba',
            'Mendiolaza'
        ];
        $location[] = [
            'Córdoba',
            'Mi Granja'
        ];
        $location[] = [
            'Córdoba',
            'Mina Clavero'
        ];
        $location[] = [
            'Córdoba',
            'Miramar'
        ];
        $location[] = [
            'Córdoba',
            'Morrison'
        ];
        $location[] = [
            'Córdoba',
            'Morteros'
        ];
        $location[] = [
            'Córdoba',
            'Mte. Buey'
        ];
        $location[] = [
            'Córdoba',
            'Mte. Cristo'
        ];
        $location[] = [
            'Córdoba',
            'Mte. De Los Gauchos'
        ];
        $location[] = [
            'Córdoba',
            'Mte. Leña'
        ];
        $location[] = [
            'Córdoba',
            'Mte. Maíz'
        ];
        $location[] = [
            'Córdoba',
            'Mte. Ralo'
        ];
        $location[] = [
            'Córdoba',
            'Nicolás Bruzone'
        ];
        $location[] = [
            'Córdoba',
            'Noetinger'
        ];
        $location[] = [
            'Córdoba',
            'Nono'
        ];
        $location[] = [
            'Córdoba',
            'Nueva Córdoba'
        ];
        $location[] = [
            'Córdoba',
            'Obispo Trejo'
        ];
        $location[] = [
            'Córdoba',
            'Olaeta'
        ];
        $location[] = [
            'Córdoba',
            'Oliva'
        ];
        $location[] = [
            'Córdoba',
            'Olivares San Nicolás'
        ];
        $location[] = [
            'Córdoba',
            'Onagolty'
        ];
        $location[] = [
            'Córdoba',
            'Oncativo'
        ];
        $location[] = [
            'Córdoba',
            'Ordoñez'
        ];
        $location[] = [
            'Córdoba',
            'Pacheco De Melo'
        ];
        $location[] = [
            'Córdoba',
            'Pampayasta N.'
        ];
        $location[] = [
            'Córdoba',
            'Pampayasta S.'
        ];
        $location[] = [
            'Córdoba',
            'Panaholma'
        ];
        $location[] = [
            'Córdoba',
            'Pascanas'
        ];
        $location[] = [
            'Córdoba',
            'Pasco'
        ];
        $location[] = [
            'Córdoba',
            'Paso del Durazno'
        ];
        $location[] = [
            'Córdoba',
            'Paso Viejo'
        ];
        $location[] = [
            'Córdoba',
            'Pilar'
        ];
        $location[] = [
            'Córdoba',
            'Pincén'
        ];
        $location[] = [
            'Córdoba',
            'Piquillín'
        ];
        $location[] = [
            'Córdoba',
            'Plaza de Mercedes'
        ];
        $location[] = [
            'Córdoba',
            'Plaza Luxardo'
        ];
        $location[] = [
            'Córdoba',
            'Porteña'
        ];
        $location[] = [
            'Córdoba',
            'Potrero de Garay'
        ];
        $location[] = [
            'Córdoba',
            'Pozo del Molle'
        ];
        $location[] = [
            'Córdoba',
            'Pozo Nuevo'
        ];
        $location[] = [
            'Córdoba',
            'Pueblo Italiano'
        ];
        $location[] = [
            'Córdoba',
            'Puesto de Castro'
        ];
        $location[] = [
            'Córdoba',
            'Punta del Agua'
        ];
        $location[] = [
            'Córdoba',
            'Quebracho Herrado'
        ];
        $location[] = [
            'Córdoba',
            'Quilino'
        ];
        $location[] = [
            'Córdoba',
            'Rafael García'
        ];
        $location[] = [
            'Córdoba',
            'Ranqueles'
        ];
        $location[] = [
            'Córdoba',
            'Rayo Cortado'
        ];
        $location[] = [
            'Córdoba',
            'Reducción'
        ];
        $location[] = [
            'Córdoba',
            'Rincón'
        ];
        $location[] = [
            'Córdoba',
            'Río Bamba'
        ];
        $location[] = [
            'Córdoba',
            'Río Ceballos'
        ];
        $location[] = [
            'Córdoba',
            'Río Cuarto'
        ];
        $location[] = [
            'Córdoba',
            'Río de Los Sauces'
        ];
        $location[] = [
            'Córdoba',
            'Río Primero'
        ];
        $location[] = [
            'Córdoba',
            'Río Segundo'
        ];
        $location[] = [
            'Córdoba',
            'Río Tercero'
        ];
        $location[] = [
            'Córdoba',
            'Rosales'
        ];
        $location[] = [
            'Córdoba',
            'Rosario del Saladillo'
        ];
        $location[] = [
            'Córdoba',
            'Sacanta'
        ];
        $location[] = [
            'Córdoba',
            'Sagrada Familia'
        ];
        $location[] = [
            'Córdoba',
            'Saira'
        ];
        $location[] = [
            'Córdoba',
            'Saladillo'
        ];
        $location[] = [
            'Córdoba',
            'Saldán'
        ];
        $location[] = [
            'Córdoba',
            'Salsacate'
        ];
        $location[] = [
            'Córdoba',
            'Salsipuedes'
        ];
        $location[] = [
            'Córdoba',
            'Sampacho'
        ];
        $location[] = [
            'Córdoba',
            'San Agustín'
        ];
        $location[] = [
            'Córdoba',
            'San Antonio de Arredondo'
        ];
        $location[] = [
            'Córdoba',
            'San Antonio de Litín'
        ];
        $location[] = [
            'Córdoba',
            'San Basilio'
        ];
        $location[] = [
            'Córdoba',
            'San Carlos Minas'
        ];
        $location[] = [
            'Córdoba',
            'San Clemente'
        ];
        $location[] = [
            'Córdoba',
            'San Esteban'
        ];
        $location[] = [
            'Córdoba',
            'San Francisco'
        ];
        $location[] = [
            'Córdoba',
            'San Ignacio'
        ];
        $location[] = [
            'Córdoba',
            'San Javier'
        ];
        $location[] = [
            'Córdoba',
            'San Jerónimo'
        ];
        $location[] = [
            'Córdoba',
            'San Joaquín'
        ];
        $location[] = [
            'Córdoba',
            'San José de La Dormida'
        ];
        $location[] = [
            'Córdoba',
            'San José de Las Salinas'
        ];
        $location[] = [
            'Córdoba',
            'San Lorenzo'
        ];
        $location[] = [
            'Córdoba',
            'San Marcos Sierras'
        ];
        $location[] = [
            'Córdoba',
            'San Marcos Sud'
        ];
        $location[] = [
            'Córdoba',
            'San Pedro'
        ];
        $location[] = [
            'Córdoba',
            'San Pedro N.'
        ];
        $location[] = [
            'Córdoba',
            'San Roque'
        ];
        $location[] = [
            'Córdoba',
            'San Vicente'
        ];
        $location[] = [
            'Córdoba',
            'Santa Catalina'
        ];
        $location[] = [
            'Córdoba',
            'Santa Elena'
        ];
        $location[] = [
            'Córdoba',
            'Santa Eufemia'
        ];
        $location[] = [
            'Córdoba',
            'Santa Maria'
        ];
        $location[] = [
            'Córdoba',
            'Sarmiento'
        ];
        $location[] = [
            'Córdoba',
            'Saturnino M.Laspiur'
        ];
        $location[] = [
            'Córdoba',
            'Sauce Arriba'
        ];
        $location[] = [
            'Córdoba',
            'Sebastián Elcano'
        ];
        $location[] = [
            'Córdoba',
            'Seeber'
        ];
        $location[] = [
            'Córdoba',
            'Segunda Usina'
        ];
        $location[] = [
            'Córdoba',
            'Serrano'
        ];
        $location[] = [
            'Córdoba',
            'Serrezuela'
        ];
        $location[] = [
            'Córdoba',
            'Sgo. Temple'
        ];
        $location[] = [
            'Córdoba',
            'Silvio Pellico'
        ];
        $location[] = [
            'Córdoba',
            'Simbolar'
        ];
        $location[] = [
            'Córdoba',
            'Sinsacate'
        ];
        $location[] = [
            'Córdoba',
            'Sta. Rosa de Calamuchita'
        ];
        $location[] = [
            'Córdoba',
            'Sta. Rosa de Río Primero'
        ];
        $location[] = [
            'Córdoba',
            'Suco'
        ];
        $location[] = [
            'Córdoba',
            'Tala Cañada'
        ];
        $location[] = [
            'Córdoba',
            'Tala Huasi'
        ];
        $location[] = [
            'Córdoba',
            'Talaini'
        ];
        $location[] = [
            'Córdoba',
            'Tancacha'
        ];
        $location[] = [
            'Córdoba',
            'Tanti'
        ];
        $location[] = [
            'Córdoba',
            'Ticino'
        ];
        $location[] = [
            'Córdoba',
            'Tinoco'
        ];
        $location[] = [
            'Córdoba',
            'Tío Pujio'
        ];
        $location[] = [
            'Córdoba',
            'Toledo'
        ];
        $location[] = [
            'Córdoba',
            'Toro Pujio'
        ];
        $location[] = [
            'Córdoba',
            'Tosno'
        ];
        $location[] = [
            'Córdoba',
            'Tosquita'
        ];
        $location[] = [
            'Córdoba',
            'Tránsito'
        ];
        $location[] = [
            'Córdoba',
            'Tuclame'
        ];
        $location[] = [
            'Córdoba',
            'Tutti'
        ];
        $location[] = [
            'Córdoba',
            'Ucacha'
        ];
        $location[] = [
            'Córdoba',
            'Unquillo'
        ];
        $location[] = [
            'Córdoba',
            'Valle de Anisacate'
        ];
        $location[] = [
            'Córdoba',
            'Valle Hermoso'
        ];
        $location[] = [
            'Córdoba',
            'Vélez Sarfield'
        ];
        $location[] = [
            'Córdoba',
            'Viamonte'
        ];
        $location[] = [
            'Córdoba',
            'Vicuña Mackenna'
        ];
        $location[] = [
            'Córdoba',
            'Villa Allende'
        ];
        $location[] = [
            'Córdoba',
            'Villa Amancay'
        ];
        $location[] = [
            'Córdoba',
            'Villa Ascasubi'
        ];
        $location[] = [
            'Córdoba',
            'Villa Candelaria N.'
        ];
        $location[] = [
            'Córdoba',
            'Villa Carlos Paz'
        ];
        $location[] = [
            'Córdoba',
            'Villa Cerro Azul'
        ];
        $location[] = [
            'Córdoba',
            'Villa Ciudad de América'
        ];
        $location[] = [
            'Córdoba',
            'Villa Ciudad Pque Los Reartes'
        ];
        $location[] = [
            'Córdoba',
            'Villa Concepción del Tío'
        ];
        $location[] = [
            'Córdoba',
            'Villa Cura Brochero'
        ];
        $location[] = [
            'Córdoba',
            'Villa de Las Rosas'
        ];
        $location[] = [
            'Córdoba',
            'Villa de María'
        ];
        $location[] = [
            'Córdoba',
            'Villa de Pocho'
        ];
        $location[] = [
            'Córdoba',
            'Villa de Soto'
        ];
        $location[] = [
            'Córdoba',
            'Villa del Dique'
        ];
        $location[] = [
            'Córdoba',
            'Villa del Prado'
        ];
        $location[] = [
            'Córdoba',
            'Villa del Rosario'
        ];
        $location[] = [
            'Córdoba',
            'Villa del Totoral'
        ];
        $location[] = [
            'Córdoba',
            'Villa Dolores'
        ];
        $location[] = [
            'Córdoba',
            'Villa El Chancay'
        ];
        $location[] = [
            'Córdoba',
            'Villa Elisa'
        ];
        $location[] = [
            'Córdoba',
            'Villa Flor Serrana'
        ];
        $location[] = [
            'Córdoba',
            'Villa Fontana'
        ];
        $location[] = [
            'Córdoba',
            'Villa Giardino'
        ];
        $location[] = [
            'Córdoba',
            'Villa Gral. Belgrano'
        ];
        $location[] = [
            'Córdoba',
            'Villa Gutierrez'
        ];
        $location[] = [
            'Córdoba',
            'Villa Huidobro'
        ];
        $location[] = [
            'Córdoba',
            'Villa La Bolsa'
        ];
        $location[] = [
            'Córdoba',
            'Villa Los Aromos'
        ];
        $location[] = [
            'Córdoba',
            'Villa Los Patos'
        ];
        $location[] = [
            'Córdoba',
            'Villa María'
        ];
        $location[] = [
            'Córdoba',
            'Villa Nueva'
        ];
        $location[] = [
            'Córdoba',
            'Villa Pque. Santa Ana'
        ];
        $location[] = [
            'Córdoba',
            'Villa Pque. Siquiman'
        ];
        $location[] = [
            'Córdoba',
            'Villa Quillinzo'
        ];
        $location[] = [
            'Córdoba',
            'Villa Rossi'
        ];
        $location[] = [
            'Córdoba',
            'Villa Rumipal'
        ];
        $location[] = [
            'Córdoba',
            'Villa San Esteban'
        ];
        $location[] = [
            'Córdoba',
            'Villa San Isidro'
        ];
        $location[] = [
            'Córdoba',
            'Villa Santa Cruz'
        ];
        $location[] = [
            'Córdoba',
            'Villa Sarmiento (G.R)'
        ];
        $location[] = [
            'Córdoba',
            'Villa Sarmiento (S.A)'
        ];
        $location[] = [
            'Córdoba',
            'Villa Tulumba'
        ];
        $location[] = [
            'Córdoba',
            'Villa Valeria'
        ];
        $location[] = [
            'Córdoba',
            'Villa Yacanto'
        ];
        $location[] = [
            'Córdoba',
            'Washington'
        ];
        $location[] = [
            'Córdoba',
            'Wenceslao Escalante'
        ];
        $location[] = [
            'Córdoba',
            'Ycho Cruz Sierras'
        ];
        $location[] = [
            'Corrientes',
            'Alvear'
        ];
        $location[] = [
            'Corrientes',
            'Bella Vista'
        ];
        $location[] = [
            'Corrientes',
            'Berón de Astrada'
        ];
        $location[] = [
            'Corrientes',
            'Bonpland'
        ];
        $location[] = [
            'Corrientes',
            'Caá Cati'
        ];
        $location[] = [
            'Corrientes',
            'Capital'
        ];
        $location[] = [
            'Corrientes',
            'Chavarría'
        ];
        $location[] = [
            'Corrientes',
            'Col. C. Pellegrini'
        ];
        $location[] = [
            'Corrientes',
            'Col. Libertad'
        ];
        $location[] = [
            'Corrientes',
            'Col. Liebig'
        ];
        $location[] = [
            'Corrientes',
            'Col. Sta Rosa'
        ];
        $location[] = [
            'Corrientes',
            'Concepción'
        ];
        $location[] = [
            'Corrientes',
            'Cruz de Los Milagros'
        ];
        $location[] = [
            'Corrientes',
            'Curuzú-Cuatiá'
        ];
        $location[] = [
            'Corrientes',
            'Empedrado'
        ];
        $location[] = [
            'Corrientes',
            'Esquina'
        ];
        $location[] = [
            'Corrientes',
            'Estación Torrent'
        ];
        $location[] = [
            'Corrientes',
            'Felipe Yofré'
        ];
        $location[] = [
            'Corrientes',
            'Garruchos'
        ];
        $location[] = [
            'Corrientes',
            'Gdor. Agrónomo'
        ];
        $location[] = [
            'Corrientes',
            'Gdor. Martínez'
        ];
        $location[] = [
            'Corrientes',
            'Goya'
        ];
        $location[] = [
            'Corrientes',
            'Guaviravi'
        ];
        $location[] = [
            'Corrientes',
            'Herlitzka'
        ];
        $location[] = [
            'Corrientes',
            'Ita-Ibate'
        ];
        $location[] = [
            'Corrientes',
            'Itatí'
        ];
        $location[] = [
            'Corrientes',
            'Ituzaingó'
        ];
        $location[] = [
            'Corrientes',
            'José Rafael Gómez'
        ];
        $location[] = [
            'Corrientes',
            'Juan Pujol'
        ];
        $location[] = [
            'Corrientes',
            'La Cruz'
        ];
        $location[] = [
            'Corrientes',
            'Lavalle'
        ];
        $location[] = [
            'Corrientes',
            'Lomas de Vallejos'
        ];
        $location[] = [
            'Corrientes',
            'Loreto'
        ];
        $location[] = [
            'Corrientes',
            'Mariano I. Loza'
        ];
        $location[] = [
            'Corrientes',
            'Mburucuyá'
        ];
        $location[] = [
            'Corrientes',
            'Mercedes'
        ];
        $location[] = [
            'Corrientes',
            'Mocoretá'
        ];
        $location[] = [
            'Corrientes',
            'Mte. Caseros'
        ];
        $location[] = [
            'Corrientes',
            'Nueve de Julio'
        ];
        $location[] = [
            'Corrientes',
            'Palmar Grande'
        ];
        $location[] = [
            'Corrientes',
            'Parada Pucheta'
        ];
        $location[] = [
            'Corrientes',
            'Paso de La Patria'
        ];
        $location[] = [
            'Corrientes',
            'Paso de Los Libres'
        ];
        $location[] = [
            'Corrientes',
            'Pedro R. Fernandez'
        ];
        $location[] = [
            'Corrientes',
            'Perugorría'
        ];
        $location[] = [
            'Corrientes',
            'Pueblo Libertador'
        ];
        $location[] = [
            'Corrientes',
            'Ramada Paso'
        ];
        $location[] = [
            'Corrientes',
            'Riachuelo'
        ];
        $location[] = [
            'Corrientes',
            'Saladas'
        ];
        $location[] = [
            'Corrientes',
            'San Antonio'
        ];
        $location[] = [
            'Corrientes',
            'San Carlos'
        ];
        $location[] = [
            'Corrientes',
            'San Cosme'
        ];
        $location[] = [
            'Corrientes',
            'San Lorenzo'
        ];
        $location[] = [
            'Corrientes',
            'San Luis del Palmar'
        ];
        $location[] = [
            'Corrientes',
            'San Miguel'
        ];
        $location[] = [
            'Corrientes',
            'San Roque'
        ];
        $location[] = [
            'Corrientes',
            'Santa Ana'
        ];
        $location[] = [
            'Corrientes',
            'Santa Lucía'
        ];
        $location[] = [
            'Corrientes',
            'Santo Tomé'
        ];
        $location[] = [
            'Corrientes',
            'Sauce'
        ];
        $location[] = [
            'Corrientes',
            'Tabay'
        ];
        $location[] = [
            'Corrientes',
            'Tapebicuá'
        ];
        $location[] = [
            'Corrientes',
            'Tatacua'
        ];
        $location[] = [
            'Corrientes',
            'Virasoro'
        ];
        $location[] = [
            'Corrientes',
            'Yapeyú'
        ];
        $location[] = [
            'Corrientes',
            'Yataití Calle'
        ];
        $location[] = [
            'Entre Ríos',
            'Alarcón'
        ];
        $location[] = [
            'Entre Ríos',
            'Alcaraz'
        ];
        $location[] = [
            'Entre Ríos',
            'Alcaraz N.'
        ];
        $location[] = [
            'Entre Ríos',
            'Alcaraz S.'
        ];
        $location[] = [
            'Entre Ríos',
            'Aldea Asunción'
        ];
        $location[] = [
            'Entre Ríos',
            'Aldea Brasilera'
        ];
        $location[] = [
            'Entre Ríos',
            'Aldea Elgenfeld'
        ];
        $location[] = [
            'Entre Ríos',
            'Aldea Grapschental'
        ];
        $location[] = [
            'Entre Ríos',
            'Aldea Ma. Luisa'
        ];
        $location[] = [
            'Entre Ríos',
            'Aldea Protestante'
        ];
        $location[] = [
            'Entre Ríos',
            'Aldea Salto'
        ];
        $location[] = [
            'Entre Ríos',
            'Aldea San Antonio (G)'
        ];
        $location[] = [
            'Entre Ríos',
            'Aldea San Antonio (P)'
        ];
        $location[] = [
            'Entre Ríos',
            'Aldea San Juan'
        ];
        $location[] = [
            'Entre Ríos',
            'Aldea San Miguel'
        ];
        $location[] = [
            'Entre Ríos',
            'Aldea San Rafael'
        ];
        $location[] = [
            'Entre Ríos',
            'Aldea Spatzenkutter'
        ];
        $location[] = [
            'Entre Ríos',
            'Aldea Sta. María'
        ];
        $location[] = [
            'Entre Ríos',
            'Aldea Sta. Rosa'
        ];
        $location[] = [
            'Entre Ríos',
            'Aldea Valle María'
        ];
        $location[] = [
            'Entre Ríos',
            'Altamirano Sur'
        ];
        $location[] = [
            'Entre Ríos',
            'Antelo'
        ];
        $location[] = [
            'Entre Ríos',
            'Antonio Tomás'
        ];
        $location[] = [
            'Entre Ríos',
            'Aranguren'
        ];
        $location[] = [
            'Entre Ríos',
            'Arroyo Barú'
        ];
        $location[] = [
            'Entre Ríos',
            'Arroyo Burgos'
        ];
        $location[] = [
            'Entre Ríos',
            'Arroyo Clé'
        ];
        $location[] = [
            'Entre Ríos',
            'Arroyo Corralito'
        ];
        $location[] = [
            'Entre Ríos',
            'Arroyo del Medio'
        ];
        $location[] = [
            'Entre Ríos',
            'Arroyo Maturrango'
        ];
        $location[] = [
            'Entre Ríos',
            'Arroyo Palo Seco'
        ];
        $location[] = [
            'Entre Ríos',
            'Banderas'
        ];
        $location[] = [
            'Entre Ríos',
            'Basavilbaso'
        ];
        $location[] = [
            'Entre Ríos',
            'Betbeder'
        ];
        $location[] = [
            'Entre Ríos',
            'Bovril'
        ];
        $location[] = [
            'Entre Ríos',
            'Caseros'
        ];
        $location[] = [
            'Entre Ríos',
            'Ceibas'
        ];
        $location[] = [
            'Entre Ríos',
            'Cerrito'
        ];
        $location[] = [
            'Entre Ríos',
            'Chajarí'
        ];
        $location[] = [
            'Entre Ríos',
            'Chilcas'
        ];
        $location[] = [
            'Entre Ríos',
            'Clodomiro Ledesma'
        ];
        $location[] = [
            'Entre Ríos',
            'Col. Alemana'
        ];
        $location[] = [
            'Entre Ríos',
            'Col. Avellaneda'
        ];
        $location[] = [
            'Entre Ríos',
            'Col. Avigdor'
        ];
        $location[] = [
            'Entre Ríos',
            'Col. Ayuí'
        ];
        $location[] = [
            'Entre Ríos',
            'Col. Baylina'
        ];
        $location[] = [
            'Entre Ríos',
            'Col. Carrasco'
        ];
        $location[] = [
            'Entre Ríos',
            'Col. Celina'
        ];
        $location[] = [
            'Entre Ríos',
            'Col. Cerrito'
        ];
        $location[] = [
            'Entre Ríos',
            'Col. Crespo'
        ];
        $location[] = [
            'Entre Ríos',
            'Col. Elia'
        ];
        $location[] = [
            'Entre Ríos',
            'Col. Ensayo'
        ];
        $location[] = [
            'Entre Ríos',
            'Col. Gral. Roca'
        ];
        $location[] = [
            'Entre Ríos',
            'Col. La Argentina'
        ];
        $location[] = [
            'Entre Ríos',
            'Col. Merou'
        ];
        $location[] = [
            'Entre Ríos',
            'Col. Oficial Nª3'
        ];
        $location[] = [
            'Entre Ríos',
            'Col. Oficial Nº13'
        ];
        $location[] = [
            'Entre Ríos',
            'Col. Oficial Nº14'
        ];
        $location[] = [
            'Entre Ríos',
            'Col. Oficial Nº5'
        ];
        $location[] = [
            'Entre Ríos',
            'Col. Reffino'
        ];
        $location[] = [
            'Entre Ríos',
            'Col. Tunas'
        ];
        $location[] = [
            'Entre Ríos',
            'Col. Viraró'
        ];
        $location[] = [
            'Entre Ríos',
            'Colón'
        ];
        $location[] = [
            'Entre Ríos',
            'Concepción del Uruguay'
        ];
        $location[] = [
            'Entre Ríos',
            'Concordia'
        ];
        $location[] = [
            'Entre Ríos',
            'Conscripto Bernardi'
        ];
        $location[] = [
            'Entre Ríos',
            'Costa Grande'
        ];
        $location[] = [
            'Entre Ríos',
            'Costa San Antonio'
        ];
        $location[] = [
            'Entre Ríos',
            'Costa Uruguay N.'
        ];
        $location[] = [
            'Entre Ríos',
            'Costa Uruguay S.'
        ];
        $location[] = [
            'Entre Ríos',
            'Crespo'
        ];
        $location[] = [
            'Entre Ríos',
            'Crucecitas 3ª'
        ];
        $location[] = [
            'Entre Ríos',
            'Crucecitas 7ª'
        ];
        $location[] = [
            'Entre Ríos',
            'Crucecitas 8ª'
        ];
        $location[] = [
            'Entre Ríos',
            'Cuchilla Redonda'
        ];
        $location[] = [
            'Entre Ríos',
            'Curtiembre'
        ];
        $location[] = [
            'Entre Ríos',
            'Diamante'
        ];
        $location[] = [
            'Entre Ríos',
            'Distrito 6º'
        ];
        $location[] = [
            'Entre Ríos',
            'Distrito Chañar'
        ];
        $location[] = [
            'Entre Ríos',
            'Distrito Chiqueros'
        ];
        $location[] = [
            'Entre Ríos',
            'Distrito Cuarto'
        ];
        $location[] = [
            'Entre Ríos',
            'Distrito Diego López'
        ];
        $location[] = [
            'Entre Ríos',
            'Distrito Pajonal'
        ];
        $location[] = [
            'Entre Ríos',
            'Distrito Sauce'
        ];
        $location[] = [
            'Entre Ríos',
            'Distrito Tala'
        ];
        $location[] = [
            'Entre Ríos',
            'Distrito Talitas'
        ];
        $location[] = [
            'Entre Ríos',
            'Don Cristóbal 1ª Sección'
        ];
        $location[] = [
            'Entre Ríos',
            'Don Cristóbal 2ª Sección'
        ];
        $location[] = [
            'Entre Ríos',
            'Durazno'
        ];
        $location[] = [
            'Entre Ríos',
            'El Cimarrón'
        ];
        $location[] = [
            'Entre Ríos',
            'El Gramillal'
        ];
        $location[] = [
            'Entre Ríos',
            'El Palenque'
        ];
        $location[] = [
            'Entre Ríos',
            'El Pingo'
        ];
        $location[] = [
            'Entre Ríos',
            'El Quebracho'
        ];
        $location[] = [
            'Entre Ríos',
            'El Redomón'
        ];
        $location[] = [
            'Entre Ríos',
            'El Solar'
        ];
        $location[] = [
            'Entre Ríos',
            'Enrique Carbo'
        ];
        $location[] = [
            'Entre Ríos',
            'Entre Ríos'
        ];
        $location[] = [
            'Entre Ríos',
            'Espinillo N.'
        ];
        $location[] = [
            'Entre Ríos',
            'Estación Campos'
        ];
        $location[] = [
            'Entre Ríos',
            'Estación Escriña'
        ];
        $location[] = [
            'Entre Ríos',
            'Estación Lazo'
        ];
        $location[] = [
            'Entre Ríos',
            'Estación Raíces'
        ];
        $location[] = [
            'Entre Ríos',
            'Estación Yerúa'
        ];
        $location[] = [
            'Entre Ríos',
            'Estancia Grande'
        ];
        $location[] = [
            'Entre Ríos',
            'Estancia Líbaros'
        ];
        $location[] = [
            'Entre Ríos',
            'Estancia Racedo'
        ];
        $location[] = [
            'Entre Ríos',
            'Estancia Solá'
        ];
        $location[] = [
            'Entre Ríos',
            'Estancia Yuquerí'
        ];
        $location[] = [
            'Entre Ríos',
            'Estaquitas'
        ];
        $location[] = [
            'Entre Ríos',
            'Faustino M. Parera'
        ];
        $location[] = [
            'Entre Ríos',
            'Febre'
        ];
        $location[] = [
            'Entre Ríos',
            'Federación'
        ];
        $location[] = [
            'Entre Ríos',
            'Federal'
        ];
        $location[] = [
            'Entre Ríos',
            'Gdor. Echagüe'
        ];
        $location[] = [
            'Entre Ríos',
            'Gdor. Mansilla'
        ];
        $location[] = [
            'Entre Ríos',
            'Gilbert'
        ];
        $location[] = [
            'Entre Ríos',
            'González Calderón'
        ];
        $location[] = [
            'Entre Ríos',
            'Gral. Almada'
        ];
        $location[] = [
            'Entre Ríos',
            'Gral. Alvear'
        ];
        $location[] = [
            'Entre Ríos',
            'Gral. Campos'
        ];
        $location[] = [
            'Entre Ríos',
            'Gral. Galarza'
        ];
        $location[] = [
            'Entre Ríos',
            'Gral. Ramírez'
        ];
        $location[] = [
            'Entre Ríos',
            'Gualeguay'
        ];
        $location[] = [
            'Entre Ríos',
            'Gualeguaychú'
        ];
        $location[] = [
            'Entre Ríos',
            'Gualeguaycito'
        ];
        $location[] = [
            'Entre Ríos',
            'Guardamonte'
        ];
        $location[] = [
            'Entre Ríos',
            'Hambis'
        ];
        $location[] = [
            'Entre Ríos',
            'Hasenkamp'
        ];
        $location[] = [
            'Entre Ríos',
            'Hernandarias'
        ];
        $location[] = [
            'Entre Ríos',
            'Hernández'
        ];
        $location[] = [
            'Entre Ríos',
            'Herrera'
        ];
        $location[] = [
            'Entre Ríos',
            'Hinojal'
        ];
        $location[] = [
            'Entre Ríos',
            'Hocker'
        ];
        $location[] = [
            'Entre Ríos',
            'Ing. Sajaroff'
        ];
        $location[] = [
            'Entre Ríos',
            'Irazusta'
        ];
        $location[] = [
            'Entre Ríos',
            'Isletas'
        ];
        $location[] = [
            'Entre Ríos',
            'J.J De Urquiza'
        ];
        $location[] = [
            'Entre Ríos',
            'Jubileo'
        ];
        $location[] = [
            'Entre Ríos',
            'La Clarita'
        ];
        $location[] = [
            'Entre Ríos',
            'La Criolla'
        ];
        $location[] = [
            'Entre Ríos',
            'La Esmeralda'
        ];
        $location[] = [
            'Entre Ríos',
            'La Florida'
        ];
        $location[] = [
            'Entre Ríos',
            'La Fraternidad'
        ];
        $location[] = [
            'Entre Ríos',
            'La Hierra'
        ];
        $location[] = [
            'Entre Ríos',
            'La Ollita'
        ];
        $location[] = [
            'Entre Ríos',
            'La Paz'
        ];
        $location[] = [
            'Entre Ríos',
            'La Picada'
        ];
        $location[] = [
            'Entre Ríos',
            'La Providencia'
        ];
        $location[] = [
            'Entre Ríos',
            'La Verbena'
        ];
        $location[] = [
            'Entre Ríos',
            'Laguna Benítez'
        ];
        $location[] = [
            'Entre Ríos',
            'Larroque'
        ];
        $location[] = [
            'Entre Ríos',
            'Las Cuevas'
        ];
        $location[] = [
            'Entre Ríos',
            'Las Garzas'
        ];
        $location[] = [
            'Entre Ríos',
            'Las Guachas'
        ];
        $location[] = [
            'Entre Ríos',
            'Las Mercedes'
        ];
        $location[] = [
            'Entre Ríos',
            'Las Moscas'
        ];
        $location[] = [
            'Entre Ríos',
            'Las Mulitas'
        ];
        $location[] = [
            'Entre Ríos',
            'Las Toscas'
        ];
        $location[] = [
            'Entre Ríos',
            'Laurencena'
        ];
        $location[] = [
            'Entre Ríos',
            'Libertador San Martín'
        ];
        $location[] = [
            'Entre Ríos',
            'Loma Limpia'
        ];
        $location[] = [
            'Entre Ríos',
            'Los Ceibos'
        ];
        $location[] = [
            'Entre Ríos',
            'Los Charruas'
        ];
        $location[] = [
            'Entre Ríos',
            'Los Conquistadores'
        ];
        $location[] = [
            'Entre Ríos',
            'Lucas González'
        ];
        $location[] = [
            'Entre Ríos',
            'Lucas N.'
        ];
        $location[] = [
            'Entre Ríos',
            'Lucas S. 1ª'
        ];
        $location[] = [
            'Entre Ríos',
            'Lucas S. 2ª'
        ];
        $location[] = [
            'Entre Ríos',
            'Maciá'
        ];
        $location[] = [
            'Entre Ríos',
            'María Grande'
        ];
        $location[] = [
            'Entre Ríos',
            'María Grande 2ª'
        ];
        $location[] = [
            'Entre Ríos',
            'Médanos'
        ];
        $location[] = [
            'Entre Ríos',
            'Mojones N.'
        ];
        $location[] = [
            'Entre Ríos',
            'Mojones S.'
        ];
        $location[] = [
            'Entre Ríos',
            'Molino Doll'
        ];
        $location[] = [
            'Entre Ríos',
            'Monte Redondo'
        ];
        $location[] = [
            'Entre Ríos',
            'Montoya'
        ];
        $location[] = [
            'Entre Ríos',
            'Mulas Grandes'
        ];
        $location[] = [
            'Entre Ríos',
            'Ñancay'
        ];
        $location[] = [
            'Entre Ríos',
            'Nogoyá'
        ];
        $location[] = [
            'Entre Ríos',
            'Nueva Escocia'
        ];
        $location[] = [
            'Entre Ríos',
            'Nueva Vizcaya'
        ];
        $location[] = [
            'Entre Ríos',
            'Ombú'
        ];
        $location[] = [
            'Entre Ríos',
            'Oro Verde'
        ];
        $location[] = [
            'Entre Ríos',
            'Paraná'
        ];
        $location[] = [
            'Entre Ríos',
            'Pasaje Guayaquil'
        ];
        $location[] = [
            'Entre Ríos',
            'Pasaje Las Tunas'
        ];
        $location[] = [
            'Entre Ríos',
            'Paso de La Arena'
        ];
        $location[] = [
            'Entre Ríos',
            'Paso de La Laguna'
        ];
        $location[] = [
            'Entre Ríos',
            'Paso de Las Piedras'
        ];
        $location[] = [
            'Entre Ríos',
            'Paso Duarte'
        ];
        $location[] = [
            'Entre Ríos',
            'Pastor Britos'
        ];
        $location[] = [
            'Entre Ríos',
            'Pedernal'
        ];
        $location[] = [
            'Entre Ríos',
            'Perdices'
        ];
        $location[] = [
            'Entre Ríos',
            'Picada Berón'
        ];
        $location[] = [
            'Entre Ríos',
            'Piedras Blancas'
        ];
        $location[] = [
            'Entre Ríos',
            'Primer Distrito Cuchilla'
        ];
        $location[] = [
            'Entre Ríos',
            'Primero de Mayo'
        ];
        $location[] = [
            'Entre Ríos',
            'Pronunciamiento'
        ];
        $location[] = [
            'Entre Ríos',
            'Pto. Algarrobo'
        ];
        $location[] = [
            'Entre Ríos',
            'Pto. Ibicuy'
        ];
        $location[] = [
            'Entre Ríos',
            'Pueblo Brugo'
        ];
        $location[] = [
            'Entre Ríos',
            'Pueblo Cazes'
        ];
        $location[] = [
            'Entre Ríos',
            'Pueblo Gral. Belgrano'
        ];
        $location[] = [
            'Entre Ríos',
            'Pueblo Liebig'
        ];
        $location[] = [
            'Entre Ríos',
            'Puerto Yeruá'
        ];
        $location[] = [
            'Entre Ríos',
            'Punta del Monte'
        ];
        $location[] = [
            'Entre Ríos',
            'Quebracho'
        ];
        $location[] = [
            'Entre Ríos',
            'Quinto Distrito'
        ];
        $location[] = [
            'Entre Ríos',
            'Raices Oeste'
        ];
        $location[] = [
            'Entre Ríos',
            'Rincón de Nogoyá'
        ];
        $location[] = [
            'Entre Ríos',
            'Rincón del Cinto'
        ];
        $location[] = [
            'Entre Ríos',
            'Rincón del Doll'
        ];
        $location[] = [
            'Entre Ríos',
            'Rincón del Gato'
        ];
        $location[] = [
            'Entre Ríos',
            'Rocamora'
        ];
        $location[] = [
            'Entre Ríos',
            'Rosario del Tala'
        ];
        $location[] = [
            'Entre Ríos',
            'San Benito'
        ];
        $location[] = [
            'Entre Ríos',
            'San Cipriano'
        ];
        $location[] = [
            'Entre Ríos',
            'San Ernesto'
        ];
        $location[] = [
            'Entre Ríos',
            'San Gustavo'
        ];
        $location[] = [
            'Entre Ríos',
            'San Jaime'
        ];
        $location[] = [
            'Entre Ríos',
            'San José'
        ];
        $location[] = [
            'Entre Ríos',
            'San José de Feliciano'
        ];
        $location[] = [
            'Entre Ríos',
            'San Justo'
        ];
        $location[] = [
            'Entre Ríos',
            'San Marcial'
        ];
        $location[] = [
            'Entre Ríos',
            'San Pedro'
        ];
        $location[] = [
            'Entre Ríos',
            'San Ramírez'
        ];
        $location[] = [
            'Entre Ríos',
            'San Ramón'
        ];
        $location[] = [
            'Entre Ríos',
            'San Roque'
        ];
        $location[] = [
            'Entre Ríos',
            'San Salvador'
        ];
        $location[] = [
            'Entre Ríos',
            'San Víctor'
        ];
        $location[] = [
            'Entre Ríos',
            'Santa Ana'
        ];
        $location[] = [
            'Entre Ríos',
            'Santa Anita'
        ];
        $location[] = [
            'Entre Ríos',
            'Santa Elena'
        ];
        $location[] = [
            'Entre Ríos',
            'Santa Lucía'
        ];
        $location[] = [
            'Entre Ríos',
            'Santa Luisa'
        ];
        $location[] = [
            'Entre Ríos',
            'Sauce de Luna'
        ];
        $location[] = [
            'Entre Ríos',
            'Sauce Montrull'
        ];
        $location[] = [
            'Entre Ríos',
            'Sauce Pinto'
        ];
        $location[] = [
            'Entre Ríos',
            'Sauce Sur'
        ];
        $location[] = [
            'Entre Ríos',
            'Seguí'
        ];
        $location[] = [
            'Entre Ríos',
            'Sir Leonard'
        ];
        $location[] = [
            'Entre Ríos',
            'Sosa'
        ];
        $location[] = [
            'Entre Ríos',
            'Tabossi'
        ];
        $location[] = [
            'Entre Ríos',
            'Tezanos Pinto'
        ];
        $location[] = [
            'Entre Ríos',
            'Ubajay'
        ];
        $location[] = [
            'Entre Ríos',
            'Urdinarrain'
        ];
        $location[] = [
            'Entre Ríos',
            'Veinte de Septiembre'
        ];
        $location[] = [
            'Entre Ríos',
            'Viale'
        ];
        $location[] = [
            'Entre Ríos',
            'Victoria'
        ];
        $location[] = [
            'Entre Ríos',
            'Villa Clara'
        ];
        $location[] = [
            'Entre Ríos',
            'Villa del Rosario'
        ];
        $location[] = [
            'Entre Ríos',
            'Villa Domínguez'
        ];
        $location[] = [
            'Entre Ríos',
            'Villa Elisa'
        ];
        $location[] = [
            'Entre Ríos',
            'Villa Fontana'
        ];
        $location[] = [
            'Entre Ríos',
            'Villa Gdor. Etchevehere'
        ];
        $location[] = [
            'Entre Ríos',
            'Villa Mantero'
        ];
        $location[] = [
            'Entre Ríos',
            'Villa Paranacito'
        ];
        $location[] = [
            'Entre Ríos',
            'Villa Urquiza'
        ];
        $location[] = [
            'Entre Ríos',
            'Villaguay'
        ];
        $location[] = [
            'Entre Ríos',
            'Walter Moss'
        ];
        $location[] = [
            'Entre Ríos',
            'Yacaré'
        ];
        $location[] = [
            'Entre Ríos',
            'Yeso Oeste'
        ];
        $location[] = [
            'Formosa',
            'Buena Vista'
        ];
        $location[] = [
            'Formosa',
            'Clorinda'
        ];
        $location[] = [
            'Formosa',
            'Col. Pastoril'
        ];
        $location[] = [
            'Formosa',
            'Cte. Fontana'
        ];
        $location[] = [
            'Formosa',
            'El Colorado'
        ];
        $location[] = [
            'Formosa',
            'El Espinillo'
        ];
        $location[] = [
            'Formosa',
            'Estanislao Del Campo'
        ];
        $location[] = [
            'Formosa',
            'Formosa'
        ];
        $location[] = [
            'Formosa',
            'Fortín Lugones'
        ];
        $location[] = [
            'Formosa',
            'Gral. Lucio V. Mansilla'
        ];
        $location[] = [
            'Formosa',
            'Gral. Manuel Belgrano'
        ];
        $location[] = [
            'Formosa',
            'Gral. Mosconi'
        ];
        $location[] = [
            'Formosa',
            'Gran Guardia'
        ];
        $location[] = [
            'Formosa',
            'Herradura'
        ];
        $location[] = [
            'Formosa',
            'Ibarreta'
        ];
        $location[] = [
            'Formosa',
            'Ing. Juárez'
        ];
        $location[] = [
            'Formosa',
            'Laguna Blanca'
        ];
        $location[] = [
            'Formosa',
            'Laguna Naick Neck'
        ];
        $location[] = [
            'Formosa',
            'Laguna Yema'
        ];
        $location[] = [
            'Formosa',
            'Las Lomitas'
        ];
        $location[] = [
            'Formosa',
            'Los Chiriguanos'
        ];
        $location[] = [
            'Formosa',
            'Mayor V. Villafañe'
        ];
        $location[] = [
            'Formosa',
            'Misión San Fco.'
        ];
        $location[] = [
            'Formosa',
            'Palo Santo'
        ];
        $location[] = [
            'Formosa',
            'Pirané'
        ];
        $location[] = [
            'Formosa',
            'Pozo del Maza'
        ];
        $location[] = [
            'Formosa',
            'Riacho He-He'
        ];
        $location[] = [
            'Formosa',
            'San Hilario'
        ];
        $location[] = [
            'Formosa',
            'San Martín II'
        ];
        $location[] = [
            'Formosa',
            'Siete Palmas'
        ];
        $location[] = [
            'Formosa',
            'Subteniente Perín'
        ];
        $location[] = [
            'Formosa',
            'Tres Lagunas'
        ];
        $location[] = [
            'Formosa',
            'Villa Dos Trece'
        ];
        $location[] = [
            'Formosa',
            'Villa Escolar'
        ];
        $location[] = [
            'Formosa',
            'Villa Gral. Güemes'
        ];
        $location[] = [
            'Jujuy',
            'Abdon Castro Tolay'
        ];
        $location[] = [
            'Jujuy',
            'Abra Pampa'
        ];
        $location[] = [
            'Jujuy',
            'Abralaite'
        ];
        $location[] = [
            'Jujuy',
            'Aguas Calientes'
        ];
        $location[] = [
            'Jujuy',
            'Arrayanal'
        ];
        $location[] = [
            'Jujuy',
            'Barrios'
        ];
        $location[] = [
            'Jujuy',
            'Caimancito'
        ];
        $location[] = [
            'Jujuy',
            'Calilegua'
        ];
        $location[] = [
            'Jujuy',
            'Cangrejillos'
        ];
        $location[] = [
            'Jujuy',
            'Caspala'
        ];
        $location[] = [
            'Jujuy',
            'Catuá'
        ];
        $location[] = [
            'Jujuy',
            'Cieneguillas'
        ];
        $location[] = [
            'Jujuy',
            'Coranzulli'
        ];
        $location[] = [
            'Jujuy',
            'Cusi-Cusi'
        ];
        $location[] = [
            'Jujuy',
            'El Aguilar'
        ];
        $location[] = [
            'Jujuy',
            'El Carmen'
        ];
        $location[] = [
            'Jujuy',
            'El Cóndor'
        ];
        $location[] = [
            'Jujuy',
            'El Fuerte'
        ];
        $location[] = [
            'Jujuy',
            'El Piquete'
        ];
        $location[] = [
            'Jujuy',
            'El Talar'
        ];
        $location[] = [
            'Jujuy',
            'Fraile Pintado'
        ];
        $location[] = [
            'Jujuy',
            'Hipólito Yrigoyen'
        ];
        $location[] = [
            'Jujuy',
            'Huacalera'
        ];
        $location[] = [
            'Jujuy',
            'Humahuaca'
        ];
        $location[] = [
            'Jujuy',
            'La Esperanza'
        ];
        $location[] = [
            'Jujuy',
            'La Mendieta'
        ];
        $location[] = [
            'Jujuy',
            'La Quiaca'
        ];
        $location[] = [
            'Jujuy',
            'Ledesma'
        ];
        $location[] = [
            'Jujuy',
            'Libertador Gral. San Martin'
        ];
        $location[] = [
            'Jujuy',
            'Maimara'
        ];
        $location[] = [
            'Jujuy',
            'Mina Pirquitas'
        ];
        $location[] = [
            'Jujuy',
            'Monterrico'
        ];
        $location[] = [
            'Jujuy',
            'Palma Sola'
        ];
        $location[] = [
            'Jujuy',
            'Palpalá'
        ];
        $location[] = [
            'Jujuy',
            'Pampa Blanca'
        ];
        $location[] = [
            'Jujuy',
            'Pampichuela'
        ];
        $location[] = [
            'Jujuy',
            'Perico'
        ];
        $location[] = [
            'Jujuy',
            'Puesto del Marqués'
        ];
        $location[] = [
            'Jujuy',
            'Puesto Viejo'
        ];
        $location[] = [
            'Jujuy',
            'Pumahuasi'
        ];
        $location[] = [
            'Jujuy',
            'Purmamarca'
        ];
        $location[] = [
            'Jujuy',
            'Rinconada'
        ];
        $location[] = [
            'Jujuy',
            'Rodeitos'
        ];
        $location[] = [
            'Jujuy',
            'Rosario de Río Grande'
        ];
        $location[] = [
            'Jujuy',
            'San Antonio'
        ];
        $location[] = [
            'Jujuy',
            'San Francisco'
        ];
        $location[] = [
            'Jujuy',
            'San Pedro'
        ];
        $location[] = [
            'Jujuy',
            'San Rafael'
        ];
        $location[] = [
            'Jujuy',
            'San Salvador'
        ];
        $location[] = [
            'Jujuy',
            'Santa Ana'
        ];
        $location[] = [
            'Jujuy',
            'Santa Catalina'
        ];
        $location[] = [
            'Jujuy',
            'Santa Clara'
        ];
        $location[] = [
            'Jujuy',
            'Susques'
        ];
        $location[] = [
            'Jujuy',
            'Tilcara'
        ];
        $location[] = [
            'Jujuy',
            'Tres Cruces'
        ];
        $location[] = [
            'Jujuy',
            'Tumbaya'
        ];
        $location[] = [
            'Jujuy',
            'Valle Grande'
        ];
        $location[] = [
            'Jujuy',
            'Vinalito'
        ];
        $location[] = [
            'Jujuy',
            'Volcán'
        ];
        $location[] = [
            'Jujuy',
            'Yala'
        ];
        $location[] = [
            'Jujuy',
            'Yaví'
        ];
        $location[] = [
            'Jujuy',
            'Yuto'
        ];
        $location[] = [
            'La Pampa',
            'Abramo'
        ];
        $location[] = [
            'La Pampa',
            'Adolfo Van Praet'
        ];
        $location[] = [
            'La Pampa',
            'Agustoni'
        ];
        $location[] = [
            'La Pampa',
            'Algarrobo del Aguila'
        ];
        $location[] = [
            'La Pampa',
            'Alpachiri'
        ];
        $location[] = [
            'La Pampa',
            'Alta Italia'
        ];
        $location[] = [
            'La Pampa',
            'Anguil'
        ];
        $location[] = [
            'La Pampa',
            'Arata'
        ];
        $location[] = [
            'La Pampa',
            'Ataliva Roca'
        ];
        $location[] = [
            'La Pampa',
            'Bernardo Larroude'
        ];
        $location[] = [
            'La Pampa',
            'Bernasconi'
        ];
        $location[] = [
            'La Pampa',
            'Caleufú'
        ];
        $location[] = [
            'La Pampa',
            'Carro Quemado'
        ];
        $location[] = [
            'La Pampa',
            'Catriló'
        ];
        $location[] = [
            'La Pampa',
            'Ceballos'
        ];
        $location[] = [
            'La Pampa',
            'Chacharramendi'
        ];
        $location[] = [
            'La Pampa',
            'Col. Barón'
        ];
        $location[] = [
            'La Pampa',
            'Col. Santa María'
        ];
        $location[] = [
            'La Pampa',
            'Conhelo'
        ];
        $location[] = [
            'La Pampa',
            'Coronel Hilario Lagos'
        ];
        $location[] = [
            'La Pampa',
            'Cuchillo-Có'
        ];
        $location[] = [
            'La Pampa',
            'Doblas'
        ];
        $location[] = [
            'La Pampa',
            'Dorila'
        ];
        $location[] = [
            'La Pampa',
            'Eduardo Castex'
        ];
        $location[] = [
            'La Pampa',
            'Embajador Martini'
        ];
        $location[] = [
            'La Pampa',
            'Falucho'
        ];
        $location[] = [
            'La Pampa',
            'Gral. Acha'
        ];
        $location[] = [
            'La Pampa',
            'Gral. Manuel Campos'
        ];
        $location[] = [
            'La Pampa',
            'Gral. Pico'
        ];
        $location[] = [
            'La Pampa',
            'Guatraché'
        ];
        $location[] = [
            'La Pampa',
            'Ing. Luiggi'
        ];
        $location[] = [
            'La Pampa',
            'Intendente Alvear'
        ];
        $location[] = [
            'La Pampa',
            'Jacinto Arauz'
        ];
        $location[] = [
            'La Pampa',
            'La Adela'
        ];
        $location[] = [
            'La Pampa',
            'La Humada'
        ];
        $location[] = [
            'La Pampa',
            'La Maruja'
        ];
        $location[] = [
            'La Pampa',
            'La Pampa'
        ];
        $location[] = [
            'La Pampa',
            'La Reforma'
        ];
        $location[] = [
            'La Pampa',
            'Limay Mahuida'
        ];
        $location[] = [
            'La Pampa',
            'Lonquimay'
        ];
        $location[] = [
            'La Pampa',
            'Loventuel'
        ];
        $location[] = [
            'La Pampa',
            'Luan Toro'
        ];
        $location[] = [
            'La Pampa',
            'Macachín'
        ];
        $location[] = [
            'La Pampa',
            'Maisonnave'
        ];
        $location[] = [
            'La Pampa',
            'Mauricio Mayer'
        ];
        $location[] = [
            'La Pampa',
            'Metileo'
        ];
        $location[] = [
            'La Pampa',
            'Miguel Cané'
        ];
        $location[] = [
            'La Pampa',
            'Miguel Riglos'
        ];
        $location[] = [
            'La Pampa',
            'Monte Nievas'
        ];
        $location[] = [
            'La Pampa',
            'Parera'
        ];
        $location[] = [
            'La Pampa',
            'Perú'
        ];
        $location[] = [
            'La Pampa',
            'Pichi-Huinca'
        ];
        $location[] = [
            'La Pampa',
            'Puelches'
        ];
        $location[] = [
            'La Pampa',
            'Puelén'
        ];
        $location[] = [
            'La Pampa',
            'Quehue'
        ];
        $location[] = [
            'La Pampa',
            'Quemú Quemú'
        ];
        $location[] = [
            'La Pampa',
            'Quetrequén'
        ];
        $location[] = [
            'La Pampa',
            'Rancul'
        ];
        $location[] = [
            'La Pampa',
            'Realicó'
        ];
        $location[] = [
            'La Pampa',
            'Relmo'
        ];
        $location[] = [
            'La Pampa',
            'Rolón'
        ];
        $location[] = [
            'La Pampa',
            'Rucanelo'
        ];
        $location[] = [
            'La Pampa',
            'Sarah'
        ];
        $location[] = [
            'La Pampa',
            'Speluzzi'
        ];
        $location[] = [
            'La Pampa',
            'Sta. Isabel'
        ];
        $location[] = [
            'La Pampa',
            'Sta. Rosa'
        ];
        $location[] = [
            'La Pampa',
            'Sta. Teresa'
        ];
        $location[] = [
            'La Pampa',
            'Telén'
        ];
        $location[] = [
            'La Pampa',
            'Toay'
        ];
        $location[] = [
            'La Pampa',
            'Tomas M. de Anchorena'
        ];
        $location[] = [
            'La Pampa',
            'Trenel'
        ];
        $location[] = [
            'La Pampa',
            'Unanue'
        ];
        $location[] = [
            'La Pampa',
            'Uriburu'
        ];
        $location[] = [
            'La Pampa',
            'Veinticinco de Mayo'
        ];
        $location[] = [
            'La Pampa',
            'Vertiz'
        ];
        $location[] = [
            'La Pampa',
            'Victorica'
        ];
        $location[] = [
            'La Pampa',
            'Villa Mirasol'
        ];
        $location[] = [
            'La Pampa',
            'Winifreda'
        ];
        $location[] = [
            'La Rioja',
            'Arauco'
        ];
        $location[] = [
            'La Rioja',
            'Capital'
        ];
        $location[] = [
            'La Rioja',
            'Castro Barros'
        ];
        $location[] = [
            'La Rioja',
            'Chamical'
        ];
        $location[] = [
            'La Rioja',
            'Chilecito'
        ];
        $location[] = [
            'La Rioja',
            'Coronel F. Varela'
        ];
        $location[] = [
            'La Rioja',
            'Famatina'
        ];
        $location[] = [
            'La Rioja',
            'Gral. A.V.Peñaloza'
        ];
        $location[] = [
            'La Rioja',
            'Gral. Belgrano'
        ];
        $location[] = [
            'La Rioja',
            'Gral. J.F. Quiroga'
        ];
        $location[] = [
            'La Rioja',
            'Gral. Lamadrid'
        ];
        $location[] = [
            'La Rioja',
            'Gral. Ocampo'
        ];
        $location[] = [
            'La Rioja',
            'Gral. San Martín'
        ];
        $location[] = [
            'La Rioja',
            'Independencia'
        ];
        $location[] = [
            'La Rioja',
            'Rosario Penaloza'
        ];
        $location[] = [
            'La Rioja',
            'San Blas de Los Sauces'
        ];
        $location[] = [
            'La Rioja',
            'Sanagasta'
        ];
        $location[] = [
            'La Rioja',
            'Vinchina'
        ];
        $location[] = [
            'Mendoza',
            'Capital'
        ];
        $location[] = [
            'Mendoza',
            'Chacras de Coria'
        ];
        $location[] = [
            'Mendoza',
            'Dorrego'
        ];
        $location[] = [
            'Mendoza',
            'Gllen'
        ];
        $location[] = [
            'Mendoza',
            'Godoy Cruz'
        ];
        $location[] = [
            'Mendoza',
            'Gral. Alvear'
        ];
        $location[] = [
            'Mendoza',
            'Guaymallén'
        ];
        $location[] = [
            'Mendoza',
            'Junín'
        ];
        $location[] = [
            'Mendoza',
            'La Paz'
        ];
        $location[] = [
            'Mendoza',
            'Las Heras'
        ];
        $location[] = [
            'Mendoza',
            'Lavalle'
        ];
        $location[] = [
            'Mendoza',
            'Luján'
        ];
        $location[] = [
            'Mendoza',
            'Luján De Cuyo'
        ];
        $location[] = [
            'Mendoza',
            'Maipú'
        ];
        $location[] = [
            'Mendoza',
            'Malargüe'
        ];
        $location[] = [
            'Mendoza',
            'Rivadavia'
        ];
        $location[] = [
            'Mendoza',
            'San Carlos'
        ];
        $location[] = [
            'Mendoza',
            'San Martín'
        ];
        $location[] = [
            'Mendoza',
            'San Rafael'
        ];
        $location[] = [
            'Mendoza',
            'Sta. Rosa'
        ];
        $location[] = [
            'Mendoza',
            'Tunuyán'
        ];
        $location[] = [
            'Mendoza',
            'Tupungato'
        ];
        $location[] = [
            'Mendoza',
            'Villa Nueva'
        ];
        $location[] = [
            'Misiones',
            'Alba Posse'
        ];
        $location[] = [
            'Misiones',
            'Almafuerte'
        ];
        $location[] = [
            'Misiones',
            'Apóstoles'
        ];
        $location[] = [
            'Misiones',
            'Aristóbulo Del Valle'
        ];
        $location[] = [
            'Misiones',
            'Arroyo Del Medio'
        ];
        $location[] = [
            'Misiones',
            'Azara'
        ];
        $location[] = [
            'Misiones',
            'Bdo. De Irigoyen'
        ];
        $location[] = [
            'Misiones',
            'Bonpland'
        ];
        $location[] = [
            'Misiones',
            'Caá Yari'
        ];
        $location[] = [
            'Misiones',
            'Campo Grande'
        ];
        $location[] = [
            'Misiones',
            'Campo Ramón'
        ];
        $location[] = [
            'Misiones',
            'Campo Viera'
        ];
        $location[] = [
            'Misiones',
            'Candelaria'
        ];
        $location[] = [
            'Misiones',
            'Capioví'
        ];
        $location[] = [
            'Misiones',
            'Caraguatay'
        ];
        $location[] = [
            'Misiones',
            'Cdte. Guacurarí'
        ];
        $location[] = [
            'Misiones',
            'Cerro Azul'
        ];
        $location[] = [
            'Misiones',
            'Cerro Corá'
        ];
        $location[] = [
            'Misiones',
            'Col. Alberdi'
        ];
        $location[] = [
            'Misiones',
            'Col. Aurora'
        ];
        $location[] = [
            'Misiones',
            'Col. Delicia'
        ];
        $location[] = [
            'Misiones',
            'Col. Polana'
        ];
        $location[] = [
            'Misiones',
            'Col. Victoria'
        ];
        $location[] = [
            'Misiones',
            'Col. Wanda'
        ];
        $location[] = [
            'Misiones',
            'Concepción De La Sierra'
        ];
        $location[] = [
            'Misiones',
            'Corpus'
        ];
        $location[] = [
            'Misiones',
            'Dos Arroyos'
        ];
        $location[] = [
            'Misiones',
            'Dos de Mayo'
        ];
        $location[] = [
            'Misiones',
            'El Alcázar'
        ];
        $location[] = [
            'Misiones',
            'El Dorado'
        ];
        $location[] = [
            'Misiones',
            'El Soberbio'
        ];
        $location[] = [
            'Misiones',
            'Esperanza'
        ];
        $location[] = [
            'Misiones',
            'F. Ameghino'
        ];
        $location[] = [
            'Misiones',
            'Fachinal'
        ];
        $location[] = [
            'Misiones',
            'Garuhapé'
        ];
        $location[] = [
            'Misiones',
            'Garupá'
        ];
        $location[] = [
            'Misiones',
            'Gdor. López'
        ];
        $location[] = [
            'Misiones',
            'Gdor. Roca'
        ];
        $location[] = [
            'Misiones',
            'Gral. Alvear'
        ];
        $location[] = [
            'Misiones',
            'Gral. Urquiza'
        ];
        $location[] = [
            'Misiones',
            'Guaraní'
        ];
        $location[] = [
            'Misiones',
            'H. Yrigoyen'
        ];
        $location[] = [
            'Misiones',
            'Iguazú'
        ];
        $location[] = [
            'Misiones',
            'Itacaruaré'
        ];
        $location[] = [
            'Misiones',
            'Jardín América'
        ];
        $location[] = [
            'Misiones',
            'Leandro N. Alem'
        ];
        $location[] = [
            'Misiones',
            'Libertad'
        ];
        $location[] = [
            'Misiones',
            'Loreto'
        ];
        $location[] = [
            'Misiones',
            'Los Helechos'
        ];
        $location[] = [
            'Misiones',
            'Mártires'
        ];
        $location[] = [
            'Misiones',
            'Misiones'
        ];
        $location[] = [
            'Misiones',
            'Mojón Grande'
        ];
        $location[] = [
            'Misiones',
            'Montecarlo'
        ];
        $location[] = [
            'Misiones',
            'Nueve de Julio'
        ];
        $location[] = [
            'Misiones',
            'Oberá'
        ];
        $location[] = [
            'Misiones',
            'Olegario V. Andrade'
        ];
        $location[] = [
            'Misiones',
            'Panambí'
        ];
        $location[] = [
            'Misiones',
            'Posadas'
        ];
        $location[] = [
            'Misiones',
            'Profundidad'
        ];
        $location[] = [
            'Misiones',
            'Pto. Iguazú'
        ];
        $location[] = [
            'Misiones',
            'Pto. Leoni'
        ];
        $location[] = [
            'Misiones',
            'Pto. Piray'
        ];
        $location[] = [
            'Misiones',
            'Pto. Rico'
        ];
        $location[] = [
            'Misiones',
            'Ruiz de Montoya'
        ];
        $location[] = [
            'Misiones',
            'San Antonio'
        ];
        $location[] = [
            'Misiones',
            'San Ignacio'
        ];
        $location[] = [
            'Misiones',
            'San Javier'
        ];
        $location[] = [
            'Misiones',
            'San José'
        ];
        $location[] = [
            'Misiones',
            'San Martín'
        ];
        $location[] = [
            'Misiones',
            'San Pedro'
        ];
        $location[] = [
            'Misiones',
            'San Vicente'
        ];
        $location[] = [
            'Misiones',
            'Santiago De Liniers'
        ];
        $location[] = [
            'Misiones',
            'Santo Pipo'
        ];
        $location[] = [
            'Misiones',
            'Sta. Ana'
        ];
        $location[] = [
            'Misiones',
            'Sta. María'
        ];
        $location[] = [
            'Misiones',
            'Tres Capones'
        ];
        $location[] = [
            'Misiones',
            'Veinticinco de Mayo'
        ];
        $location[] = [
            'Misiones',
            'Wanda'
        ];
        $location[] = [
            'Neuquén',
            'Aguada San Roque'
        ];
        $location[] = [
            'Neuquén',
            'Aluminé'
        ];
        $location[] = [
            'Neuquén',
            'Andacollo'
        ];
        $location[] = [
            'Neuquén',
            'Añelo'
        ];
        $location[] = [
            'Neuquén',
            'Bajada del Agrio'
        ];
        $location[] = [
            'Neuquén',
            'Barrancas'
        ];
        $location[] = [
            'Neuquén',
            'Buta Ranquil'
        ];
        $location[] = [
            'Neuquén',
            'Capital'
        ];
        $location[] = [
            'Neuquén',
            'Caviahué'
        ];
        $location[] = [
            'Neuquén',
            'Centenario'
        ];
        $location[] = [
            'Neuquén',
            'Chorriaca'
        ];
        $location[] = [
            'Neuquén',
            'Chos Malal'
        ];
        $location[] = [
            'Neuquén',
            'Cipolletti'
        ];
        $location[] = [
            'Neuquén',
            'Covunco Abajo'
        ];
        $location[] = [
            'Neuquén',
            'Coyuco Cochico'
        ];
        $location[] = [
            'Neuquén',
            'Cutral Có'
        ];
        $location[] = [
            'Neuquén',
            'El Cholar'
        ];
        $location[] = [
            'Neuquén',
            'El Huecú'
        ];
        $location[] = [
            'Neuquén',
            'El Sauce'
        ];
        $location[] = [
            'Neuquén',
            'Guañacos'
        ];
        $location[] = [
            'Neuquén',
            'Huinganco'
        ];
        $location[] = [
            'Neuquén',
            'Las Coloradas'
        ];
        $location[] = [
            'Neuquén',
            'Las Lajas'
        ];
        $location[] = [
            'Neuquén',
            'Las Ovejas'
        ];
        $location[] = [
            'Neuquén',
            'Loncopué'
        ];
        $location[] = [
            'Neuquén',
            'Los Catutos'
        ];
        $location[] = [
            'Neuquén',
            'Los Chihuidos'
        ];
        $location[] = [
            'Neuquén',
            'Los Miches'
        ];
        $location[] = [
            'Neuquén',
            'Manzano Amargo'
        ];
        $location[] = [
            'Neuquén',
            'Neuquén'
        ];
        $location[] = [
            'Neuquén',
            'Octavio Pico'
        ];
        $location[] = [
            'Neuquén',
            'Paso Aguerre'
        ];
        $location[] = [
            'Neuquén',
            'Picún Leufú'
        ];
        $location[] = [
            'Neuquén',
            'Piedra del Aguila'
        ];
        $location[] = [
            'Neuquén',
            'Pilo Lil'
        ];
        $location[] = [
            'Neuquén',
            'Plaza Huincul'
        ];
        $location[] = [
            'Neuquén',
            'Plottier'
        ];
        $location[] = [
            'Neuquén',
            'Quili Malal'
        ];
        $location[] = [
            'Neuquén',
            'Ramón Castro'
        ];
        $location[] = [
            'Neuquén',
            'Rincón de Los Sauces'
        ];
        $location[] = [
            'Neuquén',
            'San Martín de Los Andes'
        ];
        $location[] = [
            'Neuquén',
            'San Patricio del Chañar'
        ];
        $location[] = [
            'Neuquén',
            'Santo Tomás'
        ];
        $location[] = [
            'Neuquén',
            'Sauzal Bonito'
        ];
        $location[] = [
            'Neuquén',
            'Senillosa'
        ];
        $location[] = [
            'Neuquén',
            'Taquimilán'
        ];
        $location[] = [
            'Neuquén',
            'Tricao Malal'
        ];
        $location[] = [
            'Neuquén',
            'Varvarco'
        ];
        $location[] = [
            'Neuquén',
            'Villa Curí Leuvu'
        ];
        $location[] = [
            'Neuquén',
            'Villa del Nahueve'
        ];
        $location[] = [
            'Neuquén',
            'Villa del Puente Picún Leuvú'
        ];
        $location[] = [
            'Neuquén',
            'Villa El Chocón'
        ];
        $location[] = [
            'Neuquén',
            'Villa La Angostura'
        ];
        $location[] = [
            'Neuquén',
            'Villa Pehuenia'
        ];
        $location[] = [
            'Neuquén',
            'Villa Traful'
        ];
        $location[] = [
            'Neuquén',
            'Vista Alegre'
        ];
        $location[] = [
            'Neuquén',
            'Zapala'
        ];
        $location[] = [
            'Río Negro',
            'Aguada Cecilio'
        ];
        $location[] = [
            'Río Negro',
            'Aguada de Guerra'
        ];
        $location[] = [
            'Río Negro',
            'Allén'
        ];
        $location[] = [
            'Río Negro',
            'Arroyo de La Ventana'
        ];
        $location[] = [
            'Río Negro',
            'Arroyo Los Berros'
        ];
        $location[] = [
            'Río Negro',
            'Bariloche'
        ];
        $location[] = [
            'Río Negro',
            'Calte. Cordero'
        ];
        $location[] = [
            'Río Negro',
            'Campo Grande'
        ];
        $location[] = [
            'Río Negro',
            'Catriel'
        ];
        $location[] = [
            'Río Negro',
            'Cerro Policía'
        ];
        $location[] = [
            'Río Negro',
            'Cervantes'
        ];
        $location[] = [
            'Río Negro',
            'Chelforo'
        ];
        $location[] = [
            'Río Negro',
            'Chimpay'
        ];
        $location[] = [
            'Río Negro',
            'Chinchinales'
        ];
        $location[] = [
            'Río Negro',
            'Chipauquil'
        ];
        $location[] = [
            'Río Negro',
            'Choele Choel'
        ];
        $location[] = [
            'Río Negro',
            'Cinco Saltos'
        ];
        $location[] = [
            'Río Negro',
            'Cipolletti'
        ];
        $location[] = [
            'Río Negro',
            'Clemente Onelli'
        ];
        $location[] = [
            'Río Negro',
            'Colán Conhue'
        ];
        $location[] = [
            'Río Negro',
            'Comallo'
        ];
        $location[] = [
            'Río Negro',
            'Comicó'
        ];
        $location[] = [
            'Río Negro',
            'Cona Niyeu'
        ];
        $location[] = [
            'Río Negro',
            'Coronel Belisle'
        ];
        $location[] = [
            'Río Negro',
            'Cubanea'
        ];
        $location[] = [
            'Río Negro',
            'Darwin'
        ];
        $location[] = [
            'Río Negro',
            'Dina Huapi'
        ];
        $location[] = [
            'Río Negro',
            'El Bolsón'
        ];
        $location[] = [
            'Río Negro',
            'El Caín'
        ];
        $location[] = [
            'Río Negro',
            'El Manso'
        ];
        $location[] = [
            'Río Negro',
            'Gral. Conesa'
        ];
        $location[] = [
            'Río Negro',
            'Gral. Enrique Godoy'
        ];
        $location[] = [
            'Río Negro',
            'Gral. Fernandez Oro'
        ];
        $location[] = [
            'Río Negro',
            'Gral. Roca'
        ];
        $location[] = [
            'Río Negro',
            'Guardia Mitre'
        ];
        $location[] = [
            'Río Negro',
            'Ing. Huergo'
        ];
        $location[] = [
            'Río Negro',
            'Ing. Jacobacci'
        ];
        $location[] = [
            'Río Negro',
            'Laguna Blanca'
        ];
        $location[] = [
            'Río Negro',
            'Lamarque'
        ];
        $location[] = [
            'Río Negro',
            'Las Grutas'
        ];
        $location[] = [
            'Río Negro',
            'Los Menucos'
        ];
        $location[] = [
            'Río Negro',
            'Luis Beltrán'
        ];
        $location[] = [
            'Río Negro',
            'Mainqué'
        ];
        $location[] = [
            'Río Negro',
            'Mamuel Choique'
        ];
        $location[] = [
            'Río Negro',
            'Maquinchao'
        ];
        $location[] = [
            'Río Negro',
            'Mencué'
        ];
        $location[] = [
            'Río Negro',
            'Mtro. Ramos Mexia'
        ];
        $location[] = [
            'Río Negro',
            'Nahuel Niyeu'
        ];
        $location[] = [
            'Río Negro',
            'Naupa Huen'
        ];
        $location[] = [
            'Río Negro',
            'Ñorquinco'
        ];
        $location[] = [
            'Río Negro',
            'Ojos de Agua'
        ];
        $location[] = [
            'Río Negro',
            'Paso de Agua'
        ];
        $location[] = [
            'Río Negro',
            'Paso Flores'
        ];
        $location[] = [
            'Río Negro',
            'Peñas Blancas'
        ];
        $location[] = [
            'Río Negro',
            'Pichi Mahuida'
        ];
        $location[] = [
            'Río Negro',
            'Pilcaniyeu'
        ];
        $location[] = [
            'Río Negro',
            'Pomona'
        ];
        $location[] = [
            'Río Negro',
            'Prahuaniyeu'
        ];
        $location[] = [
            'Río Negro',
            'Rincón Treneta'
        ];
        $location[] = [
            'Río Negro',
            'Río Chico'
        ];
        $location[] = [
            'Río Negro',
            'Río Colorado'
        ];
        $location[] = [
            'Río Negro',
            'Roca'
        ];
        $location[] = [
            'Río Negro',
            'San Antonio Oeste'
        ];
        $location[] = [
            'Río Negro',
            'San Javier'
        ];
        $location[] = [
            'Río Negro',
            'Sierra Colorada'
        ];
        $location[] = [
            'Río Negro',
            'Sierra Grande'
        ];
        $location[] = [
            'Río Negro',
            'Sierra Pailemán'
        ];
        $location[] = [
            'Río Negro',
            'Valcheta'
        ];
        $location[] = [
            'Río Negro',
            'Valle Azul'
        ];
        $location[] = [
            'Río Negro',
            'Viedma'
        ];
        $location[] = [
            'Río Negro',
            'Villa Llanquín'
        ];
        $location[] = [
            'Río Negro',
            'Villa Mascardi'
        ];
        $location[] = [
            'Río Negro',
            'Villa Regina'
        ];
        $location[] = [
            'Río Negro',
            'Yaminué'
        ];
        $location[] = [
            'Salta',
            'A. Saravia'
        ];
        $location[] = [
            'Salta',
            'Aguaray'
        ];
        $location[] = [
            'Salta',
            'Angastaco'
        ];
        $location[] = [
            'Salta',
            'Animaná'
        ];
        $location[] = [
            'Salta',
            'Cachi'
        ];
        $location[] = [
            'Salta',
            'Cafayate'
        ];
        $location[] = [
            'Salta',
            'Campo Quijano'
        ];
        $location[] = [
            'Salta',
            'Campo Santo'
        ];
        $location[] = [
            'Salta',
            'Capital'
        ];
        $location[] = [
            'Salta',
            'Cerrillos'
        ];
        $location[] = [
            'Salta',
            'Chicoana'
        ];
        $location[] = [
            'Salta',
            'Col. Sta. Rosa'
        ];
        $location[] = [
            'Salta',
            'Coronel Moldes'
        ];
        $location[] = [
            'Salta',
            'El Bordo'
        ];
        $location[] = [
            'Salta',
            'El Carril'
        ];
        $location[] = [
            'Salta',
            'El Galpón'
        ];
        $location[] = [
            'Salta',
            'El Jardín'
        ];
        $location[] = [
            'Salta',
            'El Potrero'
        ];
        $location[] = [
            'Salta',
            'El Quebrachal'
        ];
        $location[] = [
            'Salta',
            'El Tala'
        ];
        $location[] = [
            'Salta',
            'Embarcación'
        ];
        $location[] = [
            'Salta',
            'Gral. Ballivian'
        ];
        $location[] = [
            'Salta',
            'Gral. Güemes'
        ];
        $location[] = [
            'Salta',
            'Gral. Mosconi'
        ];
        $location[] = [
            'Salta',
            'Gral. Pizarro'
        ];
        $location[] = [
            'Salta',
            'Guachipas'
        ];
        $location[] = [
            'Salta',
            'Hipólito Yrigoyen'
        ];
        $location[] = [
            'Salta',
            'Iruyá'
        ];
        $location[] = [
            'Salta',
            'Isla De Cañas'
        ];
        $location[] = [
            'Salta',
            'J. V. Gonzalez'
        ];
        $location[] = [
            'Salta',
            'La Caldera'
        ];
        $location[] = [
            'Salta',
            'La Candelaria'
        ];
        $location[] = [
            'Salta',
            'La Merced'
        ];
        $location[] = [
            'Salta',
            'La Poma'
        ];
        $location[] = [
            'Salta',
            'La Viña'
        ];
        $location[] = [
            'Salta',
            'Las Lajitas'
        ];
        $location[] = [
            'Salta',
            'Los Toldos'
        ];
        $location[] = [
            'Salta',
            'Metán'
        ];
        $location[] = [
            'Salta',
            'Molinos'
        ];
        $location[] = [
            'Salta',
            'Nazareno'
        ];
        $location[] = [
            'Salta',
            'Orán'
        ];
        $location[] = [
            'Salta',
            'Payogasta'
        ];
        $location[] = [
            'Salta',
            'Pichanal'
        ];
        $location[] = [
            'Salta',
            'Prof. S. Mazza'
        ];
        $location[] = [
            'Salta',
            'Río Piedras'
        ];
        $location[] = [
            'Salta',
            'Rivadavia Banda Norte'
        ];
        $location[] = [
            'Salta',
            'Rivadavia Banda Sur'
        ];
        $location[] = [
            'Salta',
            'Rosario de La Frontera'
        ];
        $location[] = [
            'Salta',
            'Rosario de Lerma'
        ];
        $location[] = [
            'Salta',
            'Saclantás'
        ];
        $location[] = [
            'Salta',
            'Salta'
        ];
        $location[] = [
            'Salta',
            'San Antonio'
        ];
        $location[] = [
            'Salta',
            'San Carlos'
        ];
        $location[] = [
            'Salta',
            'San José De Metán'
        ];
        $location[] = [
            'Salta',
            'San Ramón'
        ];
        $location[] = [
            'Salta',
            'Santa Victoria E.'
        ];
        $location[] = [
            'Salta',
            'Santa Victoria O.'
        ];
        $location[] = [
            'Salta',
            'Tartagal'
        ];
        $location[] = [
            'Salta',
            'Tolar Grande'
        ];
        $location[] = [
            'Salta',
            'Urundel'
        ];
        $location[] = [
            'Salta',
            'Vaqueros'
        ];
        $location[] = [
            'Salta',
            'Villa San Lorenzo'
        ];
        $location[] = [
            'San Juan',
            'Albardón'
        ];
        $location[] = [
            'San Juan',
            'Angaco'
        ];
        $location[] = [
            'San Juan',
            'Calingasta'
        ];
        $location[] = [
            'San Juan',
            'Capital'
        ];
        $location[] = [
            'San Juan',
            'Caucete'
        ];
        $location[] = [
            'San Juan',
            'Chimbas'
        ];
        $location[] = [
            'San Juan',
            'Iglesia'
        ];
        $location[] = [
            'San Juan',
            'Jachal'
        ];
        $location[] = [
            'San Juan',
            'Nueve de Julio'
        ];
        $location[] = [
            'San Juan',
            'Pocito'
        ];
        $location[] = [
            'San Juan',
            'Rawson'
        ];
        $location[] = [
            'San Juan',
            'Rivadavia'
        ];
        $location[] = [
            'San Juan',
            'San Juan'
        ];
        $location[] = [
            'San Juan',
            'San Martín'
        ];
        $location[] = [
            'San Juan',
            'Santa Lucía'
        ];
        $location[] = [
            'San Juan',
            'Sarmiento'
        ];
        $location[] = [
            'San Juan',
            'Ullum'
        ];
        $location[] = [
            'San Juan',
            'Valle Fértil'
        ];
        $location[] = [
            'San Juan',
            'Veinticinco de Mayo'
        ];
        $location[] = [
            'San Juan',
            'Zonda'
        ];
        $location[] = [
            'San Luis',
            'Alto Pelado'
        ];
        $location[] = [
            'San Luis',
            'Alto Pencoso'
        ];
        $location[] = [
            'San Luis',
            'Anchorena'
        ];
        $location[] = [
            'San Luis',
            'Arizona'
        ];
        $location[] = [
            'San Luis',
            'Bagual'
        ];
        $location[] = [
            'San Luis',
            'Balde'
        ];
        $location[] = [
            'San Luis',
            'Batavia'
        ];
        $location[] = [
            'San Luis',
            'Beazley'
        ];
        $location[] = [
            'San Luis',
            'Buena Esperanza'
        ];
        $location[] = [
            'San Luis',
            'Candelaria'
        ];
        $location[] = [
            'San Luis',
            'Capital'
        ];
        $location[] = [
            'San Luis',
            'Carolina'
        ];
        $location[] = [
            'San Luis',
            'Carpintería'
        ];
        $location[] = [
            'San Luis',
            'Concarán'
        ];
        $location[] = [
            'San Luis',
            'Cortaderas'
        ];
        $location[] = [
            'San Luis',
            'El Morro'
        ];
        $location[] = [
            'San Luis',
            'El Trapiche'
        ];
        $location[] = [
            'San Luis',
            'El Volcán'
        ];
        $location[] = [
            'San Luis',
            'Fortín El Patria'
        ];
        $location[] = [
            'San Luis',
            'Fortuna'
        ];
        $location[] = [
            'San Luis',
            'Fraga'
        ];
        $location[] = [
            'San Luis',
            'Juan Jorba'
        ];
        $location[] = [
            'San Luis',
            'Juan Llerena'
        ];
        $location[] = [
            'San Luis',
            'Juana Koslay'
        ];
        $location[] = [
            'San Luis',
            'Justo Daract'
        ];
        $location[] = [
            'San Luis',
            'La Calera'
        ];
        $location[] = [
            'San Luis',
            'La Florida'
        ];
        $location[] = [
            'San Luis',
            'La Punilla'
        ];
        $location[] = [
            'San Luis',
            'La Toma'
        ];
        $location[] = [
            'San Luis',
            'Lafinur'
        ];
        $location[] = [
            'San Luis',
            'Las Aguadas'
        ];
        $location[] = [
            'San Luis',
            'Las Chacras'
        ];
        $location[] = [
            'San Luis',
            'Las Lagunas'
        ];
        $location[] = [
            'San Luis',
            'Las Vertientes'
        ];
        $location[] = [
            'San Luis',
            'Lavaisse'
        ];
        $location[] = [
            'San Luis',
            'Leandro N. Alem'
        ];
        $location[] = [
            'San Luis',
            'Los Molles'
        ];
        $location[] = [
            'San Luis',
            'Luján'
        ];
        $location[] = [
            'San Luis',
            'Mercedes'
        ];
        $location[] = [
            'San Luis',
            'Merlo'
        ];
        $location[] = [
            'San Luis',
            'Naschel'
        ];
        $location[] = [
            'San Luis',
            'Navia'
        ];
        $location[] = [
            'San Luis',
            'Nogolí'
        ];
        $location[] = [
            'San Luis',
            'Nueva Galia'
        ];
        $location[] = [
            'San Luis',
            'Papagayos'
        ];
        $location[] = [
            'San Luis',
            'Paso Grande'
        ];
        $location[] = [
            'San Luis',
            'Potrero de Los Funes'
        ];
        $location[] = [
            'San Luis',
            'Quines'
        ];
        $location[] = [
            'San Luis',
            'Renca'
        ];
        $location[] = [
            'San Luis',
            'Saladillo'
        ];
        $location[] = [
            'San Luis',
            'San Francisco'
        ];
        $location[] = [
            'San Luis',
            'San Gerónimo'
        ];
        $location[] = [
            'San Luis',
            'San Martín'
        ];
        $location[] = [
            'San Luis',
            'San Pablo'
        ];
        $location[] = [
            'San Luis',
            'Santa Rosa de Conlara'
        ];
        $location[] = [
            'San Luis',
            'Talita'
        ];
        $location[] = [
            'San Luis',
            'Tilisarao'
        ];
        $location[] = [
            'San Luis',
            'Unión'
        ];
        $location[] = [
            'San Luis',
            'Villa de La Quebrada'
        ];
        $location[] = [
            'San Luis',
            'Villa de Praga'
        ];
        $location[] = [
            'San Luis',
            'Villa del Carmen'
        ];
        $location[] = [
            'San Luis',
            'Villa Gral. Roca'
        ];
        $location[] = [
            'San Luis',
            'Villa Larca'
        ];
        $location[] = [
            'San Luis',
            'Villa Mercedes'
        ];
        $location[] = [
            'San Luis',
            'Zanjitas'
        ];
        $location[] = [
            'Santa Cruz',
            'Calafate'
        ];
        $location[] = [
            'Santa Cruz',
            'Caleta Olivia'
        ];
        $location[] = [
            'Santa Cruz',
            'Cañadón Seco'
        ];
        $location[] = [
            'Santa Cruz',
            'Comandante Piedrabuena'
        ];
        $location[] = [
            'Santa Cruz',
            'El Calafate'
        ];
        $location[] = [
            'Santa Cruz',
            'El Chaltén'
        ];
        $location[] = [
            'Santa Cruz',
            'Gdor. Gregores'
        ];
        $location[] = [
            'Santa Cruz',
            'Hipólito Yrigoyen'
        ];
        $location[] = [
            'Santa Cruz',
            'Jaramillo'
        ];
        $location[] = [
            'Santa Cruz',
            'Koluel Kaike'
        ];
        $location[] = [
            'Santa Cruz',
            'Las Heras'
        ];
        $location[] = [
            'Santa Cruz',
            'Los Antiguos'
        ];
        $location[] = [
            'Santa Cruz',
            'Perito Moreno'
        ];
        $location[] = [
            'Santa Cruz',
            'Pico Truncado'
        ];
        $location[] = [
            'Santa Cruz',
            'Pto. Deseado'
        ];
        $location[] = [
            'Santa Cruz',
            'Pto. San Julián'
        ];
        $location[] = [
            'Santa Cruz',
            'Pto. Santa Cruz'
        ];
        $location[] = [
            'Santa Cruz',
            'Río Cuarto'
        ];
        $location[] = [
            'Santa Cruz',
            'Río Gallegos'
        ];
        $location[] = [
            'Santa Cruz',
            'Río Turbio'
        ];
        $location[] = [
            'Santa Cruz',
            'Tres Lagos'
        ];
        $location[] = [
            'Santa Cruz',
            'Veintiocho De Noviembre'
        ];
        $location[] = [
            'Santa Fe',
            'Aarón Castellanos'
        ];
        $location[] = [
            'Santa Fe',
            'Acebal'
        ];
        $location[] = [
            'Santa Fe',
            'Aguará Grande'
        ];
        $location[] = [
            'Santa Fe',
            'Albarellos'
        ];
        $location[] = [
            'Santa Fe',
            'Alcorta'
        ];
        $location[] = [
            'Santa Fe',
            'Aldao'
        ];
        $location[] = [
            'Santa Fe',
            'Alejandra'
        ];
        $location[] = [
            'Santa Fe',
            'Álvarez'
        ];
        $location[] = [
            'Santa Fe',
            'Ambrosetti'
        ];
        $location[] = [
            'Santa Fe',
            'Amenábar'
        ];
        $location[] = [
            'Santa Fe',
            'Angélica'
        ];
        $location[] = [
            'Santa Fe',
            'Angeloni'
        ];
        $location[] = [
            'Santa Fe',
            'Arequito'
        ];
        $location[] = [
            'Santa Fe',
            'Arminda'
        ];
        $location[] = [
            'Santa Fe',
            'Armstrong'
        ];
        $location[] = [
            'Santa Fe',
            'Arocena'
        ];
        $location[] = [
            'Santa Fe',
            'Arroyo Aguiar'
        ];
        $location[] = [
            'Santa Fe',
            'Arroyo Ceibal'
        ];
        $location[] = [
            'Santa Fe',
            'Arroyo Leyes'
        ];
        $location[] = [
            'Santa Fe',
            'Arroyo Seco'
        ];
        $location[] = [
            'Santa Fe',
            'Arrufó'
        ];
        $location[] = [
            'Santa Fe',
            'Arteaga'
        ];
        $location[] = [
            'Santa Fe',
            'Ataliva'
        ];
        $location[] = [
            'Santa Fe',
            'Aurelia'
        ];
        $location[] = [
            'Santa Fe',
            'Avellaneda'
        ];
        $location[] = [
            'Santa Fe',
            'Barrancas'
        ];
        $location[] = [
            'Santa Fe',
            'Bauer Y Sigel'
        ];
        $location[] = [
            'Santa Fe',
            'Bella Italia'
        ];
        $location[] = [
            'Santa Fe',
            'Berabevú'
        ];
        $location[] = [
            'Santa Fe',
            'Berna'
        ];
        $location[] = [
            'Santa Fe',
            'Bernardo de Irigoyen'
        ];
        $location[] = [
            'Santa Fe',
            'Bigand'
        ];
        $location[] = [
            'Santa Fe',
            'Bombal'
        ];
        $location[] = [
            'Santa Fe',
            'Bouquet'
        ];
        $location[] = [
            'Santa Fe',
            'Bustinza'
        ];
        $location[] = [
            'Santa Fe',
            'Cabal'
        ];
        $location[] = [
            'Santa Fe',
            'Cacique Ariacaiquin'
        ];
        $location[] = [
            'Santa Fe',
            'Cafferata'
        ];
        $location[] = [
            'Santa Fe',
            'Calchaquí'
        ];
        $location[] = [
            'Santa Fe',
            'Campo Andino'
        ];
        $location[] = [
            'Santa Fe',
            'Campo Piaggio'
        ];
        $location[] = [
            'Santa Fe',
            'Cañada de Gómez'
        ];
        $location[] = [
            'Santa Fe',
            'Cañada del Ucle'
        ];
        $location[] = [
            'Santa Fe',
            'Cañada Rica'
        ];
        $location[] = [
            'Santa Fe',
            'Cañada Rosquín'
        ];
        $location[] = [
            'Santa Fe',
            'Candioti'
        ];
        $location[] = [
            'Santa Fe',
            'Capital'
        ];
        $location[] = [
            'Santa Fe',
            'Capitán Bermúdez'
        ];
        $location[] = [
            'Santa Fe',
            'Capivara'
        ];
        $location[] = [
            'Santa Fe',
            'Carcarañá'
        ];
        $location[] = [
            'Santa Fe',
            'Carlos Pellegrini'
        ];
        $location[] = [
            'Santa Fe',
            'Carmen'
        ];
        $location[] = [
            'Santa Fe',
            'Carmen Del Sauce'
        ];
        $location[] = [
            'Santa Fe',
            'Carreras'
        ];
        $location[] = [
            'Santa Fe',
            'Carrizales'
        ];
        $location[] = [
            'Santa Fe',
            'Casalegno'
        ];
        $location[] = [
            'Santa Fe',
            'Casas'
        ];
        $location[] = [
            'Santa Fe',
            'Casilda'
        ];
        $location[] = [
            'Santa Fe',
            'Castelar'
        ];
        $location[] = [
            'Santa Fe',
            'Castellanos'
        ];
        $location[] = [
            'Santa Fe',
            'Cayastá'
        ];
        $location[] = [
            'Santa Fe',
            'Cayastacito'
        ];
        $location[] = [
            'Santa Fe',
            'Centeno'
        ];
        $location[] = [
            'Santa Fe',
            'Cepeda'
        ];
        $location[] = [
            'Santa Fe',
            'Ceres'
        ];
        $location[] = [
            'Santa Fe',
            'Chabás'
        ];
        $location[] = [
            'Santa Fe',
            'Chañar Ladeado'
        ];
        $location[] = [
            'Santa Fe',
            'Chapuy'
        ];
        $location[] = [
            'Santa Fe',
            'Chovet'
        ];
        $location[] = [
            'Santa Fe',
            'Christophersen'
        ];
        $location[] = [
            'Santa Fe',
            'Classon'
        ];
        $location[] = [
            'Santa Fe',
            'Cnel. Arnold'
        ];
        $location[] = [
            'Santa Fe',
            'Cnel. Bogado'
        ];
        $location[] = [
            'Santa Fe',
            'Cnel. Dominguez'
        ];
        $location[] = [
            'Santa Fe',
            'Cnel. Fraga'
        ];
        $location[] = [
            'Santa Fe',
            'Col. Aldao'
        ];
        $location[] = [
            'Santa Fe',
            'Col. Ana'
        ];
        $location[] = [
            'Santa Fe',
            'Col. Belgrano'
        ];
        $location[] = [
            'Santa Fe',
            'Col. Bicha'
        ];
        $location[] = [
            'Santa Fe',
            'Col. Bigand'
        ];
        $location[] = [
            'Santa Fe',
            'Col. Bossi'
        ];
        $location[] = [
            'Santa Fe',
            'Col. Cavour'
        ];
        $location[] = [
            'Santa Fe',
            'Col. Cello'
        ];
        $location[] = [
            'Santa Fe',
            'Col. Dolores'
        ];
        $location[] = [
            'Santa Fe',
            'Col. Dos Rosas'
        ];
        $location[] = [
            'Santa Fe',
            'Col. Durán'
        ];
        $location[] = [
            'Santa Fe',
            'Col. Iturraspe'
        ];
        $location[] = [
            'Santa Fe',
            'Col. Margarita'
        ];
        $location[] = [
            'Santa Fe',
            'Col. Mascias'
        ];
        $location[] = [
            'Santa Fe',
            'Col. Raquel'
        ];
        $location[] = [
            'Santa Fe',
            'Col. Rosa'
        ];
        $location[] = [
            'Santa Fe',
            'Col. San José'
        ];
        $location[] = [
            'Santa Fe',
            'Constanza'
        ];
        $location[] = [
            'Santa Fe',
            'Coronda'
        ];
        $location[] = [
            'Santa Fe',
            'Correa'
        ];
        $location[] = [
            'Santa Fe',
            'Crispi'
        ];
        $location[] = [
            'Santa Fe',
            'Cululú'
        ];
        $location[] = [
            'Santa Fe',
            'Curupayti'
        ];
        $location[] = [
            'Santa Fe',
            'Desvio Arijón'
        ];
        $location[] = [
            'Santa Fe',
            'Diaz'
        ];
        $location[] = [
            'Santa Fe',
            'Diego de Alvear'
        ];
        $location[] = [
            'Santa Fe',
            'Egusquiza'
        ];
        $location[] = [
            'Santa Fe',
            'El Arazá'
        ];
        $location[] = [
            'Santa Fe',
            'El Rabón'
        ];
        $location[] = [
            'Santa Fe',
            'El Sombrerito'
        ];
        $location[] = [
            'Santa Fe',
            'El Trébol'
        ];
        $location[] = [
            'Santa Fe',
            'Elisa'
        ];
        $location[] = [
            'Santa Fe',
            'Elortondo'
        ];
        $location[] = [
            'Santa Fe',
            'Emilia'
        ];
        $location[] = [
            'Santa Fe',
            'Empalme San Carlos'
        ];
        $location[] = [
            'Santa Fe',
            'Empalme Villa Constitucion'
        ];
        $location[] = [
            'Santa Fe',
            'Esmeralda'
        ];
        $location[] = [
            'Santa Fe',
            'Esperanza'
        ];
        $location[] = [
            'Santa Fe',
            'Estación Alvear'
        ];
        $location[] = [
            'Santa Fe',
            'Estacion Clucellas'
        ];
        $location[] = [
            'Santa Fe',
            'Esteban Rams'
        ];
        $location[] = [
            'Santa Fe',
            'Esther'
        ];
        $location[] = [
            'Santa Fe',
            'Esustolia'
        ];
        $location[] = [
            'Santa Fe',
            'Eusebia'
        ];
        $location[] = [
            'Santa Fe',
            'Felicia'
        ];
        $location[] = [
            'Santa Fe',
            'Fidela'
        ];
        $location[] = [
            'Santa Fe',
            'Fighiera'
        ];
        $location[] = [
            'Santa Fe',
            'Firmat'
        ];
        $location[] = [
            'Santa Fe',
            'Florencia'
        ];
        $location[] = [
            'Santa Fe',
            'Fortín Olmos'
        ];
        $location[] = [
            'Santa Fe',
            'Franck'
        ];
        $location[] = [
            'Santa Fe',
            'Fray Luis Beltrán'
        ];
        $location[] = [
            'Santa Fe',
            'Frontera'
        ];
        $location[] = [
            'Santa Fe',
            'Fuentes'
        ];
        $location[] = [
            'Santa Fe',
            'Funes'
        ];
        $location[] = [
            'Santa Fe',
            'Gaboto'
        ];
        $location[] = [
            'Santa Fe',
            'Galisteo'
        ];
        $location[] = [
            'Santa Fe',
            'Gálvez'
        ];
        $location[] = [
            'Santa Fe',
            'Garabalto'
        ];
        $location[] = [
            'Santa Fe',
            'Garibaldi'
        ];
        $location[] = [
            'Santa Fe',
            'Gato Colorado'
        ];
        $location[] = [
            'Santa Fe',
            'Gdor. Crespo'
        ];
        $location[] = [
            'Santa Fe',
            'Gessler'
        ];
        $location[] = [
            'Santa Fe',
            'Godoy'
        ];
        $location[] = [
            'Santa Fe',
            'Golondrina'
        ];
        $location[] = [
            'Santa Fe',
            'Gral. Gelly'
        ];
        $location[] = [
            'Santa Fe',
            'Gral. Lagos'
        ];
        $location[] = [
            'Santa Fe',
            'Granadero Baigorria'
        ];
        $location[] = [
            'Santa Fe',
            'Gregoria Perez De Denis'
        ];
        $location[] = [
            'Santa Fe',
            'Grutly'
        ];
        $location[] = [
            'Santa Fe',
            'Guadalupe N.'
        ];
        $location[] = [
            'Santa Fe',
            'Gödeken'
        ];
        $location[] = [
            'Santa Fe',
            'Helvecia'
        ];
        $location[] = [
            'Santa Fe',
            'Hersilia'
        ];
        $location[] = [
            'Santa Fe',
            'Hipatía'
        ];
        $location[] = [
            'Santa Fe',
            'Huanqueros'
        ];
        $location[] = [
            'Santa Fe',
            'Hugentobler'
        ];
        $location[] = [
            'Santa Fe',
            'Hughes'
        ];
        $location[] = [
            'Santa Fe',
            'Humberto 1º'
        ];
        $location[] = [
            'Santa Fe',
            'Humboldt'
        ];
        $location[] = [
            'Santa Fe',
            'Ibarlucea'
        ];
        $location[] = [
            'Santa Fe',
            'Ing. Chanourdie'
        ];
        $location[] = [
            'Santa Fe',
            'Intiyaco'
        ];
        $location[] = [
            'Santa Fe',
            'Ituzaingó'
        ];
        $location[] = [
            'Santa Fe',
            'Jacinto L. Aráuz'
        ];
        $location[] = [
            'Santa Fe',
            'Josefina'
        ];
        $location[] = [
            'Santa Fe',
            'Juan B. Molina'
        ];
        $location[] = [
            'Santa Fe',
            'Juan de Garay'
        ];
        $location[] = [
            'Santa Fe',
            'Juncal'
        ];
        $location[] = [
            'Santa Fe',
            'La Brava'
        ];
        $location[] = [
            'Santa Fe',
            'La Cabral'
        ];
        $location[] = [
            'Santa Fe',
            'La Camila'
        ];
        $location[] = [
            'Santa Fe',
            'La Chispa'
        ];
        $location[] = [
            'Santa Fe',
            'La Clara'
        ];
        $location[] = [
            'Santa Fe',
            'La Criolla'
        ];
        $location[] = [
            'Santa Fe',
            'La Gallareta'
        ];
        $location[] = [
            'Santa Fe',
            'La Lucila'
        ];
        $location[] = [
            'Santa Fe',
            'La Pelada'
        ];
        $location[] = [
            'Santa Fe',
            'La Penca'
        ];
        $location[] = [
            'Santa Fe',
            'La Rubia'
        ];
        $location[] = [
            'Santa Fe',
            'La Sarita'
        ];
        $location[] = [
            'Santa Fe',
            'La Vanguardia'
        ];
        $location[] = [
            'Santa Fe',
            'Labordeboy'
        ];
        $location[] = [
            'Santa Fe',
            'Laguna Paiva'
        ];
        $location[] = [
            'Santa Fe',
            'Landeta'
        ];
        $location[] = [
            'Santa Fe',
            'Lanteri'
        ];
        $location[] = [
            'Santa Fe',
            'Larrechea'
        ];
        $location[] = [
            'Santa Fe',
            'Las Avispas'
        ];
        $location[] = [
            'Santa Fe',
            'Las Bandurrias'
        ];
        $location[] = [
            'Santa Fe',
            'Las Garzas'
        ];
        $location[] = [
            'Santa Fe',
            'Las Palmeras'
        ];
        $location[] = [
            'Santa Fe',
            'Las Parejas'
        ];
        $location[] = [
            'Santa Fe',
            'Las Petacas'
        ];
        $location[] = [
            'Santa Fe',
            'Las Rosas'
        ];
        $location[] = [
            'Santa Fe',
            'Las Toscas'
        ];
        $location[] = [
            'Santa Fe',
            'Las Tunas'
        ];
        $location[] = [
            'Santa Fe',
            'Lazzarino'
        ];
        $location[] = [
            'Santa Fe',
            'Lehmann'
        ];
        $location[] = [
            'Santa Fe',
            'Llambi Campbell'
        ];
        $location[] = [
            'Santa Fe',
            'Logroño'
        ];
        $location[] = [
            'Santa Fe',
            'Loma Alta'
        ];
        $location[] = [
            'Santa Fe',
            'López'
        ];
        $location[] = [
            'Santa Fe',
            'Los Amores'
        ];
        $location[] = [
            'Santa Fe',
            'Los Cardos'
        ];
        $location[] = [
            'Santa Fe',
            'Los Laureles'
        ];
        $location[] = [
            'Santa Fe',
            'Los Molinos'
        ];
        $location[] = [
            'Santa Fe',
            'Los Quirquinchos'
        ];
        $location[] = [
            'Santa Fe',
            'Lucio V. Lopez'
        ];
        $location[] = [
            'Santa Fe',
            'Luis Palacios'
        ];
        $location[] = [
            'Santa Fe',
            'Ma. Juana'
        ];
        $location[] = [
            'Santa Fe',
            'Ma. Luisa'
        ];
        $location[] = [
            'Santa Fe',
            'Ma. Susana'
        ];
        $location[] = [
            'Santa Fe',
            'Ma. Teresa'
        ];
        $location[] = [
            'Santa Fe',
            'Maciel'
        ];
        $location[] = [
            'Santa Fe',
            'Maggiolo'
        ];
        $location[] = [
            'Santa Fe',
            'Malabrigo'
        ];
        $location[] = [
            'Santa Fe',
            'Marcelino Escalada'
        ];
        $location[] = [
            'Santa Fe',
            'Margarita'
        ];
        $location[] = [
            'Santa Fe',
            'Matilde'
        ];
        $location[] = [
            'Santa Fe',
            'Mauá'
        ];
        $location[] = [
            'Santa Fe',
            'Máximo Paz'
        ];
        $location[] = [
            'Santa Fe',
            'Melincué'
        ];
        $location[] = [
            'Santa Fe',
            'Miguel Torres'
        ];
        $location[] = [
            'Santa Fe',
            'Moisés Ville'
        ];
        $location[] = [
            'Santa Fe',
            'Monigotes'
        ];
        $location[] = [
            'Santa Fe',
            'Monje'
        ];
        $location[] = [
            'Santa Fe',
            'Monte Obscuridad'
        ];
        $location[] = [
            'Santa Fe',
            'Monte Vera'
        ];
        $location[] = [
            'Santa Fe',
            'Montefiore'
        ];
        $location[] = [
            'Santa Fe',
            'Montes de Oca'
        ];
        $location[] = [
            'Santa Fe',
            'Murphy'
        ];
        $location[] = [
            'Santa Fe',
            'Ñanducita'
        ];
        $location[] = [
            'Santa Fe',
            'Naré'
        ];
        $location[] = [
            'Santa Fe',
            'Nelson'
        ];
        $location[] = [
            'Santa Fe',
            'Nicanor E. Molinas'
        ];
        $location[] = [
            'Santa Fe',
            'Nuevo Torino'
        ];
        $location[] = [
            'Santa Fe',
            'Oliveros'
        ];
        $location[] = [
            'Santa Fe',
            'Palacios'
        ];
        $location[] = [
            'Santa Fe',
            'Pavón'
        ];
        $location[] = [
            'Santa Fe',
            'Pavón Arriba'
        ];
        $location[] = [
            'Santa Fe',
            'Pedro Gómez Cello'
        ];
        $location[] = [
            'Santa Fe',
            'Pérez'
        ];
        $location[] = [
            'Santa Fe',
            'Peyrano'
        ];
        $location[] = [
            'Santa Fe',
            'Piamonte'
        ];
        $location[] = [
            'Santa Fe',
            'Pilar'
        ];
        $location[] = [
            'Santa Fe',
            'Piñero'
        ];
        $location[] = [
            'Santa Fe',
            'Plaza Clucellas'
        ];
        $location[] = [
            'Santa Fe',
            'Portugalete'
        ];
        $location[] = [
            'Santa Fe',
            'Pozo Borrado'
        ];
        $location[] = [
            'Santa Fe',
            'Progreso'
        ];
        $location[] = [
            'Santa Fe',
            'Providencia'
        ];
        $location[] = [
            'Santa Fe',
            'Pte. Roca'
        ];
        $location[] = [
            'Santa Fe',
            'Pueblo Andino'
        ];
        $location[] = [
            'Santa Fe',
            'Pueblo Esther'
        ];
        $location[] = [
            'Santa Fe',
            'Pueblo Gral. San Martín'
        ];
        $location[] = [
            'Santa Fe',
            'Pueblo Irigoyen'
        ];
        $location[] = [
            'Santa Fe',
            'Pueblo Marini'
        ];
        $location[] = [
            'Santa Fe',
            'Pueblo Muñoz'
        ];
        $location[] = [
            'Santa Fe',
            'Pueblo Uranga'
        ];
        $location[] = [
            'Santa Fe',
            'Pujato'
        ];
        $location[] = [
            'Santa Fe',
            'Pujato N.'
        ];
        $location[] = [
            'Santa Fe',
            'Rafaela'
        ];
        $location[] = [
            'Santa Fe',
            'Ramayón'
        ];
        $location[] = [
            'Santa Fe',
            'Ramona'
        ];
        $location[] = [
            'Santa Fe',
            'Reconquista'
        ];
        $location[] = [
            'Santa Fe',
            'Recreo'
        ];
        $location[] = [
            'Santa Fe',
            'Ricardone'
        ];
        $location[] = [
            'Santa Fe',
            'Rivadavia'
        ];
        $location[] = [
            'Santa Fe',
            'Roldán'
        ];
        $location[] = [
            'Santa Fe',
            'Romang'
        ];
        $location[] = [
            'Santa Fe',
            'Rosario'
        ];
        $location[] = [
            'Santa Fe',
            'Rueda'
        ];
        $location[] = [
            'Santa Fe',
            'Rufino'
        ];
        $location[] = [
            'Santa Fe',
            'Sa Pereira'
        ];
        $location[] = [
            'Santa Fe',
            'Saguier'
        ];
        $location[] = [
            'Santa Fe',
            'Saladero M. Cabal'
        ];
        $location[] = [
            'Santa Fe',
            'Salto Grande'
        ];
        $location[] = [
            'Santa Fe',
            'San Agustín'
        ];
        $location[] = [
            'Santa Fe',
            'San Antonio de Obligado'
        ];
        $location[] = [
            'Santa Fe',
            'San Bernardo (N.J.)'
        ];
        $location[] = [
            'Santa Fe',
            'San Bernardo (S.J.)'
        ];
        $location[] = [
            'Santa Fe',
            'San Carlos Centro'
        ];
        $location[] = [
            'Santa Fe',
            'San Carlos N.'
        ];
        $location[] = [
            'Santa Fe',
            'San Carlos S.'
        ];
        $location[] = [
            'Santa Fe',
            'San Cristóbal'
        ];
        $location[] = [
            'Santa Fe',
            'San Eduardo'
        ];
        $location[] = [
            'Santa Fe',
            'San Eugenio'
        ];
        $location[] = [
            'Santa Fe',
            'San Fabián'
        ];
        $location[] = [
            'Santa Fe',
            'San Fco. de Santa Fé'
        ];
        $location[] = [
            'Santa Fe',
            'San Genaro'
        ];
        $location[] = [
            'Santa Fe',
            'San Genaro N.'
        ];
        $location[] = [
            'Santa Fe',
            'San Gregorio'
        ];
        $location[] = [
            'Santa Fe',
            'San Guillermo'
        ];
        $location[] = [
            'Santa Fe',
            'San Javier'
        ];
        $location[] = [
            'Santa Fe',
            'San Jerónimo del Sauce'
        ];
        $location[] = [
            'Santa Fe',
            'San Jerónimo N.'
        ];
        $location[] = [
            'Santa Fe',
            'San Jerónimo S.'
        ];
        $location[] = [
            'Santa Fe',
            'San Jorge'
        ];
        $location[] = [
            'Santa Fe',
            'San José de La Esquina'
        ];
        $location[] = [
            'Santa Fe',
            'San José del Rincón'
        ];
        $location[] = [
            'Santa Fe',
            'San Justo'
        ];
        $location[] = [
            'Santa Fe',
            'San Lorenzo'
        ];
        $location[] = [
            'Santa Fe',
            'San Mariano'
        ];
        $location[] = [
            'Santa Fe',
            'San Martín de Las Escobas'
        ];
        $location[] = [
            'Santa Fe',
            'San Martín N.'
        ];
        $location[] = [
            'Santa Fe',
            'San Vicente'
        ];
        $location[] = [
            'Santa Fe',
            'Sancti Spititu'
        ];
        $location[] = [
            'Santa Fe',
            'Sanford'
        ];
        $location[] = [
            'Santa Fe',
            'Santo Domingo'
        ];
        $location[] = [
            'Santa Fe',
            'Santo Tomé'
        ];
        $location[] = [
            'Santa Fe',
            'Santurce'
        ];
        $location[] = [
            'Santa Fe',
            'Sargento Cabral'
        ];
        $location[] = [
            'Santa Fe',
            'Sarmiento'
        ];
        $location[] = [
            'Santa Fe',
            'Sastre'
        ];
        $location[] = [
            'Santa Fe',
            'Sauce Viejo'
        ];
        $location[] = [
            'Santa Fe',
            'Serodino'
        ];
        $location[] = [
            'Santa Fe',
            'Silva'
        ];
        $location[] = [
            'Santa Fe',
            'Soldini'
        ];
        $location[] = [
            'Santa Fe',
            'Soledad'
        ];
        $location[] = [
            'Santa Fe',
            'Soutomayor'
        ];
        $location[] = [
            'Santa Fe',
            'Sta. Clara de Buena Vista'
        ];
        $location[] = [
            'Santa Fe',
            'Sta. Clara de Saguier'
        ];
        $location[] = [
            'Santa Fe',
            'Sta. Isabel'
        ];
        $location[] = [
            'Santa Fe',
            'Sta. Margarita'
        ];
        $location[] = [
            'Santa Fe',
            'Sta. Maria Centro'
        ];
        $location[] = [
            'Santa Fe',
            'Sta. María N.'
        ];
        $location[] = [
            'Santa Fe',
            'Sta. Rosa'
        ];
        $location[] = [
            'Santa Fe',
            'Sta. Teresa'
        ];
        $location[] = [
            'Santa Fe',
            'Suardi'
        ];
        $location[] = [
            'Santa Fe',
            'Sunchales'
        ];
        $location[] = [
            'Santa Fe',
            'Susana'
        ];
        $location[] = [
            'Santa Fe',
            'Tacuarendí'
        ];
        $location[] = [
            'Santa Fe',
            'Tacural'
        ];
        $location[] = [
            'Santa Fe',
            'Tartagal'
        ];
        $location[] = [
            'Santa Fe',
            'Teodelina'
        ];
        $location[] = [
            'Santa Fe',
            'Theobald'
        ];
        $location[] = [
            'Santa Fe',
            'Timbúes'
        ];
        $location[] = [
            'Santa Fe',
            'Toba'
        ];
        $location[] = [
            'Santa Fe',
            'Tortugas'
        ];
        $location[] = [
            'Santa Fe',
            'Tostado'
        ];
        $location[] = [
            'Santa Fe',
            'Totoras'
        ];
        $location[] = [
            'Santa Fe',
            'Traill'
        ];
        $location[] = [
            'Santa Fe',
            'Venado Tuerto'
        ];
        $location[] = [
            'Santa Fe',
            'Vera'
        ];
        $location[] = [
            'Santa Fe',
            'Vera y Pintado'
        ];
        $location[] = [
            'Santa Fe',
            'Videla'
        ];
        $location[] = [
            'Santa Fe',
            'Vila'
        ];
        $location[] = [
            'Santa Fe',
            'Villa Amelia'
        ];
        $location[] = [
            'Santa Fe',
            'Villa Ana'
        ];
        $location[] = [
            'Santa Fe',
            'Villa Cañas'
        ];
        $location[] = [
            'Santa Fe',
            'Villa Constitución'
        ];
        $location[] = [
            'Santa Fe',
            'Villa Eloísa'
        ];
        $location[] = [
            'Santa Fe',
            'Villa Gdor. Gálvez'
        ];
        $location[] = [
            'Santa Fe',
            'Villa Guillermina'
        ];
        $location[] = [
            'Santa Fe',
            'Villa Minetti'
        ];
        $location[] = [
            'Santa Fe',
            'Villa Mugueta'
        ];
        $location[] = [
            'Santa Fe',
            'Villa Ocampo'
        ];
        $location[] = [
            'Santa Fe',
            'Villa San José'
        ];
        $location[] = [
            'Santa Fe',
            'Villa Saralegui'
        ];
        $location[] = [
            'Santa Fe',
            'Villa Trinidad'
        ];
        $location[] = [
            'Santa Fe',
            'Villada'
        ];
        $location[] = [
            'Santa Fe',
            'Virginia'
        ];
        $location[] = [
            'Santa Fe',
            'Wheelwright'
        ];
        $location[] = [
            'Santa Fe',
            'Zavalla'
        ];
        $location[] = [
            'Santa Fe',
            'Zenón Pereira'
        ];
        $location[] = [
            'Santiago del Estero',
            'Añatuya'
        ];
        $location[] = [
            'Santiago del Estero',
            'Árraga'
        ];
        $location[] = [
            'Santiago del Estero',
            'Bandera'
        ];
        $location[] = [
            'Santiago del Estero',
            'Bandera Bajada'
        ];
        $location[] = [
            'Santiago del Estero',
            'Beltrán'
        ];
        $location[] = [
            'Santiago del Estero',
            'Brea Pozo'
        ];
        $location[] = [
            'Santiago del Estero',
            'Campo Gallo'
        ];
        $location[] = [
            'Santiago del Estero',
            'Capital'
        ];
        $location[] = [
            'Santiago del Estero',
            'Chilca Juliana'
        ];
        $location[] = [
            'Santiago del Estero',
            'Choya'
        ];
        $location[] = [
            'Santiago del Estero',
            'Clodomira'
        ];
        $location[] = [
            'Santiago del Estero',
            'Col. Alpina'
        ];
        $location[] = [
            'Santiago del Estero',
            'Col. Dora'
        ];
        $location[] = [
            'Santiago del Estero',
            'Col. El Simbolar Robles'
        ];
        $location[] = [
            'Santiago del Estero',
            'El Bobadal'
        ];
        $location[] = [
            'Santiago del Estero',
            'El Charco'
        ];
        $location[] = [
            'Santiago del Estero',
            'El Mojón'
        ];
        $location[] = [
            'Santiago del Estero',
            'Estación Atamisqui'
        ];
        $location[] = [
            'Santiago del Estero',
            'Estación Simbolar'
        ];
        $location[] = [
            'Santiago del Estero',
            'Fernández'
        ];
        $location[] = [
            'Santiago del Estero',
            'Fortín Inca'
        ];
        $location[] = [
            'Santiago del Estero',
            'Frías'
        ];
        $location[] = [
            'Santiago del Estero',
            'Garza'
        ];
        $location[] = [
            'Santiago del Estero',
            'Gramilla'
        ];
        $location[] = [
            'Santiago del Estero',
            'Guardia Escolta'
        ];
        $location[] = [
            'Santiago del Estero',
            'Herrera'
        ];
        $location[] = [
            'Santiago del Estero',
            'Icaño'
        ];
        $location[] = [
            'Santiago del Estero',
            'Ing. Forres'
        ];
        $location[] = [
            'Santiago del Estero',
            'La Banda'
        ];
        $location[] = [
            'Santiago del Estero',
            'La Cañada'
        ];
        $location[] = [
            'Santiago del Estero',
            'Laprida'
        ];
        $location[] = [
            'Santiago del Estero',
            'Lavalle'
        ];
        $location[] = [
            'Santiago del Estero',
            'Loreto'
        ];
        $location[] = [
            'Santiago del Estero',
            'Los Juríes'
        ];
        $location[] = [
            'Santiago del Estero',
            'Los Núñez'
        ];
        $location[] = [
            'Santiago del Estero',
            'Los Pirpintos'
        ];
        $location[] = [
            'Santiago del Estero',
            'Los Quiroga'
        ];
        $location[] = [
            'Santiago del Estero',
            'Los Telares'
        ];
        $location[] = [
            'Santiago del Estero',
            'Lugones'
        ];
        $location[] = [
            'Santiago del Estero',
            'Malbrán'
        ];
        $location[] = [
            'Santiago del Estero',
            'Matara'
        ];
        $location[] = [
            'Santiago del Estero',
            'Medellín'
        ];
        $location[] = [
            'Santiago del Estero',
            'Monte Quemado'
        ];
        $location[] = [
            'Santiago del Estero',
            'Nueva Esperanza'
        ];
        $location[] = [
            'Santiago del Estero',
            'Nueva Francia'
        ];
        $location[] = [
            'Santiago del Estero',
            'Palo Negro'
        ];
        $location[] = [
            'Santiago del Estero',
            'Pampa de Los Guanacos'
        ];
        $location[] = [
            'Santiago del Estero',
            'Pinto'
        ];
        $location[] = [
            'Santiago del Estero',
            'Pozo Hondo'
        ];
        $location[] = [
            'Santiago del Estero',
            'Quimilí'
        ];
        $location[] = [
            'Santiago del Estero',
            'Real Sayana'
        ];
        $location[] = [
            'Santiago del Estero',
            'Sachayoj'
        ];
        $location[] = [
            'Santiago del Estero',
            'San Pedro de Guasayán'
        ];
        $location[] = [
            'Santiago del Estero',
            'Selva'
        ];
        $location[] = [
            'Santiago del Estero',
            'Sol de Julio'
        ];
        $location[] = [
            'Santiago del Estero',
            'Sumampa'
        ];
        $location[] = [
            'Santiago del Estero',
            'Suncho Corral'
        ];
        $location[] = [
            'Santiago del Estero',
            'Taboada'
        ];
        $location[] = [
            'Santiago del Estero',
            'Tapso'
        ];
        $location[] = [
            'Santiago del Estero',
            'Termas de Rio Hondo'
        ];
        $location[] = [
            'Santiago del Estero',
            'Tintina'
        ];
        $location[] = [
            'Santiago del Estero',
            'Tomas Young'
        ];
        $location[] = [
            'Santiago del Estero',
            'Vilelas'
        ];
        $location[] = [
            'Santiago del Estero',
            'Villa Atamisqui'
        ];
        $location[] = [
            'Santiago del Estero',
            'Villa La Punta'
        ];
        $location[] = [
            'Santiago del Estero',
            'Villa Ojo de Agua'
        ];
        $location[] = [
            'Santiago del Estero',
            'Villa Río Hondo'
        ];
        $location[] = [
            'Santiago del Estero',
            'Villa Salavina'
        ];
        $location[] = [
            'Santiago del Estero',
            'Villa Unión'
        ];
        $location[] = [
            'Santiago del Estero',
            'Vilmer'
        ];
        $location[] = [
            'Santiago del Estero',
            'Weisburd'
        ];
        $location[] = [
            'Tierra del Fuego',
            'Río Grande'
        ];
        $location[] = [
            'Tierra del Fuego',
            'Tolhuin'
        ];
        $location[] = [
            'Tierra del Fuego',
            'Ushuaia'
        ];
        $location[] = [
            'Tucumán',
            'Acheral'
        ];
        $location[] = [
            'Tucumán',
            'Agua Dulce'
        ];
        $location[] = [
            'Tucumán',
            'Aguilares'
        ];
        $location[] = [
            'Tucumán',
            'Alderetes'
        ];
        $location[] = [
            'Tucumán',
            'Alpachiri'
        ];
        $location[] = [
            'Tucumán',
            'Alto Verde'
        ];
        $location[] = [
            'Tucumán',
            'Amaicha del Valle'
        ];
        $location[] = [
            'Tucumán',
            'Amberes'
        ];
        $location[] = [
            'Tucumán',
            'Ancajuli'
        ];
        $location[] = [
            'Tucumán',
            'Arcadia'
        ];
        $location[] = [
            'Tucumán',
            'Atahona'
        ];
        $location[] = [
            'Tucumán',
            'Banda del Río Sali'
        ];
        $location[] = [
            'Tucumán',
            'Bella Vista'
        ];
        $location[] = [
            'Tucumán',
            'Buena Vista'
        ];
        $location[] = [
            'Tucumán',
            'Burruyacú'
        ];
        $location[] = [
            'Tucumán',
            'Capitán Cáceres'
        ];
        $location[] = [
            'Tucumán',
            'Cevil Redondo'
        ];
        $location[] = [
            'Tucumán',
            'Choromoro'
        ];
        $location[] = [
            'Tucumán',
            'Ciudacita'
        ];
        $location[] = [
            'Tucumán',
            'Colalao del Valle'
        ];
        $location[] = [
            'Tucumán',
            'Colombres'
        ];
        $location[] = [
            'Tucumán',
            'Concepción'
        ];
        $location[] = [
            'Tucumán',
            'Delfín Gallo'
        ];
        $location[] = [
            'Tucumán',
            'El Bracho'
        ];
        $location[] = [
            'Tucumán',
            'El Cadillal'
        ];
        $location[] = [
            'Tucumán',
            'El Cercado'
        ];
        $location[] = [
            'Tucumán',
            'El Chañar'
        ];
        $location[] = [
            'Tucumán',
            'El Manantial'
        ];
        $location[] = [
            'Tucumán',
            'El Mojón'
        ];
        $location[] = [
            'Tucumán',
            'El Mollar'
        ];
        $location[] = [
            'Tucumán',
            'El Naranjito'
        ];
        $location[] = [
            'Tucumán',
            'El Naranjo'
        ];
        $location[] = [
            'Tucumán',
            'El Polear'
        ];
        $location[] = [
            'Tucumán',
            'El Puestito'
        ];
        $location[] = [
            'Tucumán',
            'El Sacrificio'
        ];
        $location[] = [
            'Tucumán',
            'El Timbó'
        ];
        $location[] = [
            'Tucumán',
            'Escaba'
        ];
        $location[] = [
            'Tucumán',
            'Esquina'
        ];
        $location[] = [
            'Tucumán',
            'Estación Aráoz'
        ];
        $location[] = [
            'Tucumán',
            'Famaillá'
        ];
        $location[] = [
            'Tucumán',
            'Gastone'
        ];
        $location[] = [
            'Tucumán',
            'Gdor. Garmendia'
        ];
        $location[] = [
            'Tucumán',
            'Gdor. Piedrabuena'
        ];
        $location[] = [
            'Tucumán',
            'Graneros'
        ];
        $location[] = [
            'Tucumán',
            'Huasa Pampa'
        ];
        $location[] = [
            'Tucumán',
            'J. B. Alberdi'
        ];
        $location[] = [
            'Tucumán',
            'La Cocha'
        ];
        $location[] = [
            'Tucumán',
            'La Esperanza'
        ];
        $location[] = [
            'Tucumán',
            'La Florida'
        ];
        $location[] = [
            'Tucumán',
            'La Ramada'
        ];
        $location[] = [
            'Tucumán',
            'La Trinidad'
        ];
        $location[] = [
            'Tucumán',
            'Lamadrid'
        ];
        $location[] = [
            'Tucumán',
            'Las Cejas'
        ];
        $location[] = [
            'Tucumán',
            'Las Talas'
        ];
        $location[] = [
            'Tucumán',
            'Las Talitas'
        ];
        $location[] = [
            'Tucumán',
            'Los Bulacio'
        ];
        $location[] = [
            'Tucumán',
            'Los Gómez'
        ];
        $location[] = [
            'Tucumán',
            'Los Nogales'
        ];
        $location[] = [
            'Tucumán',
            'Los Pereyra'
        ];
        $location[] = [
            'Tucumán',
            'Los Pérez'
        ];
        $location[] = [
            'Tucumán',
            'Los Puestos'
        ];
        $location[] = [
            'Tucumán',
            'Los Ralos'
        ];
        $location[] = [
            'Tucumán',
            'Los Sarmientos'
        ];
        $location[] = [
            'Tucumán',
            'Los Sosa'
        ];
        $location[] = [
            'Tucumán',
            'Lules'
        ];
        $location[] = [
            'Tucumán',
            'M. García Fernández'
        ];
        $location[] = [
            'Tucumán',
            'Manuela Pedraza'
        ];
        $location[] = [
            'Tucumán',
            'Medinas'
        ];
        $location[] = [
            'Tucumán',
            'Monte Bello'
        ];
        $location[] = [
            'Tucumán',
            'Monteagudo'
        ];
        $location[] = [
            'Tucumán',
            'Monteros'
        ];
        $location[] = [
            'Tucumán',
            'Padre Monti'
        ];
        $location[] = [
            'Tucumán',
            'Pampa Mayo'
        ];
        $location[] = [
            'Tucumán',
            'Quilmes'
        ];
        $location[] = [
            'Tucumán',
            'Raco'
        ];
        $location[] = [
            'Tucumán',
            'Ranchillos'
        ];
        $location[] = [
            'Tucumán',
            'Río Chico'
        ];
        $location[] = [
            'Tucumán',
            'Río Colorado'
        ];
        $location[] = [
            'Tucumán',
            'Río Seco'
        ];
        $location[] = [
            'Tucumán',
            'Rumi Punco'
        ];
        $location[] = [
            'Tucumán',
            'San Andrés'
        ];
        $location[] = [
            'Tucumán',
            'San Felipe'
        ];
        $location[] = [
            'Tucumán',
            'San Ignacio'
        ];
        $location[] = [
            'Tucumán',
            'San Javier'
        ];
        $location[] = [
            'Tucumán',
            'San José'
        ];
        $location[] = [
            'Tucumán',
            'San Miguel de Tucumán'
        ];
        $location[] = [
            'Tucumán',
            'San Pedro'
        ];
        $location[] = [
            'Tucumán',
            'San Pedro de Colalao'
        ];
        $location[] = [
            'Tucumán',
            'Santa Rosa de Leales'
        ];
        $location[] = [
            'Tucumán',
            'Sgto. Moya'
        ];
        $location[] = [
            'Tucumán',
            'Siete de Abril'
        ];
        $location[] = [
            'Tucumán',
            'Simoca'
        ];
        $location[] = [
            'Tucumán',
            'Soldado Maldonado'
        ];
        $location[] = [
            'Tucumán',
            'Sta. Ana'
        ];
        $location[] = [
            'Tucumán',
            'Sta. Cruz'
        ];
        $location[] = [
            'Tucumán',
            'Sta. Lucía'
        ];
        $location[] = [
            'Tucumán',
            'Taco Ralo'
        ];
        $location[] = [
            'Tucumán',
            'Tafí del Valle'
        ];
        $location[] = [
            'Tucumán',
            'Tafí Viejo'
        ];
        $location[] = [
            'Tucumán',
            'Tapia'
        ];
        $location[] = [
            'Tucumán',
            'Teniente Berdina'
        ];
        $location[] = [
            'Tucumán',
            'Trancas'
        ];
        $location[] = [
            'Tucumán',
            'Villa Belgrano'
        ];
        $location[] = [
            'Tucumán',
            'Villa Benjamín Araoz'
        ];
        $location[] = [
            'Tucumán',
            'Villa Chiligasta'
        ];
        $location[] = [
            'Tucumán',
            'Villa de Leales'
        ];
        $location[] = [
            'Tucumán',
            'Villa Quinteros'
        ];
        $location[] = [
            'Tucumán',
            'Yánima'
        ];
        $location[] = [
            'Tucumán',
            'Yerba Buena'
        ];
        $location[] = [
            'Tucumán',
            'Yerba Buena (S)'
        ];
        return $location;
    }
}
