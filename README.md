Sistema Nacional de Turnos
==========================

![Ministerio de Modernización](http://i.imgur.com/1b6FZA8.png)

__Last update:__ 21.10.2017

Ante todo, sentite libre de editar, agregar y corregir. El README lo hacemos entre todos. La idea del README es tener un lugar para compartir lo básico del proyecto entre todos los integrantes del equipo, para todo lo demás tenemos la Wiki. Los proyectos van cambiando y sus miembros también, por eso, es fundamental tener documentado todo lo importante para que esté siempre disponible.

Como verás, por defecto contamos con una estructura básica predefinida. Si bien todo lo que está aquí es relevante. Esta información te va a servir para entender donde está todo, como arrancar con el proyecto y como hacer las cosas menos traumáticas con el proyecto si es tu primer día.

Lo más importante de todo es que si tenés dudas, no sabes bien que hacer o querés comprar medialunas, preguntes, siempre la comunicación es mejor que quedarse callado, que se caiga el sistema y tengamos que hacer un git blame. Bueno, si querés comprar medialunas, no preguntes, traelas!

## Indice

1. [Código de convivencia](#c%C3%B3digo-de-convivencia)
2. [Donde está todo?](#donde-est%C3%A1-todo)
3. [Dominios](readme/domains.md)
4. [Que estamos usando?](#que-estamos-usando)
5. [Herramientas de calidad de código](readme/herramientas.md)
6. [Que tengo que instalar](readme/install.md)
7. [Como configurar la app](readme/configuracion.md)
8. [Como usamos GIT](readme/git.md)
9. [Los tests](readme/tests.md)
10. [Los deploys](readme/deploys.md)
11. [Docker](#docker)
12. [Base de datos](readme/database.md)
13. [REST Bundle](readme/rest.md)
14. [Documentator](readme/documentator.md)
15. [PHPMetrics](readme/phpmetrics.md)
16. [Insomnia REST client](https://hxgitlab.hexacta.com/SistemaNacionalDeTurnos/environment/tree/master/insomnia)
17. [Integración continua](readme/tfs_cicd.md)
18. [Las release notes](https://hxgitlab.hexacta.com/SistemaNacionalDeTurnos/environment/tree/master/releasenotes)
19. [Documentación de la API](https://hxgitlab.hexacta.com/SistemaNacionalDeTurnos/environment/tree/master/api)

* * *

## Código de convivencia

1. No somos un equipo de front, uno de back, el funcional y unos testers, somos un solo equipo de SNT, con desarrolladores dedicados al front, desarrolladores dedicados al back, un analista funcional que escribe las user stories que nos ayudan a desarrollar y un equipo de testing que nos ayuda a mantener la calidad del código y la solides de la aplicación.
2. Todos participamos de todas las reuniones: planning, daily, retros, groomings, y todos participamos de forma activa, sobre todo en la planning y en la retro, donde es importante el feedback que podamos dar y recibir.
3. Todos somos responsables de que los objetivos del proyecto se cumplan, hay mucha gente que va a depender de que hagamos un buen trabajo, es el Sistema Nacional de Turnos.
4. Todos somos responsables de la calidad del código y de cumplir buenas prácticas, para eso están las code reviews.
5. Los deploys se hacen con el release note en mano y coordinando con la chicos que hacen front y el equipo de testing, así no rompemos nada del lado de ellos.

## Donde está todo?

* [La wiki del proyecto](https://wiki.hexacta.com/mediawiki/index.php/SistemaNacionalDeTurnos:Portada)
* [El sitio del proyecto](https://projects.hexacta.com/sistemaNacionalDeTurnos/default.aspx)
* [El repo de GIT](https://hxgitlab.hexacta.com/SistemaNacionalDeTurnos/back-end)
* [La URL de la API](http://sntapi.hexacta.com/)
* [Los estándares de API del Gobierno Nacional](https://github.com/argob/estandares/blob/master/estandares-apis.md)
* [TFS](https://tfs.hexacta.com/tfs/Rohan/SistemaNacionalDeTurnos)

__FAQ__

* _P:_ Estoy en casa y no anda nada! _R:_ Conectate a la VPN campeón.
* _P:_ Estoy conectado a la VPN y no anda nada! _R:_ Avisanos por chat campeón!
* _P:_ A quien le pido acceso a la base de datos? _R:_ Manda mail a [fcarrera@hexacta.com](mailto:fcarrera@hexacta.com) con tus datos.
* _P:_ Por que PHP y porque 5.6? _R:_ Un hechicero lo hizo, digo, requerimientos no funcionales.

## Que estamos usando?

* __Symfony__: 2.8.24
* __La documentación oficial__: [http://symfony.com/doc](http://symfony.com/doc)
* __PHP__: 5.6.3
* __GIT__: 1.9

## Docker

Quiero usar Docker para testing o para desarrollar el front sin tener que instalar nada.

Bueno, usando [este tutorial](https://hxgitlab.hexacta.com/SistemaNacionalDeTurnos/environment/tree/master/docker), te explicamos como levantar la última versión de la API en forma local con Docker.
