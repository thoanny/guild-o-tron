<?php

namespace App\Repository;

use App\Entity\GuildActivity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method GuildActivity|null find($id, $lockMode = null, $lockVersion = null)
 * @method GuildActivity|null findOneBy(array $criteria, array $orderBy = null)
 * @method GuildActivity[]    findAll()
 * @method GuildActivity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GuildActivityRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, GuildActivity::class);
    }

    // /**
    //  * @return GuildActivity[] Returns an array of GuildActivity objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?GuildActivity
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
