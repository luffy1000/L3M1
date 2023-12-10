<?php

namespace App\Repository;

use App\Entity\StripeSessionId;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<StripeSessionId>
 *
 * @method StripeSessionId|null find($id, $lockMode = null, $lockVersion = null)
 * @method StripeSessionId|null findOneBy(array $criteria, array $orderBy = null)
 * @method StripeSessionId[]    findAll()
 * @method StripeSessionId[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StripeSessionIdRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StripeSessionId::class);
    }

//    /**
//     * @return StripeSessionId[] Returns an array of StripeSessionId objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?StripeSessionId
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
