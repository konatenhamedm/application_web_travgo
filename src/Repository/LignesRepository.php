<?php

namespace App\Repository;

use App\Entity\Lignes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Lignes|null find($id, $lockMode = null, $lockVersion = null)
 * @method Lignes|null findOneBy(array $criteria, array $orderBy = null)
 * @method Lignes[]    findAll()
 * @method Lignes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LignesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Lignes::class);
    }

    // /**
    //  * @return Lignes[] Returns an array of Lignes objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Lignes
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
