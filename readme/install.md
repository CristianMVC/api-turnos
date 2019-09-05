Instalación de software
=======================

[Volver](../README.md)

__Last update:__ 23.07.2017

## Que tengo que instalar en Windows

Básicamente PHP, porque usamos la consola de Symfony para levantar el proyecto, no vas a necesitar ni Apache, ni Nginx, ni nada de webservers.

__PHP__
1. Bajate el zip de PHP 5.6 de [acá](http://windows.php.net/download)
2. Descomprimilo en C:/php
3. Agregalo el ejecutable de PHP a las variables de entorno, [acá te dicen como](https://stackoverflow.com/questions/7307548/how-to-access-php-with-the-command-line-on-windows)
4. Si aún no instalaste GIT, bajatelo de [acá](https://git-scm.com/downloads)
5. Bajate ConEmu de [acá](https://conemu.github.io/), instalalo y configuralo para que use la consola de GitBash
6. Abri ConEmu y si está todo bien si tirás un `php -v` te debería tirar la versión
7. Bajate Composer de [acá](https://getcomposer.org/download/)

__PHPUnit__
1. Bajate el .phar de [https://phpunit.de/](https://phpunit.de/)
2. Renombralo como phpunit y movelo a la carpeta bin
3. Desde la consola proba que funcione todo con bin/phpunit --version

__PHP Mess Detector__
1. Bajate el .phar de [https://phpmd.org/](https://phpmd.org/)
2. Renombralo como phpmd y movelo a la carpeta bin
3. Desde la consola proba que funcione todo con bin/phpmd --version

__PHP Coding Standards__
1. Bajate el .phar de [https://github.com/squizlabs/PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer)
2. Renombralo como phpcs y movelo a la carpeta bin
3. Desde la consola proba que funcione todo con bin/phpcs --version


## Que tengo que instalar en Linux

Básicamente PHP y las herramientas, porque usamos la consola de Symfony para levantar el proyecto, no vas a necesitar ni Apache, ni Nginx, ni ningún otro webservers. La configuración va para Ubuntu, nada, usamos Ubuntu.

__PHP__  
```
$ sudo add-apt-repository ppa:ondrej/php
$ sudo apt-get update
$ sudo apt-get install curl
$ sudo apt-get install zip
$ sudo apt-get install php5.6
$ sudo apt-get install php5.6-pgsql
$ sudo apt-get install php5.6-curl
$ sudo apt-get install php5.6-xml
$ php -v
$ php -m
# GIT
$ sudo apt-get install git
# Composer
$ curl -sSk https://getcomposer.org/installer | php
$ sudo mv composer.phar /usr/local/bin/composer
$ sudo chmod a+c /usr/local/bin/composer
$ composer
```

__PHPUnit__
```
$ wget -c https://phar.phpunit.de/phpunit-5.7.phar
$ sudo mv phpunit-5.7.phar /usr/local/bin/phpunit
$ phpunit --version
$ ln -s /usr/local/bin/phpunit bin/phpunit
```

__PHP Mess Detector__
```
$ wget -c http://static.phpmd.org/php/latest/phpmd.phar
$ sudo mv phpmd.phar /usr/local/bin/phpmd
$ phpmd --version
$ ln -s /usr/local/bin/phpmd bin/phpmd
```
__PHP Coding Standards__
```
$ curl -OL https://squizlabs.github.io/PHP_CodeSniffer/phpcs.phar
$ sudo mv phpcs.phar /usr/local/bin/phpcs
$ phpcs --version
$ ln -s /usr/local/bin/phpcs bin/phpcs
```