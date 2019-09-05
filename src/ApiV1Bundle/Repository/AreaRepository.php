<?php
namespace ApiV1Bundle\Repository;

use Doctrine\ORM\Query;

/**
 * Class AreaRepository
 * @package ApiV1Bundle\Repository
 *
 * Esta clase es generada por el ORM. Se pueden agregar debajo los métodos que se requieran
 */

class AreaRepository extends ApiRepository
{

    /**
     * @return \Doctrine\ORM\EntityRepository
     */

    public function getRepository()
    {
        return $this->getEm()->getRepository('ApiV1Bundle:Area');
    }

    /**
     * Listado de areas paginado
     *
     * @param integer $organismoId identificador único de organismo
     * @param integer $areaId identificador único de area
     * @param integer $limit Cantidad máxima de registros a retornar
     * @param integer $offset Cantidad de registros a saltar
     * @return array
     */
    public function findAllPaginate($organismoId, $areaId, $limit, $offset)
    {
        $query = $this->getRepository()->createQueryBuilder('a');
        $query->select(['a.id', 'a.nombre', 'a.abreviatura']);
        $query->join('a.organismo', 'o');
        $query->where('o.id = :organismoId')->setParameter('organismoId', $organismoId);
        if (isset($areaId)) {
            $query->andWhere('a.id = :areaId')->setParameter('areaId', $areaId);
        }
        $query->setFirstResult($offset);
        $query->setMaxResults($limit);
        $query->orderBy('a.nombre', 'ASC');

        return $query->getQuery()->getResult();
    }

    /**
     *  Número total de areas
     *
     * @param integer $organismoId identificador único de organismo
     * @param integer $areaId identificador único de area
     * @return integer
     */
    public function getTotal($organismoId, $areaId)
    {
        $query = $this->getRepository()->createQueryBuilder('a');
        $query->select('count(a.id)');
        $query->join('a.organismo', 'o');
        if ($organismoId) {
            $query->where('o.id = :organismoId')->setParameter('organismoId', $organismoId);
        }
        if (isset($areaId)) {
            $query->andWhere('a.id = :areaId')->setParameter('areaId', $areaId);
        }
        $total = $query->getQuery()->getSingleScalarResult();

        return (int) $total;
    }

    /**
     * Listado de puntos de atención de un area
     *
     * @param integer $areaId identificador único de area
     * @param integer $limit Cantidad máxima de registros a retornar
     * @param integer $offset Cantidad de registros a saltar
     * @param integer $puntoAtencionId identificador único de punto de Atención
     * @return array
     */
    public function findPuntosAtencionPaginate($areaId, $limit, $offset, $puntoAtencionId = null)
    {
        $query = $this->getRepository()->createQueryBuilder('a');
        $query->select([
            'p.id',
            'p.nombre',
            'a.nombre as area',
            'r.nombre as provincia',
            'l.nombre as localidad',
            'p.direccion',
            'r.id as latitud',
            'l.id as longitud'
        ]);
        $query->join('a.puntosAtencion', 'p');
        $query->join('p.localidad', 'l');
        $query->join('l.provincia', 'r');
        $query->where('a.id = :areaId')->setParameter('areaId', $areaId);

        if (!is_null($puntoAtencionId)) {
            $query->andWhere('p.id = :puntoAtencionId')->setParameter('puntoAtencionId', $puntoAtencionId);
        }

        $query->setFirstResult($offset);
        $query->setMaxResults($limit);

        return $query->getQuery()->getResult();
    }



    public function findTramitesOrganismo($idOrganismo, $limit, $offset) {

        $query = $this->getRepository()->createQueryBuilder('a');
        $query->select('t.id', 't.nombre','t.descripcion', 't.duracion', 't.visibilidad','t.org','pt.id as punto_atencion_id','gt.id as grupo_tramite_id, c.id as idCat','t.miArgentina');
        $query->join('a.tramites', 't');
        $query->leftjoin('t.categoriaTramite', 'c');
        $query->join('a.organismo', 'o');
        $query->leftJoin('t.puntosAtencion', 'pt', 'WITH', 'pt.estado = 1');
        $query->leftJoin('t.grupoTramites', 'gt');
        $query->where('o.id = :orgId')->setParameter('orgId', $idOrganismo);
        $query->andWhere('t.org = 1');
        $query->setFirstResult($offset);
        $query->setMaxResults($limit);
        $query->orderBy('t.nombre', 'ASC');
        $query->distinct();
        return $query->getQuery()->getResult(Query::HYDRATE_ARRAY);

    }



