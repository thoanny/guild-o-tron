<?php

namespace App\Repository;

use App\Entity\GuildRaid;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method GuildRaid|null find($id, $lockMode = null, $lockVersion = null)
 * @method GuildRaid|null findOneBy(array $criteria, array $orderBy = null)
 * @method GuildRaid[]    findAll()
 * @method GuildRaid[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GuildRaidRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, GuildRaid::class);
    }

    // /**
    //  * @return GuildRaid[] Returns an array of GuildRaid objects
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
    public function findOneBySomeField($value): ?GuildRaid
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
