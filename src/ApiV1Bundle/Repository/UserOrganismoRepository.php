<?php
namespace ApiV1Bundle\Repository;

/**
 * AgenteRepository
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UserOrganismoRepository extends ApiRepository
{

    /**
     *
     * @return \Doctrine\ORM\EntityRepository
     */
    private function getRepository()
    {
        return $this->getEntityManager()->getRepository('ApiV1Bundle:Agente');
    }
}