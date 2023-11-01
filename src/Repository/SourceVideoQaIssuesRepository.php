<?php

namespace App\Repository;

use App\Entity\SourceVideoQaIssues;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SourceVideoQaIssues|null find($id, $lockMode = null, $lockVersion = null)
 * @method SourceVideoQaIssues|null findOneBy(array $criteria, array $orderBy = null)
 * @method SourceVideoQaIssues[]    findAll()
 * @method SourceVideoQaIssues[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SourceVideoQaIssuesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SourceVideoQaIssues::class);
    }

    // /**
    //  * @return SourceVideoQaIssues[] Returns an array of SourceVideoQaIssues objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SourceVideoQaIssues
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