    /**
     * Listado de trámites de un area
     *
     * @param integer $areaId identificador único de area
     * @param integer $limit Cantidad máxima de registros a retornar
     * @param integer $offset Cantidad de registros a saltar
     * @return array
     */
    public function findTramitesPaginate($areaId, $limit, $offset)
    {

        $query = $this->getRepository()->createQueryBuilder('a');

        $query->select('t.id', 't.nombre','t.descripcion', 't.duracion', 't.visibilidad','t.org', 'pt.id as punto_atencion_id','gt.id as grupo_tramite_id','c.id as idCat','t.miArgentina');
        $query->join('a.tramites', 't');
        $query->leftjoin('t.categoriaTramite', 'c');
        $query->leftJoin('t.puntosAtencion', 'pt', 'WITH', 'pt.estado = 1');
        $query->leftJoin('t.grupoTramites', 'gt');
        $query->where('a.id = :areaId')->setParameter('areaId', $areaId);
        $query->groupBy('t.id');
        $query->setFirstResult($offset);
        $query->setMaxResults($limit);
        $query->orderBy('t.nombre', 'ASC');

        return $query->getQuery()->getResult();
    }

    /**
     * Total puntos atención del area
     *
     * @param integer $areaId identificador único de area
     * @param integer $puntoAtencionId identificador único de punto de Atención
     * @return int
     */
    public function getTotalPuntosAtencion($areaId, $puntoAtencionId)
    {
        $query = $this->getRepository()->createQueryBuilder('a');
        $query->select('count(p.id)');
        $query->join('a.puntosAtencion', 'p');
        $query->where('a.id = :areaId')->setParameter('areaId', $areaId);
        if (!is_null($puntoAtencionId)) {
            $query->andWhere('p.id = :puntoAtencionId')->setParameter('puntoAtencionId', $puntoAtencionId);
        }
        $total = $query->getQuery()->getSingleScalarResult();

        return (int) $total;
    }

    /**
     * Busca un trámite del área
     *
     * @param string $nombre Nombre el trámite a buscar
     * @param integer $id Identificador único del área
     *
     * @return mixed
     */

    public function search($nombre, $id)
    {
        $query = $this->getRepository()->createQueryBuilder('a');
        $query->select(['t.id', 't.nombre', 't.visibilidad']);
        $query->join('a.tramites', 't');
        $query->where('a.id = :areaId')->setParameter('areaId', $id);
        $query->andWhere('LOWER(t.nombre) LIKE :data')->setParameter('data', '%' . strtolower($nombre) . '%');
        return $query->getQuery()->getResult();
    }

    /**
     * Total Tramites del area
     *
     * @param integer $areaId identificador único de area
     * @return integer
     */
    public function getTotalTramites($areaId)
    {
        $query = $this->getRepository()->createQueryBuilder('a');
        $query->select('count(t.id)');
        $query->join('a.tramites', 't');
        $query->where('a.id = :areaId')->setParameter('areaId', $areaId);
        $total = $query->getQuery()->getSingleScalarResult();

        return (int) $total;
    }


    public function getTotalTramitesOrg($idOrganismo)
    {
        $query = $this->getRepository()->createQueryBuilder('a');
        $query->select('t.id', 't.nombre', 't.duracion', 't.visibilidad','t.org');
        $query->join('a.tramites', 't');
        $query->join('a.organismo', 'o');
        $query->where('o.id = :orgId')->setParameter('orgId', $idOrganismo);
        $query->andWhere('t.org = 1');

        $total = count($query->getQuery()->getResult());

        return (int) $total;
    }


    /**
     * Buscar área por nombre
     *
     * @param string $nombre Nombre del área a buscar
     * @param integer $limit Cantidad máxima de registros a retornar
     * @param integer $offset Cantidad de registros a saltar
     * @return mixed
     */
    public function searchAreaByNombreAbreviatura($nombre, $offset, $limit)
    {
        $query = $this->getRepository()->createQueryBuilder('a');
        $query->select(['a.id', 'a.nombre', 'a.abreviatura']);
        $query->where('LOWER(a.nombre) LIKE :nombre');
        $query->orWhere('LOWER(a.abreviatura) LIKE :nombre');
        $query->setParameter('nombre', '%' . strtolower($nombre) . '%');
        $query->setFirstResult($offset);
        $query->setMaxResults($limit);
        return $query->getQuery()->getResult();
    }

    /**
     * Total de áreas que cumplen una condición
     *
     * @param string $nombre Nombre o abreviatura del área a buscar
     * @return int
     */
    public function getTotalSearch($nombre)
    {
        $query = $this->getRepository()->createQueryBuilder('a');
        $query->select('count(a.id)');
        $query->where('LOWER(a.nombre) LIKE :nombre');
        $query->orWhere('LOWER(a.abreviatura) LIKE :nombre');
        $query->setParameter('nombre', '%' . strtolower($nombre) . '%');
        $total = $query->getQuery()->getSingleScalarResult();

        return (int) $total;
    }
}
