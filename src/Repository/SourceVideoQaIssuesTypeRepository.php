<?php

namespace App\Repository;

use App\Entity\SourceVideoQaIssuesType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SourceVideoQaIssuesType|null find($id, $lockMode = null, $lockVersion = null)
 * @method SourceVideoQaIssuesType|null findOneBy(array $criteria, array $orderBy = null)
 * @method SourceVideoQaIssuesType[]    findAll()
 * @method SourceVideoQaIssuesType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SourceVideoQaIssuesTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SourceVideoQaIssuesType::class);
    }

    // /**
    //  * @return SourceVideoQaIssuesType[] Returns an array of SourceVideoQaIssuesType objects
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
    public function findOneBySomeField($value): ?SourceVideoQaIssuesType
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
