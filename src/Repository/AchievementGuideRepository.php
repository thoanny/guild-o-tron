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

    public function findByLocale($locale)
    {
      return $this->createQueryBuilder('g')
        ->select("g.achievement AS id, g.{$locale} AS url")
        ->andWhere("g.{$locale} != ''")
        ->orderBy('g.achievement', 'ASC')
        ->getQuery()
        ->getResult()
      ;
    }

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
