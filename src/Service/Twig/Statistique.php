<?php
namespace App\Service\Twig;

use App\Entity\Clients;
use App\Entity\Vehicules;
use Doctrine\ORM\EntityManagerInterface;

class Statistique

{
    private $em;


    public function __construct(EntityManagerInterface $em){

        $this->em = $em;
    }
    public function nombre_clients(){

        $repo = $this->em->getRepository(Clients::class)->createQueryBuilder('c');
        return $repo->select('count(c.id)')
            ->getQuery()
            ->getSingleScalarResult();
        ;

    }

    public function nombre_vehicule(){

        $repo = $this->em->getRepository(Vehicules::class)->createQueryBuilder('v');
        return $repo->select('count(v.id)')
            ->getQuery()
            ->getSingleScalarResult();
        ;

    }
}