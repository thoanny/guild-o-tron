<?php

namespace App\Repository;

use App\Entity\Guild;
use App\Entity\GuildMember;
use App\Entity\GuildTag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Guild|null find($id, $lockMode = null, $lockVersion = null)
 * @method Guild|null findOneBy(array $criteria, array $orderBy = null)
 * @method Guild[]    findAll()
 * @method Guild[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GuildRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Guild::class);
    }

    public function findGuildForDirectory() {
      return $this->createQueryBuilder('g')
          // ->select('g.tag', 'g.name', 'g.description', 'g.slug', 'g.emblem', 'g.introduction', 'g.guild_tags')
          // ->leftJoin(GuildTag::class, 'gt', 'WITH', 'gt.guild_id = g.id')
          ->andWhere('g.display_in_directory = :directory')
          ->setParameter('directory', 1)
          ->orderBy('g.id', 'DESC')
          ->setMaxResults(10)
          ->getQuery()
          ->getResult()
      ;
    }

    public function findMyGuilds($name) {
      return $this->createQueryBuilder('g')
          ->select('g.tag', 'g.name', 'g.slug')
          ->leftJoin(GuildMember::class, 'gm', 'WITH', 'gm.guild = g.id')
          ->andWhere("gm.members LIKE :name")
          ->setParameter('name', "%{$name}%")
          ->orderBy('g.name', 'ASC')
          ->getQuery()
          ->getResult()
      ;
    }

    // /**
    //  * @return Guild[] Returns an array of Guild objects
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
    public function findOneBySomeField($value): ?Guild
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
