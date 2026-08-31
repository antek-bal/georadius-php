<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Stats;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Stats>
 */
class StatsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Stats::class);
    }

    public function getStatsByUser(User $user): ?Stats
    {
        if (null === $user->getId()) {
            return null;
        }

        return $this->createQueryBuilder('s')
            ->andWhere('s.user = :val')
            ->setParameter('val', $user)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
