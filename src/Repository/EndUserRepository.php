<?php

namespace App\Repository;

use App\Entity\EndUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method EndUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method EndUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method EndUser[]    findAll()
 * @method EndUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EndUserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EndUser::class);
    }

    public function loadUserByUsername($username)
    {
        return $this->findOneBy(['username' => $username]);
    }

    public function loadUserByemail($username)
    {
        return $this->findOneBy(['email' => $username]);
    }

    public function loadUserByChosenName($chosenName)
    {
        return $this->findOneBy(['chosenName' => $chosenName]);
    }

    public function findBetween(\Datetime $from, \Datetime $to)
    {
        $startDate = new \DateTime($from->format("Y-m-d")." 00:00:00");
        $endDate   = new \DateTime($to->format("Y-m-d")." 00:00:00");


        return $this->createQueryBuilder('eu')
            ->andWhere('eu.createdAt BETWEEN :from AND :to')
            ->setParameter('from', $startDate )
            ->setParameter('to', $endDate)
            ->getQuery()
            ->getResult();
    }
}
