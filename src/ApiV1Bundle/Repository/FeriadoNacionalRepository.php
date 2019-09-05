<?php

namespace ApiV1Bundle\Repository;

/**
 * Class FeriadoNacionalRepository
 * @package ApiV1Bundle\Repository
 *
 * Esta clase es generada por el ORM. Se pueden agregar debajo los métodos que se requieran
 */

class FeriadoNacionalRepository extends ApiRepository
{
    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    private function getRepository()
    {
        return $this->getEntityManager()->getRepository('ApiV1Bundle:FeriadoNacional');
    }

    /**
     * //TODO Esto es lo mismo que hacer un findAll. Eliminar esta función y reemplazarla donde se este usando
     * Obtener todos los feriados nacionales
     *
     * @return mixed
     */
    public function getAllFeriadosNacionales()
    {
        $query = $this->getRepository()->createQueryBuilder('fn');
        $query->select('fn.fecha');
        $query->orderBy('fn.fecha', 'ASC');
        return $query->getQuery()->getResult();
    }
    }
