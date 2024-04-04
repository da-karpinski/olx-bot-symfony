<?php

namespace App\Repository;

use App\Entity\IntegrationLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<IntegrationLog>
 *
 * @method IntegrationLog|null find($id, $lockMode = null, $lockVersion = null)
 * @method IntegrationLog|null findOneBy(array $criteria, array $orderBy = null)
 * @method IntegrationLog[]    findAll()
 * @method IntegrationLog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IntegrationLogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, IntegrationLog::class);
    }

    //    /**
    //     * @return IntegrationLog[] Returns an array of IntegrationLog objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('i')
    //            ->andWhere('i.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('i.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?IntegrationLog
    //    {
    //        return $this->createQueryBuilder('i')
    //            ->andWhere('i.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
