<?php

namespace App\Repository;

use App\Entity\City;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<City>
 */
class CityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, City::class);
    }

    public function getRandomCity(): ?City
    {
        $cities = $this->createQueryBuilder('c')
            ->andWhere('c.isStartingCity = :val')
            ->setParameter('val', true)
            ->getQuery()
            ->getResult();
        
        if (empty($cities)) {
            return null;
        }

        $randomIndex = array_rand($cities);
        return $cities[$randomIndex];
    }
}
