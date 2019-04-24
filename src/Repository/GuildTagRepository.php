<?php

namespace App\Repository;

use App\Entity\GuildTag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method GuildTag|null find($id, $lockMode = null, $lockVersion = null)
 * @method GuildTag|null findOneBy(array $criteria, array $orderBy = null)
 * @method GuildTag[]    findAll()
 * @method GuildTag[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GuildTagRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, GuildTag::class);
    }

    // /**
    //  * @return GuildTag[] Returns an array of GuildTag objects
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
    public function findOneBySomeField($value): ?GuildTag
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
