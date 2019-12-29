<?php

namespace App\Repository;

use App\Entity\TaskList;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;

/**
 * @method TaskList|null find($id, $lockMode = null, $lockVersion = null)
 * @method TaskList|null findOneBy(array $criteria, array $orderBy = null)
 * @method TaskList[]    findAll()
 * @method TaskList[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskListRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TaskList::class);
    }

    // /**
    //  * @return TaskList[] Returns an array of TaskList objects
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
    public function findOneBySomeField($value): ?TaskList
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */



    public function add(TaskList $taskList): bool
    {
        $query = $this->createQueryBuilder('s');
        $query->select('MAX(s.relativeId) AS maxValue');
        $query->andWhere('s.project = :project')->setParameter('project', $taskList->getProject());
        $maxId = $query->getQuery()->getResult()[0]['maxValue'];
        $taskList->setRelativeId($maxId + 1);

        try {
            $this->getEntityManager()->persist($taskList);
            $this->getEntityManager()->flush($taskList);
            return true;
        } catch (ORMException | UniqueConstraintViolationException | OptimisticLockException $e) {
            return false;
        }
    }
}