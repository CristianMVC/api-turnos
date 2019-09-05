<?php
namespace ApiV1Bundle\Repository;

/**
 * DiaNoLaborableRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class DiaNoLaborableRepository extends ApiRepository
{

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    private function getRepository()
    {
        return $this->getEntityManager()->getRepository('ApiV1Bundle:DiaNoLaborable');
    }

    /**
     * Elimina los días no habiles por fecha
     *
     * @param date $fecha fecha a eliminar
     * @return mixed|\Doctrine\DBAL\Driver\Statement|array|NULL
     */
    public function deleteDiaNoLaborable($fecha)
    {
        $query = $this->getRepository()->createQueryBuilder('d');
        $query->delete();
        $query->where('d.fecha = :fecha')->setParameter('fecha', $fecha);
        return $query->getQuery()->execute();
    }

    /**
     * Determinar si Es día no laborable para un punto de atención
     *
     * @param date $fecha fecha
     * @param object $puntoAtencion objeto PuntoAtencion
     * @return bool
     */
    public function isDiaNoLaborable($fecha, $puntoAtencion)
    {
        $query = $this->getRepository()->createQueryBuilder('d');
        $query->where('d.fecha = :fecha')->setParameter('fecha', $fecha);
        $query->andWhere('d.puntoAtencion = :puntoAtencion')->setParameter('puntoAtencion', $puntoAtencion);
        $result = $query->getQuery()->getResult();

        return !empty($result);
    }
}