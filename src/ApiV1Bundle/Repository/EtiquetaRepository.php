<?php
/**
 * Created by PhpStorm.
 * User: cristian
 * Date: 10/06/19
 * Time: 15:41
 */

namespace ApiV1Bundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\Tools\Pagination\Paginator;


class EtiquetaRepository  extends ApiRepository
{


    /**
     * @return EntityRepository
     */
    private function getRepository()
    {
        return $this->getEntityManager()->getRepository('ApiV1Bundle:Etiqueta');
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
        $query = $this->getRepository()->createQueryBuilder('e');
        $query->select(['e.id', 'e.nombre', 'count(e.id) as cantidadTramites']);
        $query->setFirstResult($offset);
        $query->setMaxResults($limit);
        $query->groupBy('e.id');
        $query->orderBy('e.nombre', 'ASC');

        $paginator = new Paginator($query, $fetchJoinCollection = true);
        $count = count($paginator);


    return ["total"=>$count, "result"=>$query->getQuery()->getResult()];
    }

    /**
     * Retorna todas las etiquetas de un tramite
     *
     * @param integer $id tramite
     * @param integer $limit Cantidad máxima de registros a retornar
     * @param integer $offset Cantidad de registros a saltar
     * @return mixed
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */


    public function etiquetasPorTramite($id, $offset, $limit){
        $query = $this->getRepository()->createQueryBuilder('e');
        $query->select(['e.id', 'e.nombre','count(t.id) as cantidadTramites']);
        $query->join('e.tramites', 't');
        $query->where('t.id = :id')->setParameter('id', $id);
        $query->setFirstResult($offset);
        $query->setMaxResults($limit);
        $query->groupBy('e.id');
        $query->orderBy('e.nombre', 'ASC');

       return $query->getQuery()->getResult();
    }
    
    
    /**
     * Retorna todas las etiquetas de un tramite
     * @param array $etiquetas 
     * @param integer $limit Cantidad máxima de registros a retornar
     * @param integer $offset Cantidad de registros a saltar
     * @return mixed
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    
    public function filtrarPorEtiqueta($etiquetas, $offset, $limit) {
        $semi_filtred_array = [];
        $filter_array = [];

        $query = $this->getRepository()->createQueryBuilder('e');
        $query->select(['t.id','t.nombre','e.nombre as et']);
        $query->join('e.tramites', 't');
        $query->where('e.nombre IN (:etq)')->setParameter('etq', array_values($etiquetas));
        $query->orderBy('t.id', 'ASC');
        $query->setFirstResult($offset);
        $query->setMaxResults($limit);
        $query->distinct(); 
        $no_filtred_array = $query->getQuery()->getResult(Query::HYDRATE_ARRAY);

        foreach($no_filtred_array as $nfr) {
           $semi_filtred_array[$nfr['id']][] = $nfr['et'] ;
           $semi_filtred_array[$nfr['id']]['nombre'] = $nfr['nombre'] ;
        }
        $i = 0;
        foreach($semi_filtred_array as $key => $sfa) {
            if(count(array_diff($etiquetas,$sfa)) == 0) {
            $filter_array[$i]['id'] = $key;  
            $filter_array[$i]['nombre'] = $sfa['nombre'];          
            }
            $i++;
        }

           return $filter_array;  
                     
    }


}