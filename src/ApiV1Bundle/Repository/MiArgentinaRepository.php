<?php
/**
 * Created by PhpStorm.
 * User: cristian
 * Date: 26/08/19
 * Time: 11:58
 */

namespace ApiV1Bundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\ORM\EntityManager;

/**
 *  MiArgentinaRepository
 **/
class MiArgentinaRepository
{

  private $em;

    public function __construct(EntityManager $em) {

      $this->em = $em;

    }


    public function getTotalMiArgentina($limit, $offset) {
        $query = $this->em->getRepository('ApiV1Bundle:Tramite')->createQueryBuilder('t');
        $query->select( 't.id', 't.nombre','t.descripcion', 't.duracion', ' a.nombre as area', 'o.nombre as organismo',  't.visibilidad','t.org',' c.id as idCat','t.miArgentina','t.excepcional', 'MAX(ptot.multiturno)  AS multiturno' , 'MAX(ptot.multiturno_cantidad)  AS max_multiturno_cantidad');
        $query->leftjoin('t.categoriaTramite', 'c');
        $query->join('t.areas', 'a');
        $query->join('a.organismo', 'o');
        $query->join('t.puntosAtencion', 'ptot');
        $query->where('t.miArgentina = 1');
        $query->andWhere('c.id is not null');
        $query->andWhere('t.visibilidad = 1');
        $query->groupBy('t.id');
        //$query->distinct();
        $query->setFirstResult($offset);
        $query->setMaxResults($limit);
        $query->orderBy('t.nombre', 'ASC');

        return $query->getQuery()->getResult();

    }



