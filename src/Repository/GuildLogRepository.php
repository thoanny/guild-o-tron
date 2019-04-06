<?php

namespace App\Repository;

use App\Entity\GuildLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method GuildLog|null find($id, $lockMode = null, $lockVersion = null)
 * @method GuildLog|null findOneBy(array $criteria, array $orderBy = null)
 * @method GuildLog[]    findAll()
 * @method GuildLog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GuildLogRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, GuildLog::class);
    }

    // /**
    //  * @return GuildLog[] Returns an array of GuildLog objects
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
    public function findOneBySomeField($value): ?GuildLog
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
