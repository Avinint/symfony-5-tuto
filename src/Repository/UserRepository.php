<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findAllWithMoreThan5Posts()
    {
        return $this->getAllWithMoreThan5Posts()
            ->getQuery()
            ->getResult();
    }

    public function findAllWithMoreThan5PostsExceptUser(User $user)
    {
        return $this->getAllWithMoreThan5Posts()
            ->where('u != :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    private function getAllWithMoreThan5Posts() : QueryBuilder
    {
        $qb = $this->createQueryBuilder('u');
        return $qb->select('u')
            ->innerJoin('u.posts', 'mp')
            ->groupBy('u')
            ->having('count(mp) > 5');
    }

}
