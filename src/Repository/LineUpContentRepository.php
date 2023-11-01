<?php

namespace App\Repository;

use App\Entity\LineUpContent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method LineUpContent|null find($id, $lockMode = null, $lockVersion = null)
 * @method LineUpContent|null findOneBy(array $criteria, array $orderBy = null)
 * @method LineUpContent[]    findAll()
 * @method LineUpContent[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LineUpContentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LineUpContent::class);
    }

    // /**
    //  * @return LineUpContent[] Returns an array of LineUpContent objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?LineUpContent
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
