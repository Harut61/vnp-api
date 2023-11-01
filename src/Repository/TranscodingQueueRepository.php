<?php

namespace App\Repository;

use App\Entity\TranscodingQueue;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TranscodingQueue|null find($id, $lockMode = null, $lockVersion = null)
 * @method TranscodingQueue|null findOneBy(array $criteria, array $orderBy = null)
 * @method TranscodingQueue[]    findAll()
 * @method TranscodingQueue[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TranscodingQueueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TranscodingQueue::class);
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

    /**
     * @param array $status
     * @return mixed
     */
    public function findTotalProcessInQueue(array $status)
    {
        $qb = $this->createQueryBuilder('tq');

        $qb
            ->select('count(tq.id) as count')
            ->where($qb->expr()->notIn('tq.status', $status))
            ->andWhere($qb->expr()->isNotNull('tq.status'));

        $count = $qb->getQuery()->getSingleScalarResult();

        return $count;
    }

    /**
     * @param array $status
     * @param null $year
     * @param null $month
     */
    public function findTotalPerDay(array $status, $year = null, $month = null, $day = null)
    {
        $qb = $this->createQueryBuilder('tq');

        if ($month === null) {
            $month = (int) date('m');
        }

        if ($year === null) {
            $year = (int) date('Y');
        }

        if ($day === null) {
            $day = (int) date('d');
        }

        $start = "$year-$month-$day 00:00:00";
        $end = "$year-$month-$day 23:59:59";

        $qb
            ->select('count(tq.id) as count')
            ->where($qb->expr()->in('tq.status', $status));
        $qb->andWhere( $qb->expr()->between('tq.createdAt',':start',':end'));
        $qb->setParameter('start', "$start");
        $qb->setParameter('end', "$end");

        return $qb->getQuery()->getSingleScalarResult();
    }
}
