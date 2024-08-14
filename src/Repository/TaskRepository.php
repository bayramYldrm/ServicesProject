<?php

namespace App\Repository;

use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Task>
 *
 * @method Task|null find($id, $lockMode = null, $lockVersion = null)
 * @method Task|null findOneBy(array $criteria, array $orderBy = null)
 * @method Task[]    findAll()
 * @method Task[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    /**
     * @return Task[] Returns an array of Task objects
     */
    public function findByProject(int $projectId): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.project = :projectId')
            ->setParameter('projectId', $projectId)
            ->orderBy('t.created_at', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Task[] Returns an array of Task objects with a specific status
     */
    public function findByStatus(string $status): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.status = :status')
            ->setParameter('status', $status)
            ->orderBy('t.created_at', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Task[] Returns an array of Task objects assigned to a specific user
     */
    public function findByAssignedUser(int $userId): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.assignedTo = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('t.created_at', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Task[] Returns an array of Task objects with a specific priority
     */
    public function findTasksWithPriority(int $priority): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.priority = :priority')
            ->setParameter('priority', $priority)
            ->orderBy('t.created_at', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return array Returns an array of task counts grouped by status
     */
    public function countTasksByStatus(): array
    {
        return $this->createQueryBuilder('t')
            ->select('t.status, COUNT(t.id) as taskCount')
            ->groupBy('t.status')
            ->getQuery()
            ->getResult();
    }
}
