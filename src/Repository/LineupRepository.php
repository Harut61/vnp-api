<?php

namespace App\Repository;

use App\Entity\LineUp;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method LineUp|null find($id, $lockMode = null, $lockVersion = null)
 * @method LineUp|null findOneBy(array $criteria, array $orderBy = null)
 * @method LineUp[]    findAll()
 * @method LineUp[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LineupRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LineUp::class);
    }

    public function findBetween(\Datetime $from, \Datetime $to)
    {
        $startDate = new \DateTime($from->format("Y-m-d")." 00:00:00");
        $endDate   = new \DateTime($to->format("Y-m-d")." 00:00:00");


        return $this->createQueryBuilder('l')
            ->andWhere('l.createdAt BETWEEN :from AND :to')
            ->setParameter('from', $startDate )
            ->setParameter('to', $endDate)
            ->getQuery()
            ->getResult();
    }
}
