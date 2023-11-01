<?php

namespace App\Repository;

use App\Entity\SourceVideo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SourceVideo|null find($id, $lockMode = null, $lockVersion = null)
 * @method SourceVideo|null findOneBy(array $criteria, array $orderBy = null)
 * @method SourceVideo[]    findAll()
 * @method SourceVideo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SourceVideoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SourceVideo::class);
    }

    public function findBetween(\Datetime $from, \Datetime $to)
    {
        $startDate = new \DateTime($from->format("Y-m-d")." 00:00:00");
        $endDate   = new \DateTime($to->format("Y-m-d")." 23:59:59");


        return $this->createQueryBuilder('s')
            ->andWhere('s.createdAt BETWEEN :from AND :to')
            ->setParameter('from', $startDate )
            ->setParameter('to', $endDate)
            ->getQuery()
            ->getResult();
    }
}
