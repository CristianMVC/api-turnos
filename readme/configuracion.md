Como configurar la app
======================

[Volver](../README.md)

__Last update:__ 23.07.2017

__Archivo de configuración__: app/config/parameters.yml

Generá tu clave SSH desde la consola y subila con un copy/paste de la __id_rsa.pub__ a [Gitlab](https://hxgitlab.hexacta.com/profile/keys). Si la generaste bien, debe estar en tu carpeta personal dentro de la carpeta .ssh.

```
$ ssh-keygen -t RSA
```

Bajate el proyecto, instalá las dependencias y configuralo. Para esto ya debes tener acceso a la base de datos.

```
# clonate el repo
$ git clone git@hxgitlab.hexacta.com:SistemaNacionalDeTurnos/back-end.git .
# instalamos las dependencias y de paso configuralo
$ composer install -vvv
```

## Como levantar la app

1. cd a la carpeta del proyecto
2. ejecutar el comando `php app/console server:run localhost:8080`
3. abri el browser y entra la URL [http://localhost:8080](http://localhost:8080)