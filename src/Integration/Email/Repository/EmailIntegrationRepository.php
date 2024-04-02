<?php

namespace App\Integration\Email\Repository;

use App\Integration\Email\Entity\EmailIntegration;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EmailIntegration>
 *
 * @method EmailIntegration|null find($id, $lockMode = null, $lockVersion = null)
 * @method EmailIntegration|null findOneBy(array $criteria, array $orderBy = null)
 * @method EmailIntegration[]    findAll()
 * @method EmailIntegration[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmailIntegrationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EmailIntegration::class);
    }

    //    /**
    //     * @return EmailIntegration[] Returns an array of EmailIntegration objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('e.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?EmailIntegration
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
