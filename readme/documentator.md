Documentator
============

[Volver](../README.md)

__Last update:__ 23.07.2017

Generamos la documentación automáticamente usando [PHPDocumentator](https://phpdoc.org/).

La documentación de la API se guarda en el [repo de documentación](https://hxgitlab.hexacta.com/SistemaNacionalDeTurnos/environment).

__Linux__

```
$ sudo apt-get install graphviz
$ wget http://www.phpdoc.org/phpDocumentor.phar
$ mv phpDocumentor.phar bin/phpdoc
$ chmod a+x bin/phpdoc
$ bin/phpdoc -d src/ApiV1Bundle -t docs/api
```