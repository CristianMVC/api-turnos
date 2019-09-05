PHP Metrics
=============

[Volver](../README.md)

__Last update:__ 26.08.2017

## Instalar PHPMetrics

```
$ cd bin
$ curl -L  https://github.com/phpmetrics/PhpMetrics/releases/download/v2.1.0/phpmetrics.phar -O phpmetrics.phar
$ chmod a+x bin/phpmetrics.phar
```

## Como generar el reporta

```
$ bin/phpmetrics.phar --report-html='./app/report' ./src/ApiV1Bundle/
```