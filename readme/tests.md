Los tests
=========

[Volver](../README.md)

__Last update:__ 23.07.2017

Hay dos tipos de tests, los tests unitarios y los smoke tests. Tests unitarios para los modelos y smoke tests para los endpoints de la API.

Es preferible que primero escribas los tests a partir de las user stories antes de comenzar a escribir código; si, estamos hablando de TDD, y es mejor que te amigues con TDD, porque a largo plazo es mejor para todos.

Puedo escribir el código y después escribir los tests? Si, podes, pero que ese código incluya los tests es parte de la _"Definition of done"_ del PBI, sin tests, no hay PBI aprobado y no hay release, y nos desviamos en puntos y en horas.

Para ambos tests usamos la misma herramienta, PHPUnit. Para correr las pruebas, dependiendo de donde tengas instalado PHPUnit

```
$ php bin/phpunit -c app
```