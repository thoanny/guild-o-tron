<?php

namespace App\Repository;

use App\Entity\GuildTreasury;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method GuildTreasury|null find($id, $lockMode = null, $lockVersion = null)
 * @method GuildTreasury|null findOneBy(array $criteria, array $orderBy = null)
 * @method GuildTreasury[]    findAll()
 * @method GuildTreasury[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GuildTreasuryRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, GuildTreasury::class);
    }

    // /**
    //  * @return guildTreasury[] Returns an array of guildTreasury objects
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
    public function findOneBySomeField($value): ?guildTreasury
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
