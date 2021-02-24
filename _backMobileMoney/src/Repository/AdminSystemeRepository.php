<?php

namespace App\Repository;

use App\Entity\AdminSysteme;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AdminSysteme|null find($id, $lockMode = null, $lockVersion = null)
 * @method AdminSysteme|null findOneBy(array $criteria, array $orderBy = null)
 * @method AdminSysteme[]    findAll()
 * @method AdminSysteme[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdminSystemeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AdminSysteme::class);
    }

    // /**
    //  * @return AdminSysteme[] Returns an array of AdminSysteme objects
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
    public function findOneBySomeField($value): ?AdminSysteme
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
