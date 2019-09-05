<?php

namespace ApiV1Bundle\Repository;

/**
 * CategoriaRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CategoriaRepository extends ApiRepository
{
    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    private function getRepository()
    {
        return $this->getEntityManager()->getRepository('ApiV1Bundle:Categoria');
    }

    /**
     * Comprobar si un tramite está ya en un punto de atención. Opcionalmente,
     * puede comprobar que un tramite esté en una categoría del punto de
     * atención.
     *
     * @param integer $puntoAtencionId identificador único de punto de Atención
     * @param integer $tramiteId identificador único de trámite
     * @param integer $categoriaId identificador único de categoría (opcional)
     * @return int
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function checkRelationship($puntoAtencionId, $tramiteId, $categoriaId = null)
    {
        $query = $this->getRepository()->createQueryBuilder('c');
        $query->select('count(c.id)');
        $query->join('c.puntoAtencion', 'p');
        $query->join('c.tramites', 't');
        $query->where('p.id = :puntoAtencionId')->setParameter('puntoAtencionId', $puntoAtencionId);
        $query->andWhere('t.id = :tramiteId')->setParameter('tramiteId', $tramiteId);
        if ($categoriaId) {
            $query->andWhere('c.id != :categoriaId')->setParameter('categoriaId', $categoriaId);
        }
        $total = $query->getQuery()->getSingleScalarResult();
        return (int) $total;
    }

    /**
     * Obtiene todas las categorías de un punto de atención
     *
     * @param integer $limit Cantidad máxima de registros a retornar
     * @param integer $offset Cantidad de registros a saltar
     * @param integer $puntoAtencionId identificador único de punto de Atención
     * @return mixed
     */
    public function findAllPaginated($offset, $limit, $puntoAtencionId)
    {
        $query = $this->getRepository()->createQueryBuilder('c');
        $query->select(['c.id', 'c.nombre', 'count(t.id) as cantidadTramites']);
        $query->join('c.puntoAtencion', 'p');
        $query->join('c.tramites', 't');
        $query->where('p.id = :puntoAtencionId')->setParameter('puntoAtencionId', $puntoAtencionId);
        $query->setFirstResult($offset);
        $query->setMaxResults($limit);
        $query->groupBy('c.id');
        $query->orderBy('c.nombre', 'ASC');
        return $query->getQuery()->getResult();
    }

    /**
     * Obtiene el número de categorías de un punto de atención
     *
     * @param integer $puntoAtencionId identificador único de punto de Atención
     * @return int
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getTotal($puntoAtencionId)
    {
        $query = $this->getRepository()->createQueryBuilder('c');
        $query->select('count(c.id)');
        $query->join('c.puntoAtencion', 'p');
        $query->where('p.id = :puntoAtencionId')->setParameter('puntoAtencionId', $puntoAtencionId);
        $total = $query->getQuery()->getSingleScalarResult();
        return (int) $total;
    }

    /**
     * Totem: Obtiene todas las categorías de un punto de atención
     *
     * @param integer $limit Cantidad máxima de registros a retornar
     * @param integer $offset Cantidad de registros a saltar
     * @param integer $puntoAtencionId identificador único de punto de Atención
     * @return mixed
     */
    public function findAllPaginatedTotem($puntoAtencionId, $offset, $limit)
    {
        $query = $this->getRepository()->createQueryBuilder('c');
        $query->select('c.id', 'c.nombre');
        $query->distinct();
        $query->join('c.puntoAtencion', 'p');
        $query->where('p.id = :puntoAtencionId');
        $query->join('c.tramites', 't');
        $query->andWhere('t.visibilidad = 1');
        $query->join('t.puntosAtencion', 'pt');
        $query->andWhere('pt.puntoAtencion = :puntoAtencionId');
        $query->andWhere('pt.estado = 1');
        $query->setFirstResult($offset);
        $query->setMaxResults($limit);
        $query->orderBy('c.nombre', 'ASC');
        $query->setParameter('puntoAtencionId', $puntoAtencionId);
        return $query->getQuery()->getResult();
    }

    /**
     * Totem: Obtiene el número de categorías de un punto de atención
     *
     * @param integer $puntoAtencionId identificador único de punto de Atención
     * @return int
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getTotalTotem($puntoAtencionId)
    {
        $query = $this->getRepository()->createQueryBuilder('c');
        $query->select('c.id');
        $query->distinct();
        $query->join('c.puntoAtencion', 'p');
        $query->where('p.id = :puntoAtencionId');
        $query->join('c.tramites', 't');
        $query->andWhere('t.visibilidad = 1');
        $query->join('t.puntosAtencion', 'pt');
        $query->andWhere('pt.puntoAtencion = :puntoAtencionId');
        $query->andWhere('pt.estado = 1');
        $query->setParameter('puntoAtencionId', $puntoAtencionId);
        return count($query->getQuery()->getArrayResult());
    }
}