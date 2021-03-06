<?php

namespace App\Repository;

use App\Entity\Tag;
use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use http\Exception\BadMethodCallException;
use Psr\Log\LoggerInterface;

/**
 * @method Task|null find($id, $lockMode = null, $lockVersion = null)
 * @method Task|null findOneBy(array $criteria, array $orderBy = null)
 * @method Task[]    findAll()
 * @method Task[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskRepository extends ServiceEntityRepository
{
    private $logger;

    public function __construct(ManagerRegistry $registry, LoggerInterface $logger)
    {
        parent::__construct($registry, Task::class);
        $this->logger = $logger;
    }

    // /**
    //  * @return Task[] Returns an array of Task objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Task
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function add(Task $task): bool
    {
        $query = $this->createQueryBuilder('s');
        $query->select('MAX(s.relativeId) AS maxValue');
        $query->andWhere('s.project = :project')->setParameter('project', $task->getProject());
        $maxId = $query->getQuery()->getResult()[0]['maxValue'];
        $task->setRelativeId($maxId + 1);
        try {
            $this->getEntityManager()->persist($task);
            $this->getEntityManager()->flush($task);
            return true;
        } catch (ORMException | UniqueConstraintViolationException | OptimisticLockException $e) {
            return false;
        }
    }

    public function update(Task $user): string
    {
        try {
            $this->getEntityManager()->flush($user);
            return false;
        } catch (ORMException | UniqueConstraintViolationException | OptimisticLockException $e) {
            $this->logger->error($e->getMessage());
            return $e->getMessage();
        }
    }

}
