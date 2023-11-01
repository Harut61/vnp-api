<?php

namespace App\Repository;

use App\Entity\EndUserPrefNewsSource;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method EndUserPrefNewsSource|null find($id, $lockMode = null, $lockVersion = null)
 * @method EndUserPrefNewsSource|null findOneBy(array $criteria, array $orderBy = null)
 * @method EndUserPrefNewsSource[]    findAll()
 * @method EndUserPrefNewsSource[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EndUserPrefNewsSourceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EndUserPrefNewsSource::class);
    }

    // /**
    //  * @return EndUserPrefNewsSource[] Returns an array of EndUserPrefNewsSource objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?EndUserPrefNewsSource
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
