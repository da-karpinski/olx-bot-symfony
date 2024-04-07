<?php

namespace App\Integration\Telegram\Repository;

use App\Integration\Telegram\Entity\TelegramIntegration;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TelegramIntegration>
 *
 * @method TelegramIntegration|null find($id, $lockMode = null, $lockVersion = null)
 * @method TelegramIntegration|null findOneBy(array $criteria, array $orderBy = null)
 * @method TelegramIntegration[]    findAll()
 * @method TelegramIntegration[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TelegramIntegrationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TelegramIntegration::class);
    }

    //    /**
    //     * @return TelegramIntegration[] Returns an array of TelegramIntegration objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('t.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?TelegramIntegration
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
