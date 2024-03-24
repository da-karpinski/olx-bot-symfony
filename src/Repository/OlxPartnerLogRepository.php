<?php

namespace App\Repository;

use App\Entity\OlxPartnerLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<OlxPartnerLog>
 *
 * @method OlxPartnerLog|null find($id, $lockMode = null, $lockVersion = null)
 * @method OlxPartnerLog|null findOneBy(array $criteria, array $orderBy = null)
 * @method OlxPartnerLog[]    findAll()
 * @method OlxPartnerLog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OlxPartnerLogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OlxPartnerLog::class);
    }

    //    /**
    //     * @return OlxPartnerLog[] Returns an array of OlxPartnerLog objects
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

    //    public function findOneBySomeField($value): ?OlxPartnerLog
    //    {
    //        return $this->createQueryBuilder('o')
    //            ->andWhere('o.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
