<?php
/**
 * Clase estatica para obtener los campos genericos de los formularios de tramites
 * Campos:
 * dni
 * numero_afiliado
 * sexo
 * nacionalidad
 */
namespace ApiV1Bundle\Entity;

abstract class CamposGenericos
{

    private static $campos = [];

    /**
     * Obtener los campos genericos del formulario
     */
    final public static function getCamposGenericos()
    {
        self::setDNI();
        self::setNumeroAfiliado();
        self::setTelefono();
        self::setNacionalidad();
        self::setSexo();
        self::setCuit();
        self::setNombre();
        self::setApellido();
        self::setEmail();
        return self::$campos;
    }

    /**
     * Estructura DNI
     */
    final private static function setDNI()
    {
        $dni = [
            'description' => 'Documento Nacional de Identidad',
            'formComponent' => [
                'typeValue' => 'number'
            ],
            'key' => 'dni',
            'label' => 'DNI',
            'order' => 1,
            'required' => true,
            'type' => 'textbox',
            'mandatory' => false
        ];
        self::$campos['dni'] = $dni;
    }

    /**
     * Estructura número de afiliado
     */
    final private static function setNumeroAfiliado()
    {
        $numeroAfiliado = [
            'description' => '',
            'formComponent' => [
                'typeValue' => 'number'
            ],
            'key' => 'numero_afiliado',
            'label' => 'Número de afiliado',
            'order' => 1,
            'required' => true,
            'type' => 'textbox',
            'mandatory' => false
        ];
        self::$campos['numeroAfiliado'] = $numeroAfiliado;
    }

    /**
     * Estructura número de teléfono
     */
    final private static function setTelefono()
    {
        $telefono = [
            'description' => '',
            'formComponent' => [
                'typeValue' => 'number'
            ],
            'key' => 'telefono',
            'label' => 'Número de teléfono',
            'order' => 1,
            'required' => true,
            'type' => 'textbox',
            'mandatory' => false
        ];
        self::$campos['telefono'] = $telefono;
    }

    /**
     * Estructura Nacionalidad
     */
    final private static function setNacionalidad()
    {
        $nacionalidad = [
            'description' => '',
            'formComponent' => [
                'typeValue' => 'text'
            ],
            'key' => 'nacionalidad',
            'label' => 'Nacionalidad',
            'order' => 1,
            'required' => true,
            'type' => 'textbox',
            'mandatory' => false
        ];
        self::$campos['nacionalidad'] = $nacionalidad;
    }

    /**
     * Estructura Sexo
     */
    final private static function setSexo()
    {
        $sexo = [
            'description' => '',
            'formComponent' => [
                'options' => [
                    ['key' => 'masculino', 'value' => 'masculino'],
                    ['key' => 'femenino', 'value' => 'femenino']
                ]
            ],
            'key' => 'sexo',
            'label' => 'Sexo',
            'order' => 1,
            'required' => true,
            'type' => 'radio',
            'mandatory' => false
        ];
        self::$campos['sexo'] = $sexo;
    }

    /**
     * Estructura CUIT/CUIL
     */
    final private static function setCuit()
    {
        $cuit = [
            'description' => '',
            'formComponent' => [
                'typeValue' => 'text'
            ],
            'key' => 'cuil',
            'label' => 'CUIT/CUIL',
            'order' => 1,
            'required' => true,
            'type' => 'textbox',
            'mandatory' => true
        ];
        self::$campos['cuit'] = $cuit;
    }

    /**
     * Estructura Nombre
     */
    final private static function setNombre()
    {
        $nombre = [
            'description' => '',
            'formComponent' => [
                'typeValue' => 'text'
            ],
            'key' => 'nombre',
            'label' => 'Nombre',
            'order' => 1,
            'required' => true,
            'type' => 'textbox',
            'mandatory' => true
        ];
        self::$campos['nombre'] = $nombre;
    }

    /**
     * Estructura Apellido
     */
    final private static function setApellido()
    {
        $apellido = [
            'description' => '',
            'formComponent' => [
                'typeValue' => 'text'
            ],
            'key' => 'apellido',
            'label' => 'Apellido',
            'order' => 1,
            'required' => true,
            'type' => 'textbox',
            'mandatory' => true
        ];
        self::$campos['apellido'] = $apellido;
    }

    /**
     * Estructura Email
     */
    final private static function setEmail()
    {
        $email = [
            'description' => '',
            'formComponent' => [
                'typeValue' => 'text'
            ],
            'key' => 'email',
            'label' => 'Email',
            'order' => 1,
            'required' => true,
            'type' => 'textbox',
            'mandatory' => true
        ];
        self::$campos['email'] = $email;
    }
}
