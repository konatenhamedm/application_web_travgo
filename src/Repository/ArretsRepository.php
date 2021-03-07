<?php

namespace App\Repository;

use App\Entity\Arrets;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Arrets|null find($id, $lockMode = null, $lockVersion = null)
 * @method Arrets|null findOneBy(array $criteria, array $orderBy = null)
 * @method Arrets[]    findAll()
 * @method Arrets[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArretsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Arrets::class);
    }

    // /**
    //  * @return Arrets[] Returns an array of Arrets objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Arrets
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
