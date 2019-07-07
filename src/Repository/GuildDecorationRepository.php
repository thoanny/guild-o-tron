<?php

namespace App\Repository;

use App\Entity\GuildDecoration;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method GuildDecoration|null find($id, $lockMode = null, $lockVersion = null)
 * @method GuildDecoration|null findOneBy(array $criteria, array $orderBy = null)
 * @method GuildDecoration[]    findAll()
 * @method GuildDecoration[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GuildDecorationRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, GuildDecoration::class);
    }

    // /**
    //  * @return GuildDecoration[] Returns an array of GuildDecoration objects
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
    public function findOneBySomeField($value): ?GuildDecoration
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
