<?php

namespace App\Repository;

use App\Entity\StoryQaIssuesType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method StoryQaIssuesType|null find($id, $lockMode = null, $lockVersion = null)
 * @method StoryQaIssuesType|null findOneBy(array $criteria, array $orderBy = null)
 * @method StoryQaIssuesType[]    findAll()
 * @method StoryQaIssuesType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StoryQaIssuesTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StoryQaIssuesType::class);
    }

    // /**
    //  * @return StoryQaIssuesType[] Returns an array of StoryQaIssuesType objects
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
    public function findOneBySomeField($value): ?StoryQaIssuesType
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
