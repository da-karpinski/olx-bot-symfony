<?php

namespace App\Repository;

use App\Entity\OfferParameter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<OfferParameter>
 *
 * @method OfferParameter|null find($id, $lockMode = null, $lockVersion = null)
 * @method OfferParameter|null findOneBy(array $criteria, array $orderBy = null)
 * @method OfferParameter[]    findAll()
 * @method OfferParameter[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OfferParameterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OfferParameter::class);
    }

    //    /**
    //     * @return OfferParameter[] Returns an array of OfferParameter objects
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

    //    public function findOneBySomeField($value): ?OfferParameter
    //    {
    //        return $this->createQueryBuilder('o')
    //            ->andWhere('o.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
