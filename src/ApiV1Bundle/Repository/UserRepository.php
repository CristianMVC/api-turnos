<?php
namespace ApiV1Bundle\Repository;

use ApiV1Bundle\Entity\User;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 * UserRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UserRepository extends ApiRepository
{

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    private function getRepository()
    {
        return $this->getEntityManager()->getRepository('ApiV1Bundle:User');
    }
}
