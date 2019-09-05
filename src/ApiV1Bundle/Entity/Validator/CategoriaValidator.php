<?php

namespace ApiV1Bundle\Entity\Validator;

use ApiV1Bundle\Entity\Categoria;
use ApiV1Bundle\Entity\PuntoAtencion;
use ApiV1Bundle\Entity\Tramite;
use ApiV1Bundle\Entity\ValidateResultado;
use ApiV1Bundle\Repository\CategoriaRepository;
use ApiV1Bundle\Repository\PuntoAtencionRepository;

/**
 * Class CategoriaValidator
 * @package ApiV1Bundle\Entity\Validator
 */
class CategoriaValidator extends SNTValidator
{
    /** @var CategoriaRepository  */
    private $categoriaRepository;
    /** @var PuntoAtencionRepository  */
    private $puntoAtencionRepository;

    /**
     * CategoriaValidator constructor.
     * @param CategoriaRepository $categoriaRepository
     * @param PuntoAtencionRepository $puntoAtencionRepository
     */
    public function __construct(
        CategoriaRepository $categoriaRepository,
        PuntoAtencionRepository $puntoAtencionRepository
    )
    {
        $this->categoriaRepository = $categoriaRepository;
        $this->puntoAtencionRepository = $puntoAtencionRepository;
    }

    /**
     * Validar parámetros
     *
     * @param array $params arreglo con los datos a validar
     * @return array
     */
    public function validateParams($params)
    {
        $errors = $this->validar(
            $params, [
                "nombre" => "required",
                "tramites" => "required:matriz",
            ]
        );

        if (!count($errors) && count($params['tramites']) < 1) {
            $errors[] = "Debe tener al menos un trámite";
        }

        return $errors;
    }

    /**
     * Validar crear
     *
     * @param object $puntoAtencion objeto punto de atención
     * @param array $params arreglo con los datos a validar
     * @return ValidateResultado
     */
    public function validarCreate($params, $puntoAtencion)
    {
        $errors = $this->validateParams($params);

        if (!$puntoAtencion) {
            $errors[] = "El punto de atención es inválido";
        }

        return new ValidateResultado(null, $errors);
    }

    /**
     * Validar editar
     *
     * @param object $puntoAtencion objeto punto de atención
     * @param object $categoria objeto categoría
     * @param array $params arreglo con los datos a validar
     * @return ValidateResultado
     */
    public function validarEdit($params, $puntoAtencion, $categoria)
    {
        $errors = $this->validateParams($params);

        if (!$puntoAtencion) {
            $errors[] = "El punto de atención no existe";
        }

        if (!$categoria) {
            $errors[] = "La categoría no existe";
        }

        return new ValidateResultado(null, $errors);
    }

    /**
     * Validar eliminar
     * @param object $puntoAtencion objeto punto de atención
     * @param object $categoria objeto categoría
     * @return ValidateResultado
     */
    public function validarDelete($puntoAtencion, $categoria)
    {
        $errors = [];
        if (!$puntoAtencion) {
            $errors[] = "El punto de atención no existe";
        }

        if (!$categoria) {
            $errors[] = "La categoría no existe";
        }

        return new ValidateResultado(null, $errors);
    }

    /**
     * Validar usuario
     *
     * @param object $usuarioValidateResult objeto ValidateResultado
     * @param object $puntoAtencionId objeto punto de atención
     * @return ValidateResultado
     */
    public function validarUsuario($usuarioValidateResult, $puntoAtencionId)
    {
        $errors = [];

        if (!$usuarioValidateResult->hasError()) {
            $usuario = $usuarioValidateResult->getEntity();
            $pda = $usuario->getPuntoAtencion();

            if ($pda->getId() != $puntoAtencionId) {
                $errors[] = "El usuario no pertenece al punto de atención";
            }

            if (count($errors)) {
                return new ValidateResultado(null, $errors);
            }
            return new ValidateResultado($usuario, []);
        }

        return $usuarioValidateResult;
    }

    /**
     * Validar relación categoría - puntod eatención
     *
     * @param PuntoAtencion $puntoAtencion
     * @param Categoria $categoria
     * @return ValidateResultado
     */
    public function validarCategoriaPuntoAtencion(
        PuntoAtencion $puntoAtencion,
        Categoria $categoria)
    {
        $errors = [];
        if ($puntoAtencion->getId() != $categoria->getPuntoAtencion()->getId()) {
            $errors[] = "La categoría no pertenece a este punto de atención";
        }
        return new ValidateResultado(null, $errors);
    }

    /**
     * validar trámite
     *
     * @param object $tramite objeto Tramite
     * @param integer $categoriaId identificador único de categoría
     * @param integer $puntoAtencionId identificador único de punto de atención
     * @return ValidateResultado
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function validarTramite($tramite, $puntoAtencionId, $categoriaId)
    {
        $errors = [];
        if (!$tramite) {
            $errors[] = "Trámite inexistente";
            return new ValidateResultado(null, $errors);
        }

        // Verificar que el trámite pertenece al mismo PDA
        $checkEnabled =
            $this->puntoAtencionRepository->checkTramiteRelationship($puntoAtencionId, $tramite->getId());
        if ($checkEnabled) {

            // Verificar que el trámite no pertenezca a otra categoría en el mismo PDA
            $checkOwnership = $this->categoriaRepository->checkRelationship(
                $puntoAtencionId,
                $tramite->getId(),
                $categoriaId);
            if ($checkOwnership) {
                $errors[] = "El trámite {$tramite->getNombre()} ya pertenece a" .
                    " una categoría en el mismo punto de atención";
                return new ValidateResultado(null, $errors);
            }
        } else {
            $errors[] = "El trámite {$tramite->getNombre()} no puede ser realizado por el punto de atención";
        }
        return new ValidateResultado(null, $errors);
    }
}
