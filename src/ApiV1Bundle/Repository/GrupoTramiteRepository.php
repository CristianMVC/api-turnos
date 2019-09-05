<?php

namespace ApiV1Bundle\Repository;

/**
 * Class GrupoTramiteRepository
 * @package ApiV1Bundle\Repository
 *
 * Esta clase es generada por el ORM. Se pueden agregar debajo los métodos que se requieran
 */

class GrupoTramiteRepository extends ApiRepository
{
    /**
     * @return \Doctrine\ORM\EntityRepository
     */

    private function getRepository()
    {
        return $this->getEntityManager()->getRepository('ApiV1Bundle:GrupoTramite');
    }

    /**
     * Listado de grupos de tramites paginado
     *
     * @param integer $puntoAtencionId identificador único de punto de Atención
     * @param integer $limit Cantidad máxima de registros a retornar
     * @param integer $offset Cantidad de registros a saltar
     * @return array
     */
    public function findAllPaginate($puntoAtencionId, $limit, $offset)
    {
        $query = $this->getRepository()->createQueryBuilder('g');
        $query->select([
            'g.id',
            'g.nombre',
            'g.horizonte',
            'g.intervaloTiempo as intervalo'
        ]);
        $query->join('g.puntoAtencion', 'p');
        $query->where('p.id = :puntoAtencionId')->setParameter('puntoAtencionId', $puntoAtencionId);
        $query->setFirstResult($offset);
        $query->setMaxResults($limit);
        $query->orderBy('g.nombre', 'ASC');
        return $query->getQuery()->getResult();
    }

    /**
     * Número total de grupos de trámites
     *
     * @param integer $puntoAtencionId identificador único de punto de Atención
     * @return integer
     */
    public function getTotal($puntoAtencionId)
    {
        $query = $this->getRepository()->createQueryBuilder('g');
        $query->select('count(g.id)');
        $query->join('g.puntoAtencion', 'p');
        $query->where('p.id = :puntoAtencionId')->setParameter('puntoAtencionId', $puntoAtencionId);
        $total = $query->getQuery()->getSingleScalarResult();
        return (int) $total;
    }

    /**
     * Comprobar si un tramite está ya en un grupo del punto de atención
     *
     * @param integer $puntoAtencionId identificador único de punto de Atención
     * @param integer $tramiteId identificador único de trámite
     * @param integer $grupoTramitesId identificador único de grupo trámite
     * @return integer
     */
    public function checkRelationship($puntoAtencionId, $tramiteId, $grupoTramitesId = null)
    {
        $query = $this->getRepository()->createQueryBuilder('g');
        $query->select('count(g.id)');
        $query->join('g.puntoAtencion', 'p');
        $query->join('g.tramites', 't');
        $query->where('p.id = :puntoAtencionId')->setParameter('puntoAtencionId', $puntoAtencionId);
        $query->andWhere('t.id = :tramiteId')->setParameter('tramiteId', $tramiteId);
        if ($grupoTramitesId) {
            $query->andWhere('g.id != :grupoTramitesId')->setParameter('grupoTramitesId', $grupoTramitesId);
        }
        $total = $query->getQuery()->getSingleScalarResult();
        return (int) $total;
    }



     /**
     * Comprobar si un trámite ya pertenece a un grupo de trámites
     *
      * @param integer $tramiteId identificador único de trámite
      * @param integer $grupoTramitesId identificador único de grupo trámite
     * @return integer
     */
    public function checkTramiteGrupoTramite($grupoTramiteId, $tramiteId)
    {
        $query = $this->getRepository()->createQueryBuilder('g');
        $query->select('count(g.id)');
        $query->join('g.puntoAtencion', 'p');
        $query->join('p.grupoTramites', 'gt');
        $query->join('gt.tramites', 't');
        $query->where('g.id = :grupoTramiteId')->setParameter('grupoTramiteId', $grupoTramiteId);
        $query->andWhere('t.id = :tramiteId')->setParameter('tramiteId', $tramiteId);
        $total = $query->getQuery()->getSingleScalarResult();
        return (int) $total;
    }

    /**
     * Buscamos un grupo de trámites por punto de atención y trámite
     *
     * @param integer $puntoAtencionId identificador único de punto de Atención
     * @param integer $tramiteId identificador único de trámite
     * @return mixed
     */
    public function findByPuntoAtencionTramite($puntoAtencionId, $tramiteId)
    {
        $query = $this->getRepository()->createQueryBuilder('g');
        $query->select('g.id');
        $query->join('g.puntoAtencion', 'p');
        $query->join('p.grupoTramites', 'gt');
        $query->join('gt.tramites', 't');
        $query->where('p.id = :puntoAtencionId')->setParameter('puntoAtencionId', $puntoAtencionId);
        $query->andWhere('t.id = :tramiteId')->setParameter('tramiteId', $tramiteId);
        return $query->getQuery()->getOneOrNullResult();
    }
}
