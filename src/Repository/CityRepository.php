<?php

declare(strict_types=1);

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
        $cities = $this->getStartingCities();

        if (empty($cities)) {
            return null;
        }

        $randomIndex = array_rand($cities);

        return $cities[$randomIndex];
    }

    public function getRandomCityFromCountry(string $countryCode): ?City
    {
        $cities = $this->getStartingCities();

        $filteredCities = array_filter($cities, function (City $city) use ($countryCode) {
            return $city->getCountryCode() === $countryCode;
        });

        if (empty($filteredCities)) {
            return null;
        }

        $randomIndex = array_rand($filteredCities);

        return $filteredCities[$randomIndex];
    }

    public function findCityByName(string $cityName): ?City
    {
        $city = $this->createQueryBuilder('c')
            ->andWhere('c.name = :val')
            ->setParameter('val', $cityName)
            ->getQuery()
            ->getOneOrNullResult();

        return $city instanceof City ? $city : null;
    }

    /**
     * @return list<City>
     */
    private function getStartingCities(): array
    {
        /** @var list<City> $cities */
        $cities = $this->createQueryBuilder('c')
            ->andWhere('c.isStartingCity = :val')
            ->setParameter('val', true)
            ->getQuery()
            ->getResult();

        return $cities;
    }
}
