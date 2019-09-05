<?php

namespace ApiV1Bundle\Repository;
use ApiV1Bundle\Entity\Usuario;
use ApiV1Bundle\Entity\User;

/**
 * Class UsuarioRepository
 * @package ApiV1Bundle\Repository
 */
class UsuarioRepository extends ApiRepository
{
    /**
     * @return \Doctrine\ORM\EntityRepository
     */

    private function getRepository()
    {
        return $this->getEntityManager()->getRepository('ApiV1Bundle:Usuario');
    }

    /**
     * Obtiene todos los usuarios con un resultado paginado
     *
     * @param integer $limit Cantidad máxima de registros a retornar
     * @param integer $offset Cantidad de registros a saltar
     * @param object $usuarioLogueado objeto Usuario
     * @param array $params
     * @return mixed
     */
    public function findAllPaginate($offset, $limit, $usuarioLogueado, $params = [])
    {
        $organismoID = $usuarioLogueado->getOrganismoId();
        $areaID = $usuarioLogueado->getAreaId();
        $puntoAtencionID = $usuarioLogueado->getPuntoAtencionId();

        $query = $this->getRepository()->createQueryBuilder('u');

        $query->where('u.id <> :uid')->setParameter('uid', $usuarioLogueado->getId());
        $query->leftjoin("u.organismo","organismo");
        $query->leftjoin("u.area","area");
        $query->leftjoin("u.puntoAtencion","punto_atencion");
        $query->innerjoin("u.user","user");

        if (isset($organismoID)) {
            $query->andWhere('u.organismo = :organismo')->setParameter('organismo', $organismoID);
        }

        if (isset($areaID)) {
            $query->andWhere('u.area = :area')->setParameter('area', $areaID);
        }

        if (isset($puntoAtencionID)) {
            $query->andWhere('u.puntoAtencion = :puntoAtencion')->setParameter('puntoAtencion', $puntoAtencionID);
        }
        
        $nombre = isset($params['nombre'])?trim($params['nombre']):null;
        $rol = isset($params['rol'])?trim($params['rol']):null;
        if($rol && $rol > 0){
            $query->andWhere('user.rol = :rol')->setParameter('rol', $rol);
        }
        if ($nombre != "") {
            $query->andWhere('u.nombre LIKE :nombre '
                    . ' OR u.apellido LIKE :nombre '
                    . ' OR CONCAT(u.nombre,\' \',u.apellido) LIKE :nombre '
                    . ' OR ( user.rol = '.User::ROL_ORGANISMO.' AND organismo.nombre LIKE :nombre ) '
                    . ' OR ( user.rol = '.User::ROL_ORGANISMO_AUX.' AND organismo.nombre LIKE :nombre ) '
                    . ' OR ( user.rol = '.User::ROL_AREA.' AND area.nombre LIKE :nombre ) '
                    . ' OR ( user.rol = '.User::ROL_PUNTOATENCION.' AND punto_atencion.nombre LIKE :nombre ) '
                    . ' OR ( user.rol = '.User::ROL_AGENTE.' AND punto_atencion.nombre LIKE :nombre ) '
                    . ' ')->setParameter('nombre', '%'.$nombre.'%');
        }
        $nolimit = isset($params['nolimit'])?trim($params['nolimit']):null;
        if($nolimit != -1){
            $query->setFirstResult($offset);
            $query->setMaxResults($limit);
        }
        
        $query->orderBy('u.user', 'ASC');
        return $query->getQuery()->getResult();
    }

    /**
     * Obtiene el total de usuarios
     *
     * @param object $usuarioLogueado objeto Usuario
     * @param array $params
     * @return number
     */
    public function getTotal($usuarioLogueado, $params = [])
    {
        $organismoID = $usuarioLogueado->getOrganismoId();
        $areaID = $usuarioLogueado->getAreaId();
        $puntoAtencionID = $usuarioLogueado->getPuntoAtencionId();

        $query = $this->getRepository()->createQueryBuilder('u');
        $query->select('count(u.id)');
        $query->where('u.id <> :uid')->setParameter('uid', $usuarioLogueado->getId());
        $query->leftjoin("u.organismo","organismo");
        $query->leftjoin("u.area","area");
        $query->leftjoin("u.puntoAtencion","punto_atencion");
        $query->innerjoin("u.user","user");
        if (isset($organismoID)) {
            $query->andWhere('u.organismo = :organismo')->setParameter('organismo', $organismoID);
        }

        if (isset($areaID)) {
            $query->andWhere('u.area = :area')->setParameter('area', $areaID);
        }

        if (isset($puntoAtencionID)) {
            $query->andWhere('u.puntoAtencion = :puntoAtencion')->setParameter('puntoAtencion', $puntoAtencionID);
        }
        
        $nombre = isset($params['nombre'])?trim($params['nombre']):null;
        $rol = isset($params['rol'])?trim($params['rol']):null;
        if($rol && $rol > 0){
            $query->andWhere('user.rol = :rol')->setParameter('rol', $rol);
        }
        if ($nombre != "") {
            $query->andWhere('u.nombre LIKE :nombre '
                    . ' OR u.apellido LIKE :nombre '
                    . ' OR CONCAT(u.nombre,\' \',u.apellido) LIKE :nombre '
                    . ' OR ( user.rol = '.User::ROL_ORGANISMO.' AND organismo.nombre LIKE :nombre ) '
                    . ' OR ( user.rol = '.User::ROL_ORGANISMO_AUX.' AND organismo.nombre LIKE :nombre ) '
                    . ' OR ( user.rol = '.User::ROL_AREA.' AND area.nombre LIKE :nombre ) '
                    . ' OR ( user.rol = '.User::ROL_PUNTOATENCION.' AND punto_atencion.nombre LIKE :nombre ) '
                    . ' OR ( user.rol = '.User::ROL_AGENTE.' AND punto_atencion.nombre LIKE :nombre ) '
                    . ' ')->setParameter('nombre', '%'.$nombre.'%');
        }
        
        $total = $query->getQuery()->getSingleScalarResult();
        return (int) $total;
    }
}
