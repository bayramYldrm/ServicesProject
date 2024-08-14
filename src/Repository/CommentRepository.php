<?php

namespace App\Repository;

use App\Entity\Comment;
use App\Entity\UserActiv;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

/**
 * @extends ServiceEntityRepository<Comment>
 *
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    /**
     * Find comments by UserActiv entity, ordered by creation date.
     *
     * @param UserActiv $userActiv
     * @param array $orderBy
     * @param int|null $limit
     * @param int|null $offset
     *
     * @return Comment[]
     */
    public function findByUserActiv(UserActiv $userActiv, array $orderBy = ['createdAt' => 'DESC'], ?int $limit = null, ?int $offset = null): array
    {
        $qb = $this->createQueryBuilder('c')
            ->andWhere('c.userActiv = :userActiv')
            ->setParameter('userActiv', $userActiv)
            ->orderBy('c.createdAt', $orderBy['createdAt'])
            ->setMaxResults($limit)
            ->setFirstResult($offset);

        return $qb->getQuery()->getResult();
    }

    /**
     * Find a single comment by its ID and UserActiv.
     *
     * @param int $id
     * @param UserActiv $userActiv
     *
     * @return Comment|null
     */
    public function findOneByIdAndUserActiv(int $id, UserActiv $userActiv): ?Comment
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.id = :id')
            ->andWhere('c.userActiv = :userActiv')
            ->setParameter('id', $id)
            ->setParameter('userActiv', $userActiv)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Get all comments that have been reported more than a given number of times.
     *
     * @param int $minReports
     *
     * @return Comment[]
     */
    public function findReportedComments(int $minReports): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.reported >= :minReports')
            ->setParameter('minReports', $minReports)
            ->orderBy('c.reported', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
