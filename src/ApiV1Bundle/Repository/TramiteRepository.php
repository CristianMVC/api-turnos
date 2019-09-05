<?php
namespace ApiV1Bundle\Repository;

use ApiV1Bundle\Entity\Tramite;

/**
 * Class TramiteRepository
 * @package ApiV1Bundle\Repository
 *
 * Esta clase es generada por el ORM. Se pueden agregar debajo los métodos que se requieran
 */
class TramiteRepository extends ApiRepository
{

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    private function getRepository()
    {
        return $this->getEntityManager()->getRepository('ApiV1Bundle:Tramite');
    }

    /**
     * Listado de trámites paginado
     *
     * @param String $term Consulta
     * @param integer $limit Cantidad máxima de registros a retornar
     * @param integer $offset Cantidad de registros a saltar
     * @return mixed
     */
    public function findAllTramitePaginate($term, $limit, $offset)
    {
        $query = $this->getRepository()->createQueryBuilder('t');
        $query->distinct();
        $query->select('t.id, t.nombre, a.nombre as area, o.nombre as organismo, t.excepcional, c.id as idCat', 't.miArgentina');
        $query->leftjoin('t.categoriaTramite', 'c');
        $query->join('t.areas', 'a');
        $query->join('a.organismo', 'o');
        $query->join('t.puntosAtencion', 'pt');
        $query->where('t.visibilidad = 1');
        $query->andWhere('pt.estado = 1');
        $query->andWhere('t.miArgentina <> 1');

        if ($term && ! empty($term)) {
            $query->Andwhere(
                $query->expr()->orX(
                    $query->expr()->like('lower(t.nombre)', ':data'),
                    $query->expr()->like('lower(a.nombre)', ':data'),
                    $query->expr()->like('lower(a.abreviatura)', ':data'),
                    $query->expr()->like('lower(o.nombre)', ':data'),
                    $query->expr()->like('lower(o.abreviatura)', ':data')
                )
            )->setParameter('data', '%' . strtolower($term) . '%');
        }
        $query->setFirstResult($offset);
        $query->setMaxResults($limit);
        $query->orderBy('t.nombre', 'ASC');

        return $query->getQuery()->getResult();
    }



    public function findTramitesOrganismo($idOrganismo, $limit, $offset) {

        $query = $this->getRepository()->createQueryBuilder('t');
        $query->select('t.id', 't.nombre', 't.duracion', 't.visibilidad','t.org',  'c.id as idCat', 't.miArgentina');
        $query->leftjoin('t.categoriaTramite', 'c');
        $query->join('a.tramites', 't');
        $query->join('a.organismo', 'o');
        $query->where('o.id = :orgId')->setParameter('orgId', $idOrganismo);
        $query->setFirstResult($offset);
        $query->setMaxResults($limit);
        $query->orderBy('t.nombre', 'ASC');

        return $query->getQuery()->getResult();


    }




    public function findAreasTramite($id) {

        $query = $this->getRepository()->createQueryBuilder('t');
        $query->select('a.id');
        $query->join('t.areas', 'a');
        $query->where('t.id = :tramiteId')->setParameter('tramiteId', $id);
        return $query->getQuery()->getResult();
    }



    /**
     * Obtiene los puntos de atención del tramite por Provincia y Localidad
     *
     * @param Tramite $tramite
     * @param integer $idProvincia identificador único de provincia
     * @param integer $idLocalidad identificador único de localidad
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function findPuntosAtencionByTramite(Tramite $tramite, $idProvincia, $idLocalidad)
    {
        $puntosAtencion = $tramite->getPuntosAtencion()->filter(
            function ($pa) use ($idProvincia, $idLocalidad) {
                return ($pa->provincia->id == $idProvincia and $pa->localidad->id == $idLocalidad);
            }
        );

        return $puntosAtencion;
    }

    /**
     * Número total de trámites
     *
     * @param string $term término de búsqueda
     * @return int
     */
    public function getTotal($term)
    {
        $query = $this->getRepository()->createQueryBuilder('t');

        $query->select('t.id', 'sum(pt.estado)');
        $query->join('t.areas', 'a');
        $query->join('a.organismo', 'o');
        $query->join('t.puntosAtencion', 'pt');
        $query->where('t.visibilidad = 1');
        if ($term && ! empty($term)) {
            $query->Andwhere(
                $query->expr()->orX(
                    $query->expr()->like('lower(t.nombre)', ':data'),
                    $query->expr()->like('lower(a.nombre)', ':data'),
                    $query->expr()->like('lower(a.abreviatura)', ':data'),
                    $query->expr()->like('lower(o.nombre)', ':data'),
                    $query->expr()->like('lower(o.abreviatura)', ':data')
                )
            )->setParameter('data', '%' . strtolower($term) . '%');
        }
        $query->having('SUM(pt.estado) > 0');
        $query->groupBy('t.id');

        $total = $query->getQuery()->getResult();
        return count($total);
    }

