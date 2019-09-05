Changelog :: SNT
================

## Version 0.5.1

__Features__

* Task 34747:[back] modelado de usuarios SNT
* Task 34748:[back] generar endpoint para crear usuarios
* Task 34749:[back] roles disponibles de acuerdo al usuario.
* Task 34757:[back] agregar endpoint para modificar datos de usuario.
* Task 34762:[back] endpoint para borrar usuario
* Task 34779:[back] endpoint modificar contraseña
* Task 35169:[back] validar token al ingresar a la url de recuperar contraseña
* Task 35586:[back] Crear endpoint listado areas con autocomplete que pueda ser filtrado por organismo
* Task 35587:[back] Crear endpoint listado de puntos de atención con autocomplete que pueda ser filtrado por organismo y area
* Task 35792:[back] Agregar busqueda por codigo de turno o cuil en colas además de backend de turnos
* Task 36261:[back] generalizar errores SNT
* Task 36357:[back] El listado de puntos de atencion debe contemplar el rol del usuario
* Task 37321:[back] Test listado de usuarios filtrado por rol
* Task 38115:[back] Agregar trámites al resultado de búsqueda de turnos del ciudadano
* Task 38229:[back] Faltan properties del turno, antes venian y ahora no
* Task 38385:[back] Enviar organismos, area y pda al obtener el usuario

__Bugs__

* Bug 34963:[back] el endpoint de disponibilidad devuelve 200 con error
* Bug 35732:[Backoffice][AGREGAR FERIADO NACIONAL] Al crear un nuevo PDA el Feriado nacional no se replica automáticamente en el calendario
* Bug 38020:[Colas][MODIFICAR ESTADO DEL TURNO] Al cambiar el estado el turno a Terminado "No Atendido" en Recepción el mismo aparece en Ventanilla
* Bug 38647:[Backoffice] [RANGO DE ATENCION] - Se produce un error al ingresar un rango no válido
* Bug 38660:[back] Cancelacion de turnos por id
* Bug 38690:[Colas][BUSCAR TURNO POR CUIL] Al buscar por CUIL trae turnos que no pertenecen a ese punto de atención