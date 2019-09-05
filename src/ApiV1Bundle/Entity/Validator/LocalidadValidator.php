<?php
namespace ApiV1Bundle\Entity\Validator;

use ApiV1Bundle\Entity\ValidateResultado;
use ApiV1Bundle\Repository\ProvinciaRepository;

/**
 * Class LocalidadValidator
 * @package ApiV1Bundle\Entity\Validator
 */
class LocalidadValidator extends SNTValidator
{
    /** @var ProvinciaRepository  */
    private $provinciaRepository;

    /**
     * LocalidadValidator constructor.
     * @param ProvinciaRepository $provinciaRepository
     */
    public function __construct(ProvinciaRepository $provinciaRepository)
    {
        $this->provinciaRepository = $provinciaRepository;
    }

    /**
     * Verifica los parámetros usados en la búsqueda predictiva
     *
     * @param integer $id identificador único de provincia
     * @param string $qry filtro para la busqueda
     * @return ValidateResultado
     */
    public function validarBusqueda($id, $qry)
    {
        $errors = [];

        $provincia = $this->provinciaRepository->find($id);
        if (!$provincia) {
            $errors[] = 'Provincia inexistente';
        }

        if (strlen($qry) < 3) {
            $errors[] = 'La consulta debe tener un mínimo de 3 carácteres';
        }

        return new ValidateResultado(null, $errors);
    }
}
