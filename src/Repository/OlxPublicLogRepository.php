<?php

namespace App\Repository;

use App\Entity\OlxPublicLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<OlxPublicLog>
 *
 * @method OlxPublicLog|null find($id, $lockMode = null, $lockVersion = null)
 * @method OlxPublicLog|null findOneBy(array $criteria, array $orderBy = null)
 * @method OlxPublicLog[]    findAll()
 * @method OlxPublicLog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OlxPublicLogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OlxPublicLog::class);
    }

    //    /**
    //     * @return OlxPublicLog[] Returns an array of OlxPublicLog objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('o')
    //            ->andWhere('o.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('o.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?OlxPublicLog
    //    {
    //        return $this->createQueryBuilder('o')
    //            ->andWhere('o.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
