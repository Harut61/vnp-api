<?php

namespace App\Repository;

use App\Entity\TranscodingProfile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TranscodingProfile|null find($id, $lockMode = null, $lockVersion = null)
 * @method TranscodingProfile|null findOneBy(array $criteria, array $orderBy = null)
 * @method TranscodingProfile[]    findAll()
 * @method TranscodingProfile[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TranscodingProfileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TranscodingProfile::class);
    }

    // /**
    //  * @return TranscodingProfile[] Returns an array of TranscodingProfile objects
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
    public function findOneBySomeField($value): ?TranscodingProfile
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
