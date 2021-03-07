<?php

namespace App\Repository;

use App\Entity\Chauffeurs;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Chauffeurs|null find($id, $lockMode = null, $lockVersion = null)
 * @method Chauffeurs|null findOneBy(array $criteria, array $orderBy = null)
 * @method Chauffeurs[]    findAll()
 * @method Chauffeurs[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChauffeursRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Chauffeurs::class);
    }

    // /**
    //  * @return Chauffeurs[] Returns an array of Chauffeurs objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Chauffeurs
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
