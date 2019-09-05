<?php
namespace ApiV1Bundle\Repository;

/**
 * Class LocalidadRepository
 * @package ApiV1Bundle\Repository
 *
 * Esta clase es generada por el ORM. Se pueden agregar debajo los métodos que se requieran
 */
class LocalidadRepository extends ApiRepository
{

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    private function getRepository()
    {
        return $this->getEntityManager()->getRepository('ApiV1Bundle:Localidad');
    }

    /**
     * Listado de organismos paginado
     *
     * @param integer $limit Cantidad máxima de registros a retornar
     * @param integer $offset Cantidad de registros a saltar
     * @param integer $provinciaId identificador único de provincia
     * @return mixed
     */
    public function findAllPaginated($provinciaId, $offset, $limit)
    {
        $query = $this->getRepository()->createQueryBuilder('l');
        $query->select([
            'l.id',
            'l.nombre'
        ]);
        $query->join('l.provincia', 'p');
        $query->where('p.id = :provinciaId')->setParameter('provinciaId', $provinciaId);
        $query->setFirstResult($offset);
        $query->setMaxResults($limit);
        $query->orderBy('l.nombre', 'ASC');

        return $query->getQuery()->getResult();
    }

    /**
     * Número total de organismos
     *
     * @param integer $provinciaId identificador único de provincia     *
     * @return int
     */
    public function getTotal($provinciaId)
    {
        $query = $this->getRepository()->createQueryBuilder('l');
        $query->select('count(l.id)');
        $query->join('l.provincia', 'p');
        $query->where('p.id = :provinciaId')->setParameter('provinciaId', $provinciaId);

        $total = $query->getQuery()->getResult();

        return count($total);
    }

    /**
     * Busqueda de localidad
     * @param integer $id identificador único de Provincia
     * @param string $qry texto a buscar en el nombre de la localidad
     * @return mixed
     */
    public function busqueda($id, $qry)
    {
        $query = $this->getRepository()->createQueryBuilder('l');
        $query->select([
            'l.id',
            'l.nombre'
        ]);
        $query->join('l.provincia', 'p');
        $query->where('p.id = :id')->setParameter('id', $id);
        $query->andWhere('LOWER(l.nombre) LIKE :qry')->setParameter('qry', strtolower($qry) . '%');
        return $query->getQuery()->getResult();
    }
}