    public function findTramitesOrganismoMiArgentina($idOrganismo, $limit, $offset) {


        $query = $this->em->getRepository('ApiV1Bundle:Area')->createQueryBuilder('a');
        $query->select('t.id', 't.nombre','t.descripcion', 't.duracion', ' a.nombre as area', 'o.nombre as organismo',  't.visibilidad','t.org',' c.id as idCat','t.miArgentina','t.excepcional');
        $query->join('a.tramites', 't');
        $query->leftjoin('t.categoriaTramite', 'c');
        $query->join('a.organismo', 'o');
        $query->where('o.id = :orgId')->setParameter('orgId', $idOrganismo);
        $query->andWhere('t.org = 1');
        $query->andWhere('c.id is not null');
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
    public function findTramitesPaginateMiArgentina($areaId, $limit, $offset) {

        $query = $this->em->getRepository('ApiV1Bundle:Area')->createQueryBuilder('a');
        $query->select('t.id', 't.nombre','t.descripcion', 't.duracion', ' a.nombre as area', 'o.nombre as organismo',  't.visibilidad','t.org',' c.id as idCat','t.miArgentina','t.excepcional');
        $query->join('a.tramites', 't');
        $query->join('a.organismo', 'o');
        $query->leftjoin('t.categoriaTramite', 'c');
        $query->where('a.id = :areaId')->setParameter('areaId', $areaId);
        $query->andWhere('t.miArgentina = 1');
        $query->andWhere('c.id is not null');
        $query->groupBy('t.id');
        $query->setFirstResult($offset);
        $query->setMaxResults($limit);
        $query->orderBy('t.nombre', 'ASC');

        return $query->getQuery()->getResult();
    }



    public function findTramiteMiArgentina($id) {

        $query = $this->em->getRepository('ApiV1Bundle:Tramite')->createQueryBuilder('t');

        $query->select('t.id', 't.nombre','t.descripcion', 't.requisitos','f.campos'   ,'t.duracion', 't.visibilidad','t.org', 'pt.id as punto_atencion_id',
                                 'gt.id as grupo_tramite_id','c.id as idCat','t.miArgentina','pt.multiturno', 'a.nombre as area', 'o.nombre as organismo','t.excepcional');
        $query->leftjoin('t.categoriaTramite', 'c');
        $query->join('t.formulario','f');
        $query->join('t.areas', 'a');
        $query->join('a.organismo', 'o');
        $query->leftJoin('t.puntosAtencion', 'pt', 'WITH', 'pt.estado = 1');
        $query->leftJoin('t.grupoTramites', 'gt');
        $query->where('t.miArgentina = 1');
        $query->andWhere('c.id is not null');
        $query->andWhere('t.id = :id')->setParameter('id', $id);
        $query->distinct();

        return $query->getQuery()->getResult();

    }


    public function findFiltrarPorCategoriaMiArgentina($categoria, $limit, $offset) {

        $query = $this->em->getRepository('ApiV1Bundle:Tramite')->createQueryBuilder('t');

        $query->select('t.id', 't.nombre','t.descripcion', 't.duracion', ' a.nombre as area', 'o.nombre as organismo',  't.visibilidad','t.org',' c.id as idCat','t.miArgentina','t.excepcional');
        $query->leftjoin('t.categoriaTramite', 'c');
        $query->join('t.formulario','f');
        $query->join('t.areas', 'a');
        $query->join('a.organismo', 'o');
        $query->leftJoin('t.puntosAtencion', 'pt', 'WITH', 'pt.estado = 1');
        $query->leftJoin('t.grupoTramites', 'gt');
        $query->where('c.nombre IN (:cat)')->setParameter('cat', $categoria);
        $query->andWhere('t.miArgentina = 1');
        $query->andWhere('c.id is not null');
        $query->setFirstResult($offset);
        $query->setMaxResults($limit);
        $query->orderBy('t.nombre', 'ASC');

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

    public function filtrarPorEtiquetaMiArgentina($etiquetas, $offset, $limit) {
        $semi_filtred_array = [];
        $filter_array = [];

        $query = $this->em->getRepository('ApiV1Bundle:Etiqueta')->createQueryBuilder('e');
        $query->select('t.id','t.nombre','e.nombre as et','c.id as idCat','t.miArgentina' ,'a.nombre as area', 'o.nombre as organismo, t.excepcional','t.descripcion');
        $query->join('e.tramites', 't');
        $query->join('t.areas', 'a');
        $query->join('a.organismo', 'o');
        $query->leftjoin('t.categoriaTramite', 'c');
        $query->where('e.nombre IN (:etq)')->setParameter('etq', array_values($etiquetas));
        $query->andWhere('c.id is not null');
        $query->orderBy('t.id', 'ASC');
        $query->setFirstResult($offset);
        $query->setMaxResults($limit);
        $query->distinct();
        $no_filtred_array = $query->getQuery()->getResult();

        foreach($no_filtred_array as $nfr) {
            $semi_filtred_array[$nfr['id']][] = $nfr['et'] ;
            $semi_filtred_array[$nfr['id']]['nombre'] = $nfr['nombre'] ;
            $semi_filtred_array[$nfr['id']]['organismo'] = $nfr['organismo'] ;
            $semi_filtred_array[$nfr['id']]['area'] = $nfr['area'] ;
            $semi_filtred_array[$nfr['id']]['excepcional'] = $nfr['excepcional'] ;
            $semi_filtred_array[$nfr['id']]['idCat'] = $nfr['idCat'] ;
            $semi_filtred_array[$nfr['id']]['miArgentina'] = $nfr['miArgentina'] ;
            $semi_filtred_array[$nfr['id']]['descripcion'] = $nfr['descripcion'] ;
        }
        $i = 0;
        foreach($semi_filtred_array as $key => $sfa) {
            if(count(array_diff($etiquetas,$sfa)) == 0) {
                $filter_array[$i]['id'] = $key;
                $filter_array[$i]['nombre'] = $sfa['nombre'];
                $filter_array[$i]['organismo'] = $sfa['organismo'] ;
                $filter_array[$i]['area'] = $sfa['area'] ;
                $filter_array[$i]['excepcional'] = $sfa['excepcional'] ;
                $filter_array[$i]['idCat'] = $sfa['idCat'];
                $filter_array[$i]['miArgentina'] = $sfa['miArgentina'];
                $filter_array[$i]['descripcion'] = $sfa['descripcion'];
            }
            $i++;
        }

        return $filter_array;
    }



    public function listarCategoriasMiArgentina($limit, $offset) {

        $query = $this->em->getRepository('ApiV1Bundle:CategoriaTramite')->createQueryBuilder('c');
        $query->select('c.id','c.nombre');
        $query->join('c.tramite', 't');
        $query->distinct();
        $query->setFirstResult($offset);
        $query->setMaxResults($limit);

        return $query->getQuery()->getResult();
    }



    public function findTurnosPorSolicitanteMiArgentina($desde, $hasta, $cuil) {

        $query = $this->em->getRepository('ApiV1Bundle:Turno')->createQueryBuilder('t');
        $query->select('t', 'p , p.direccion as direccion_punto_atencion');
        $query->join('t.datosTurno', 'd');
        $query->join('t.puntoAtencion', 'p');
        $query->where('t.cuilSolicitante = :cuil')->setParameter('cuil', $cuil);
        $query->andWhere('t.fecha >= :desde')->setParameter('desde', $desde);
        $query->andWhere('t.fecha <= :hasta')->setParameter('hasta', $hasta);
        $query->andWhere('t.estado < 2');
        $query->andWhere('concat(t.fecha,t.hora) > :hoy')->setParameter('hoy', date("Y-m-dH:i:s"));
        $query->orWhere('d.cuil = :cuil')->setParameter('cuil', $cuil);
        $query->andWhere('t.estado < 2');
        $query->andWhere('concat(t.fecha,t.hora) > :hoy')->setParameter('hoy', date("Y-m-dH:i:s"));
        $query->distinct();
        $query->orderBy('t.id', 'ASC');


        return $query->getQuery()->getResult();

    }


    /**
     * Listado de trámites paginado
     *
     * @param String $term Consulta
     * @param integer $limit Cantidad máxima de registros a retornar
     * @param integer $offset Cantidad de registros a saltar
     * @return mixed
     */
    public function findAllTramiteByEtiquetaPaginateMiArgentina($etiquetas, $offset=0, $limit=10)
    {
        $query = $this->em->getRepository('ApiV1Bundle:Tramite')->createQueryBuilder('t');
        $query->distinct();
        $query->select('t.id, t.nombre', 't.excepcional',  'a.nombre as area', 'o.nombre as organismo', 'c.id as idCat', 't.descripcion','t.miArgentina');
        $query->leftjoin('t.categoriaTramite', 'c');
        $query->join('t.areas', 'a');
        $query->join('t.etiquetas', 'e');
        $query->join('a.organismo', 'o');
        $query->join('t.puntosAtencion', 'pt');
        foreach ($etiquetas as $key => $value) {
            $query->orWhere('LOWER(e.nombre) LIKE :data')->setParameter('data', '%' . strtolower($value) . '%');
        }
        foreach ($etiquetas as $key => $value) {
            $query->orWhere('LOWER(t.nombre) LIKE :data')->setParameter('data', '%' . strtolower($value) . '%');
        }
        $query->andWhere('t.visibilidad = 1');
        $query->andWhere('t.miArgentina = 1');
        $query->andWhere('pt.estado = 1');


        $query->setFirstResult($offset);
        $query->setMaxResults($limit);
        $query->orderBy('t.nombre', 'ASC');

        return $query->getQuery()->getResult();
    }


}