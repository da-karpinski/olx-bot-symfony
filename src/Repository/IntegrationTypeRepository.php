<?php

namespace App\Repository;

use App\Entity\IntegrationType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<IntegrationType>
 *
 * @method IntegrationType|null find($id, $lockMode = null, $lockVersion = null)
 * @method IntegrationType|null findOneBy(array $criteria, array $orderBy = null)
 * @method IntegrationType[]    findAll()
 * @method IntegrationType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IntegrationTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, IntegrationType::class);
    }

    //    /**
    //     * @return IntegrationType[] Returns an array of IntegrationType objects
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

    //    public function findOneBySomeField($value): ?IntegrationType
    //    {
    //        return $this->createQueryBuilder('i')
    //            ->andWhere('i.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
