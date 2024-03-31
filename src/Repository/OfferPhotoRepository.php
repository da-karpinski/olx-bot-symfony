<?php

namespace App\Repository;

use App\Entity\OfferPhoto;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<OfferPhoto>
 *
 * @method OfferPhoto|null find($id, $lockMode = null, $lockVersion = null)
 * @method OfferPhoto|null findOneBy(array $criteria, array $orderBy = null)
 * @method OfferPhoto[]    findAll()
 * @method OfferPhoto[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OfferPhotoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OfferPhoto::class);
    }

    //    /**
    //     * @return OfferPhoto[] Returns an array of OfferPhoto objects
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

    //    public function findOneBySomeField($value): ?OfferPhoto
    //    {
    //        return $this->createQueryBuilder('o')
    //            ->andWhere('o.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