    /**
     * Tramites por grupo
     *
     * @param integer $grupoId identificador único de GrupoTramite
     * @return mixed
     */
    public function findTramitesByGrupo($grupoId)
    {
        $query = $this->getRepository()->createQueryBuilder('t');
        $query->select([
            't.id',
            't.nombre',
            'c.id as idCat'
        ]);
        $query->leftjoin('t.categoriaTramite', 'c');
        $query->join('t.grupoTramites', 'g');
        $query->where('g.id = :grupoId')->setParameter('grupoId', $grupoId);
        $query->orderBy('t.nombre', 'ASC');
        return $query->getQuery()->getResult();
    }

    /**
     * Listado de tramites por punto de atención
     *
     * @param integer $puntoAtencionId identificador único de punto de atención
     * @param integer $limit Cantidad máxima de registros a retornar
     * @param integer $offset Cantidad de registros a saltar
     * @return mixed
     */
    public function findByPuntoAtencion($puntoAtencionId, $offset, $limit)
    {
        $query = $this->getRepository()->createQueryBuilder('t');
        $query->select([
            't.id',
            't.nombre',
            't.visibilidad',
            'pt.estado',
            'gt.id as grupo_tramite_id',
            'c.id as idCat',
            't.miArgentina'
        ]);
        $query->leftjoin('t.categoriaTramite', 'c');
        $query->join('t.puntosAtencion', 'pt');
        $query->join('pt.puntoAtencion', 'p');
        $query->leftJoin('t.grupoTramites', 'gt');
        $query->where('p.id = :puntoAtencionId')->setParameter('puntoAtencionId', $puntoAtencionId);
        $query->setFirstResult($offset);
        $query->setMaxResults($limit);
        $query->groupBy('t.id');
        $query->orderBy('t.nombre', 'ASC');
        return $query->getQuery()->getResult();
    }

    /**
     * Total de tramites del punto de atención
     *
     * @param integer $puntoAtencionId identificador único de punto de atención
     * @return integer
     */
    public function getTotalByPuntoAtencion($puntoAtencionId)
    {
        $query = $this->getRepository()->createQueryBuilder('t');
        $query->select('count(t.id)');
        $query->join('t.puntosAtencion', 'pt');
        $query->join('pt.puntoAtencion', 'p');
        $query->where('p.id = :puntoAtencionId')->setParameter('puntoAtencionId', $puntoAtencionId);
        $total = $query->getQuery()->getSingleScalarResult();
        return (int) $total;
    }

    /**
     * Encontrar el horizonte de un tramite
     *
     * @param integer $tramiteId identificador único de trámite
     * @return mixed
     */
    public function findHorizonte($tramiteId)
    {
        $query = $this->getRepository()->createQueryBuilder('t');
        $query->select(['t.id', 'gt.horizonte']);
        $query->join('t.puntosAtencion', 'pt');
        $query->join('pt.puntoAtencion', 'p');
        $query->join('p.grupoTramites', 'gt');
        $query->where('t.id = :tramiteId')->setParameter(':tramiteId', $tramiteId);
        $query->orderBy('gt.horizonte', 'DESC');
        return $query->getQuery()->setMaxResults(1)->getOneOrNullResult();
    }

    /**
     * Comprobar si un tramite está ya en un grupo del punto de atención
     *
     * @param integer $puntoAtencionId identificador único de punto de atención
     * @param integer $tramiteId identificador único de trámite
     * @return integer
     */
    public function checkTramiteGrupoTramite($puntoAtencionId, $tramiteId)
    {
        $query = $this->getRepository()->createQueryBuilder('t');
        $query->select('count(gt.id)');
        $query->join('t.puntosAtencion', 'pt');
        $query->join('pt.puntoAtencion', 'p');
        $query->join('p.grupoTramites', 'gt');
        $query->join('gt.tramites', 'tr');
        $query->where('p.id = :puntoAtencionId')->setParameter('puntoAtencionId', $puntoAtencionId);
        $query->andWhere('tr.id = :tramiteId')->setParameter('tramiteId', $tramiteId);
        $total = $query->getQuery()->getSingleScalarResult();
        return (int) $total;
    }

