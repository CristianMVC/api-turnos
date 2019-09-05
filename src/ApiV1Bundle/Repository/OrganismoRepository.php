<?php
namespace ApiV1Bundle\Repository;

use \Doctrine\ORM\EntityRepository;

/**
 * Class OrganismoRepository
 * @package ApiV1Bundle\Repository
 *
 * Esta clase es generada por el ORM. Se pueden agregar debajo los métodos que se requieran
 */

class OrganismoRepository extends ApiRepository
{

    /**
     * @return EntityRepository
     */

    private function getRepository()
    {
        return $this->getEntityManager()->getRepository('ApiV1Bundle:Organismo');
    }

    /**
     * Listado de organismos paginado
     *
     * @param integer $limit Cantidad máxima de registros a retornar
     * @param integer $offset Cantidad de registros a saltar
     * @param object $usuarioLogueado objeto usuario
     * @return mixed
     */

    public function findAllPaginate($offset, $limit, $usuarioLogueado)
    {
        $organismoID = $usuarioLogueado->getOrganismoId();

        $query = $this->getRepository()->createQueryBuilder('o');
        $query->select(['o.id', 'o.nombre', 'o.abreviatura']);

        if(isset($organismoID)) {
            $query->where('o.id = :organismo')->setParameter('organismo', $organismoID);
        }

        $query->setFirstResult($offset);
        $query->setMaxResults($limit);
        $query->orderBy('o.nombre', 'ASC');
        return $query->getQuery()->getResult();
    }

    /**
     * Número total de organismos
     *
     * @param object $usuarioLogueado objeto usuario
     * @return integer
     */

    public function getTotal($usuarioLogueado)
    {
        $organismoID = $usuarioLogueado->getOrganismoId();

        $query = $this->getRepository()->createQueryBuilder('o');
        $query->select('count(o.id)');
        if(isset($organismoID)) {
            $query->where('o.id = :organismo')->setParameter('organismo', $organismoID);
        }
        $total = $query->getQuery()->getSingleScalarResult();

        return (int)$total;
    }

    /**
     * total registros de Buscar un organismo por Nombre y Abreviatura
     * @param string $term cadena a buscar
     * @return int
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getTotalSearch($term)
    {
        $query = $this->getRepository()->createQueryBuilder('o');
        $query->select('count(o.id)');
        $query->where('LOWER(o.nombre) LIKE :nombre');
        $query->orWhere('LOWER(o.abreviatura) LIKE :nombre');
        $query->setParameter('nombre', '%' . strtolower($term) . '%');
        $total = $query->getQuery()->getSingleScalarResult();
        return (int) $total;
    }

    /**
     * Buscar un organismo por Nombre y Abreviatura
     *
     * @param string $term cadena para buscar un organismo
     * @param integer $limit Cantidad máxima de registros a retornar
     * @param integer $offset Cantidad de registros a saltar
     * @return mixed
     */
    public function search($term, $offset, $limit)
    {
        $query = $this->getRepository()->createQueryBuilder('o');
        $query->select(['o.id', 'o.nombre', 'o.abreviatura']);
        $query->where('LOWER(o.nombre) LIKE :nombre');
        $query->orWhere('LOWER(o.abreviatura) LIKE :nombre');
        $query->setParameter('nombre', '%' . strtolower($term) . '%');
        $query->setFirstResult($offset);
        $query->setMaxResults($limit);
        return $query->getQuery()->getResult();
    }

}
