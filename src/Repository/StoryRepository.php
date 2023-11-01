<?php

namespace App\Repository;

use App\Entity\Story;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Story|null find($id, $lockMode = null, $lockVersion = null)
 * @method Story|null findOneBy(array $criteria, array $orderBy = null)
 * @method Story[]    findAll()
 * @method Story[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Story::class);
    }

    public function findBetween(\Datetime $from, \Datetime $to)
    {
        $startDate = new \DateTime($from->format("Y-m-d")." 00:00:00");
        $endDate   = new \DateTime($to->format("Y-m-d")." 00:00:00");


        return $this->createQueryBuilder('s')
            ->andWhere('s.createdAt BETWEEN :from AND :to')
            ->setParameter('from', $startDate )
            ->setParameter('to', $endDate)
            ->getQuery()
            ->getResult();
    }
}
