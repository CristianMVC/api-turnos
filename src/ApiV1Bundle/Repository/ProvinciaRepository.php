<?php
namespace ApiV1Bundle\Repository;

/**
 * Class ProvinciaRepository
 * @package ApiV1Bundle\Repository
 *
 * Esta clase es generada por el ORM. Se pueden agregar debajo los métodos que se requieran
 */
class ProvinciaRepository extends ApiRepository
{

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    private function getRepository()
    {
        return $this->getEntityManager()->getRepository('ApiV1Bundle:Provincia');
    }

    /**
     * Listado de provincias paginado
     *
     * @param integer $limit Cantidad máxima de registros a retornar
     * @param integer $offset Cantidad de registros a saltar
     * @return mixed
     */
    public function findAllPaginate($offset, $limit)
    {
        $query = $this->getRepository()->createQueryBuilder('p');
        $query->select([
            'p.id',
            'p.nombre'
        ]);
        $query->setFirstResult($offset);
        $query->setMaxResults($limit);
        $query->orderBy('p.nombre', 'ASC');

        return $query->getQuery()->getResult();
    }

    /**
     * Número total de provincias
     *
     * @return integer
     */
    public function getTotal()
    {
        $query = $this->getRepository()->createQueryBuilder('p');
        $query->select('count(p.id)');
        $total = $query->getQuery()->getSingleScalarResult();

        return (int)$total;
    }
}
