<?php
namespace App\Repository;

use App\Entity\UserActiv;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class UserActivRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserActiv::class);
    }

    /**
     * @param string $query
     * @return UserActiv[]
     */
    public function findByQuery(string $query): array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.projectName LIKE :query OR u.owner LIKE :query')
            ->setParameter('query', '%' . $query . '%')
            ->getQuery()
            ->getResult();
    }
}
