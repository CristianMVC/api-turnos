Base de datos
=============

[Volver](../README.md)

__Last update:__ 21.10.2017

## Listado de bases de datos

| Host | Port | User | Password | Database | Environment |
|------|------|------|----------|----------|-------------|
| hxv-sntdb.hexacta.com | 3306 | root | asdf1234 | snt_uat | UAT |
| hxv-sntdb.hexacta.com | 3306 | root | asdf1234 | ant_testing | Testing |
| hxv-sntdb.hexacta.com | 3306 | root | asdf1234 | snt_develop | Develop |
| hxv-sntdb.hexacta.com | 3306 | root | asdf1234 | snt_unit | Unit testing |

## Como generar o actualizar mi base de datos

```
$ php app/console doctrine:schema:validate
$ php app/console doctrine:schema:update
$ php app/console doctrine:schema:update --force
```

## Seeders

```
$ php app/console snt:seeder:provincias
$ php app/console snt:seeder:organismos
$ php app/console snt:seeder:puntosatencion
$ php app/console snt:seeder:tramites
$ php app/console snt:seeder:tramites:puntoatencion
$ php app/console snt:seeder:puntosatencion:horarios
$ php app/console snt:seeder:tramites:grupo
$ php app/console snt:seeder:puntosatencion:disponibilidad
$ php app/console snt:seeder:turnos
```
