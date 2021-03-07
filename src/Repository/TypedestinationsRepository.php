<?php

namespace App\Repository;

use App\Entity\Typedestinations;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Typedestinations|null find($id, $lockMode = null, $lockVersion = null)
 * @method Typedestinations|null findOneBy(array $criteria, array $orderBy = null)
 * @method Typedestinations[]    findAll()
 * @method Typedestinations[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TypedestinationsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Typedestinations::class);
    }

    // /**
    //  * @return Typedestinations[] Returns an array of Typedestinations objects
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
    public function findOneBySomeField($value): ?Typedestinations
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
