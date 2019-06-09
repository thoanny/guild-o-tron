<?php

namespace App\Repository;

use App\Entity\GuildAchievement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method GuildAchievement|null find($id, $lockMode = null, $lockVersion = null)
 * @method GuildAchievement|null findOneBy(array $criteria, array $orderBy = null)
 * @method GuildAchievement[]    findAll()
 * @method GuildAchievement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GuildAchievementRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, GuildAchievement::class);
    }

    // /**
    //  * @return GuildAchievement[] Returns an array of GuildAchievement objects
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
    public function findOneBySomeField($value): ?GuildAchievement
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
