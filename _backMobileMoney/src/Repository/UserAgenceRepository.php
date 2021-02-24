<?php

namespace App\Repository;

use App\Entity\UserAgence;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserAgence|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserAgence|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserAgence[]    findAll()
 * @method UserAgence[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserAgenceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserAgence::class);
    }

    // /**
    //  * @return UserAgence[] Returns an array of UserAgence objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?UserAgence
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
