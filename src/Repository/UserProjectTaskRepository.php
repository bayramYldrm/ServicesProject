<?php

namespace App\Repository;

use App\Entity\UserProjectTask;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserProjectTask>
 *
 * @method UserProjectTask|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserProjectTask|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserProjectTask[]    findAll()
 * @method UserProjectTask[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserProjectTaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserProjectTask::class);
    }

    /**
     * @return UserProjectTask[] Returns an array of UserProjectTask objects related to a specific project
     */
    public function findByProject(int $projectId): array
    {
        return $this->createQueryBuilder('upt')
            ->andWhere('upt.project = :projectId')
            ->setParameter('projectId', $projectId)
            ->orderBy('upt.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return UserProjectTask|null Returns a UserProjectTask object related to a specific project and task ID
     */
    public function findOneByProjectAndTask(int $projectId, int $taskId): ?UserProjectTask
    {
        return $this->createQueryBuilder('upt')
            ->andWhere('upt.project = :projectId')
            ->andWhere('upt.task = :taskId')
            ->setParameter('projectId', $projectId)
            ->setParameter('taskId', $taskId)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
