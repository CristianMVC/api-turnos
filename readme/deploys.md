Deploys
=======

[Volver](../README.md)

__Last update:__ 23.07.2017

## La release note

La release note es el documento en donde nosotros decimos que cambios se hicieron en ese release (Business as usual). Eso incluye features desarrolladas, bugs arreglados, mejoras en el sistema.

Para que sirven las release notes? Para que tanto el funcional, como los devs de front y los testers sepan que cambios y mejoras fueron introducidas en determinado release, más allá de las tareas en TFS. Es un resumen de todas las tareas y bugs que tenemos en TFS que fueron incluidos en determinado punto de la historia en la que considerabamos que la API estaba estable.

__FAQ__

* _P:_ Donde guardamos las release notes? _R:_ Al no ser un release note formal, las versionamos dentro del repo de GIT, en la carpeta [releases](https://hxgitlab.hexacta.com/SistemaNacionalDeTurnos/back-end/tree/master/releases).

## Los builds

Un build de la API es un tag dentro del repositorio de GIT en el branch __master__ que marca un punto en la historia del proyecto que cumple con los siguientes requisitos:

* Están incluidos features terminadas (Código)
* Fue testeado por el desarrollador (Test unitarios, Smoke tests)
* Paso por un peer review entre desarrolladores
* Fue probado por los testers con el build actual del front (Manual, Selenium)

```
$ git tag -a v0.1.0 -m 'release_note_name'
$ git push origin v0.1.0
```

Para que usamos tags? Para poder hacer un rollback a una versión anterior si a pesar de que un build cumple los requisitos, cuando llega al servidor de desarrollo rompe algo.

## Los deploys

Los deploys los hacemos desde [TFS](#deploys-con-tfs), con todo el proceso de builds y de tests automáticos incluidos, pero...
Hay que hacer un deploy a mano, que horror!

```
$ ssh hxv-snt.hexacta.com
$ cd /usr/share/nginx/snt-api
$ git pull
$ git checkout _la_version_que_corresponde_
$ composer install --no-dev --no-scripts -vvv
```

## Los rollbacks

Si por alguna razón tenemos que hacer un rollback, lo hacemos desde TFS, si TFS nos da problemas, el último recurso es hacer un deploy de la versión anterior de forma manual, como dice la sección deploys.

## Deploys con TFS

El build funciona automáticamente cuando se hacer un merge a develop en el caso de front y a testing en el caso de back, aún así se dejan documentados los pasos:

1. Ir a TFS donde tenemos los [builds](https://tfs.hexacta.com/tfs/Rohan/SistemaNacionalDeTurnos/_build)
1. Hay un build de Front-end y un build de Backend, muy bien distinguidos uno de otro
1. Seleccionamos el build que vamos a accionar
1. Se activa el build con "Queue new build..." que está en la parte superior derecha, si, el azul gigante!
1. Esperas que termine todo OK, o va a llegar un mail diciendo que algo salió mal
1. A quien le llega ese mail? Al owner del build