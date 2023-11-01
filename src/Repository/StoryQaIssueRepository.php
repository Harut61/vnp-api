<?php

namespace App\Repository;

use App\Entity\StoryQaIssue;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method StoryQaIssue|null find($id, $lockMode = null, $lockVersion = null)
 * @method StoryQaIssue|null findOneBy(array $criteria, array $orderBy = null)
 * @method StoryQaIssue[]    findAll()
 * @method StoryQaIssue[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StoryQaIssueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StoryQaIssue::class);
    }

    // /**
    //  * @return StoryQaIssue[] Returns an array of StoryQaIssue objects
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
    public function findOneBySomeField($value): ?StoryQaIssue
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
