<?php

namespace App\Repository;

use App\Entity\AchievementGuide;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method AchievementGuide|null find($id, $lockMode = null, $lockVersion = null)
 * @method AchievementGuide|null findOneBy(array $criteria, array $orderBy = null)
 * @method AchievementGuide[]    findAll()
 * @method AchievementGuide[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AchievementGuideRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, AchievementGuide::class);
    }

    // /**
    //  * @return AchievementGuide[] Returns an array of AchievementGuide objects
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
    public function findOneBySomeField($value): ?AchievementGuide
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
