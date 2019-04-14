<?php

namespace App\Repository;

use App\Entity\GuildMember;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method GuildMember|null find($id, $lockMode = null, $lockVersion = null)
 * @method GuildMember|null findOneBy(array $criteria, array $orderBy = null)
 * @method GuildMember[]    findAll()
 * @method GuildMember[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GuildMemberRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, GuildMember::class);
    }

    public function findMyGuilds(User $user) {
      $q =  $this->createQueryBuilder('gm')
            ->andWhere("gm.members LIKE :name")
            ->setParameter('name', "%{$user->getAccountName()}%")
            ->getQuery();

      return $q->execute();
    }

    // /**
    //  * @return GuildMember[] Returns an array of GuildMember objects
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
    public function findOneBySomeField($value): ?GuildMember
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
