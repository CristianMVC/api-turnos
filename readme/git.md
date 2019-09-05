El uso de GIT
=============

[Volver](../README.md)

__Last update:__ 23.07.2017

## Los branches

__master__: el branch principal, nadie debe comitear acá, los cambios llegan por merge con el branch testing.  
__testing__: es el branch donde los testers hacen su laburo sin que interfiera con el trabajo de front o back. Nadie debe comitear acá, los cambios llegan por merge con el branch develop.  
__develop__: es el branch de desarrollo de la API. Nadie debe comitear acá, los cambios llegan de las branches que se abren a partir de este branch.  

![Branches](http://i.imgur.com/kixWpkj.png)

## El precommit

Usamos el [pre-commit de Symfony](https://github.com/hoangthienan/symfony-pre-commit)

__Windows__
```
cd [project]
del .git/hooks/pre-commit
copy tools\pre-commit .git\hooks\pre-commit
```

__Linux__
```
cd [project]
unlink .git/hooks/pre-commit
cp tools/pre-commit .git/hooks/pre-commit
```

## Mi primer commit

```
$ git checkout develop
$ git pull
$ git checkout -b #_del_pbi-nombre_de_la_tarea
# acá agrego mucho código y otras cosas
$ git push origin #_del_pbi-nombre_de_la_tarea
```

Con todos estos datos, podes ir a Gitlab y abrir un Merge Request para que puedan hacer el code review correspondiente.

* _P:_ Puedo comitear a Master o Dev? _R:_ No se te ocurra, te cortamos los dedos!
* _P:_ En que branch trabajo? _R:_ Tenés que crear un branch con el número del PBI y el nombre de la tarea y comitear a ese branch

## Code review / Peer review

1. Si, hay tareas de Peer review
2. El code review se hace entre el desarrollador que escribió el código y el que revisa, que puede o no ser el arquitecto
2. El code review se hace con un Merge Request, si no hay Merge Request, hay tabla
3. El code review debe incluir:
	* Revisión de código
	* Revisión de tests
	* Ejecución de los tests
4. Si el cambio es muy grande o involucra una parte CORE de la aplicación que debe ser revisada, se debe abrir un Work item "peer review"en [TFS](https://tfs.hexacta.com/tfs/Rohan/SistemaNacionalDeTurnos) y linkearlo como child del PBI o tarea desarrollada. Ese peer review debe incluir:
	* Título: el nombre del branch
	* Area path
	* Iteration path
	* Tipo de inspección: Walkthrough
	* Asignarselo a la persona que va a hacer el code review
	* En la descripción agregar el link al Merge Request
5. Para que sirve tanto lio? Para formalizar el proceso y tener trazabilidad entre el Backlog y el repositorio de GIT.