    /**
     * Provincias donde se puede realizar el trámite
     *
     * @param integer $tramiteId identificador único de trámite
     * @param integer $limit Cantidad máxima de registros a retornar
     * @param integer $offset Cantidad de registros a saltar
     * @return mixed
     */
    public function findProvinciasPaginate($limit, $offset, $tramiteId, $pdaId = null)
    {

        $query = $this->getRepository()->createQueryBuilder('t');
        $query->select(['p.id', 'p.nombre']);
        $query->join('t.puntosAtencion', 'pt', 'WITH', 'pt.estado = 1');
        $query->join('pt.puntoAtencion', 'pa');
        $query->join('pa.provincia', 'p');
        $query->where('t.id = :tramiteId')->setParameter(':tramiteId', $tramiteId);
        if($pdaId){
            $query->andWhere('pa.id = :pdaId')->setParameter(':pdaId', $pdaId);
        }
        $query->orderBy('p.nombre', 'ASC');
        $query->groupBy('p.id');
        $query->setFirstResult($offset);
        $query->setMaxResults($limit);
        return $query->getQuery()->getResult();
    }

    /**
     * Total de provincias donde se puede realizar el trámite
     *
     * @param integer $tramiteId identificador único de trámite
     * @return integer
     */
    public function getTotalProvincias($tramiteId)
    {
        $query = $this->getRepository()->createQueryBuilder('t');
        $query->select('p.id');
        $query->join('t.puntosAtencion', 'pt');
        $query->join('pt.puntoAtencion', 'pa');
        $query->join('pa.provincia', 'p');
        $query->where('t.id = :tramiteId')->setParameter(':tramiteId', $tramiteId);
        $query->groupBy('p.id');
        $total = $query->getQuery()->getResult();
        return count($total);
    }

    /**
     * Localidades donde se puede realizar el trámite
     *
     * @param integer $limit Cantidad máxima de registros a retornar
     * @param integer $offset Cantidad de registros a saltar
     * @param integer $tramiteId identificador único de trámite
     * @param integer $provinciaId identificador único de provincia
     * @return mixed
     */
    public function findLocalidadesPaginate($limit, $offset, $tramiteId, $provinciaId)
    {
        $query = $this->getRepository()->createQueryBuilder('t');
        $query->select(['l.id', 'l.nombre']);
        $query->join('t.puntosAtencion', 'pt');
        $query->join('pt.puntoAtencion', 'pa');
        $query->join('pa.provincia', 'p');
        $query->join('pa.localidad', 'l');
        $query->where('t.id = :tramiteId')->setParameter(':tramiteId', $tramiteId);
        $query->andWhere('p.id = :provinciaId')->setParameter(':provinciaId', $provinciaId);
        $query->orderBy('l.nombre', 'ASC');
        $query->groupBy('p.id', 'l.id');
        $query->setFirstResult($offset);
        $query->setMaxResults($limit);
        return $query->getQuery()->getResult();
    }

    /**
     * Total de localidades donde se puede realizar el trámite
     *
     * @param integer $tramiteId identificador único de trámite
     * @param integer $provinciaId identificador único de provincia
     * @return integer
     */
    public function getTotalLocalidades($tramiteId, $provinciaId)
    {
        $query = $this->getRepository()->createQueryBuilder('t');
        $query->select('l.id');
        $query->join('t.puntosAtencion', 'pt');
        $query->join('pt.puntoAtencion', 'pa');
        $query->join('pa.provincia', 'p');
        $query->join('pa.localidad', 'l');
        $query->where('t.id = :tramiteId')->setParameter(':tramiteId', $tramiteId);
        $query->andWhere('p.id = :provinciaId')->setParameter(':provinciaId', $provinciaId);
        $query->groupBy('p.id', 'l.id');
        $total = $query->getQuery()->getResult();
        return count($total);
    }

    /**
     * Totem: obtener grupotramite ID a partir de un trámite y un punto de atención
     *
     * @param integer $tramiteId Identificador único del trámite del que se quiere obtener información
     * @param integer $puntoAtencionId Identificador único del punto de atención
     * @return mixed
     */
    public function getGrupotramiteIdByPunto($puntoAtencionId, $tramiteId){
        $query = $this->getRepository()->createQueryBuilder('t');
        $query->select( 'g.id grupoTramiteId');
        $query->join('t.grupoTramites','g');
        $query->andwhere('g.puntoAtencion = :puntoAtencion')->setParameter('puntoAtencion', $puntoAtencionId);
        $query->andwhere('t.id = :tramiteId')->setParameter('tramiteId', $tramiteId);
        return $query->getQuery()->getOneOrNullResult();
    }

