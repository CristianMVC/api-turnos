<?php
/**
 * Created by PhpStorm.
 * User: cristian
 * Date: 05/06/19
 * Time: 11:35
 */

namespace ApiV1Bundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
* CategoriaTramiteRepository
**/
class CategoriaTramiteRepository extends ApiRepository
{


    /**
     * @return EntityRepository
     */
    private function getRepository()
    {
        return $this->getEntityManager()->getRepository('ApiV1Bundle:CategoriaTramite');
    }




    /**
     * Obtiene todas las categorías de un punto de atención
     *
     * @param integer $limit Cantidad máxima de registros a retornar
     * @param integer $offset Cantidad de registros a saltar
     * @return mixed
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findAllPaginated($offset, $limit)
    {
        $query = $this->getRepository()->createQueryBuilder('c');
        $query->select('c.id', 'c.nombre' ,'count(t.id) as cantidadTramites','t.id as idTramite','t.nombre as nombreTramite');
        $query->Join('c.tramite','t');
        $query->setFirstResult($offset);
        $query->setMaxResults($limit);
        $query->groupBy('c.id');
        $query->orderBy('c.nombre', 'ASC');


        return $query->getQuery()->getResult();
    }



}