    /**
     * Totem: Listado de trámites paginado por punto de atención
     *
     * @param integer $puntoAtencionId Identificador único de punto de atención
     * @param string $term filtro de tramites por nombre
     * @param integer $limit Cantidad máxima de registros a retornar
     * @param integer $offset Cantidad de registros a saltar
     * @return mixed
     */
    public function findTramitesTotemByPuntoNombrePaginate($puntoAtencionId, $term, $limit, $offset)
    {
        $query = $this->getRepository()->createQueryBuilder('t');
        $query->select('t.id, t.nombre', 'g.id grupoTramiteId','c.id as idCat','t.miArgentina');
        $query->andWhere('t.visibilidad = 1');
        $query->leftjoin('t.categoriaTramite', 'c');
        $query->join('t.grupoTramites','g');
        $query->andwhere('g.puntoAtencion = :puntoAtencion')->setParameter('puntoAtencion', $puntoAtencionId);
        if ($term && ! empty($term)) {
            $query->andWhere('lower(t.nombre) LIKE :data')->setParameter('data', '%' . strtolower($term) . '%');
        }
        $query->setFirstResult($offset);
        $query->setMaxResults($limit);
        $query->orderBy('t.nombre', 'ASC');
        return $query->getQuery()->getResult();
    }

    /**
     * Totem: Número total de trámites por punto y nombre
     *
     * @param integer $puntoAtencionId ID punto de atención
     * @param string $term filtro de tramites por nombre
     * @return int
     */
    public function getTotalTotemPuntoNombre($puntoAtencionId, $term)
    {
        $query = $this->getRepository()->createQueryBuilder('t');
        $query->select('count(t.id)');
        $query->where('t.visibilidad = 1');
        $query->join('t.grupoTramites','g');
        $query->andwhere('g.puntoAtencion = :puntoAtencion')->setParameter('puntoAtencion', $puntoAtencionId);
        if ($term && ! empty($term)) {
            $query->andWhere('lower(t.nombre) LIKE :data')->setParameter('data', '%' . strtolower($term) . '%');
        }
        $total = $query->getQuery()->getSingleScalarResult();
        return (int) $total;
    }

    /**
     * Totem: Listado de trámites paginado por categoría
     *
     * @param integer $puntoAtencionId ID punto de atención
     * @param integer $categoriaId ID punto de la categoria
     * @param integer $limit Cantidad máxima de registros a retornar
     * @param integer $offset Cantidad de registros a saltar
     * @return mixed
     */
    public function findTramitesTotemByCategoriaPaginate($puntoAtencionId, $categoriaId, $limit, $offset)
    {
        $query = $this->getRepository()->createQueryBuilder('t');
        $query->select('t.id, t.nombre', 'g.id grupoTramiteId, pt.permite_prioridad');
        $query->join('t.categorias', 'c');
        $query->where('c.id = :categoriaId')->setParameter('categoriaId', $categoriaId);
        $query->andwhere('c.puntoAtencion = :puntoAtencion')->setParameter('puntoAtencion', $puntoAtencionId);
        $query->andWhere('t.visibilidad = 1');
        $query->join('t.grupoTramites','g');
        $query->andwhere('g.puntoAtencion = :puntoAtencion')->setParameter('puntoAtencion', $puntoAtencionId);
        $query->join('t.puntosAtencion', 'pt');
        $query->andWhere('pt.puntoAtencion = :puntoAtencion');
        $query->andWhere('pt.estado = 1');
        $query->setFirstResult($offset);
        $query->setMaxResults($limit);
        $query->orderBy('t.nombre', 'ASC');
        return $query->getQuery()->getResult();
    }

    /**
     * Totem: Número total de trámites por categoría
     *
     * @param integer $puntoAtencionId identificador único de punto de atención
     * @param integer $categoriaId  identificador único de categoría
     * @return int
     */
    public function getTotalTotemCategoria($puntoAtencionId, $categoriaId)
    {
        $query = $this->getRepository()->createQueryBuilder('t');
        $query->select('count(t.id)');
        $query->join('t.categorias', 'c');
        $query->where('c.id = :categoriaId')->setParameter('categoriaId', $categoriaId);
        $query->andwhere('c.puntoAtencion = :puntoAtencion')->setParameter('puntoAtencion', $puntoAtencionId);
        $query->join('t.grupoTramites','g');
        $query->andwhere('g.puntoAtencion = :puntoAtencion')->setParameter('puntoAtencion', $puntoAtencionId);
        $query->andWhere('t.visibilidad = 1');
        $query->join('t.puntosAtencion', 'pt');
        $query->andWhere('pt.puntoAtencion = :puntoAtencion');
        $query->andWhere('pt.estado = 1');
        $total = $query->getQuery()->getSingleScalarResult();
        return (int) $total;
    }
    


}
