<?php

declare(strict_types=1);

namespace App\Tests\Repository;

use App\Entity\City;
use App\Repository\CityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CityTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;
    private CityRepository $repository;

    protected function setUp(): void
    {
        self::bootKernel();

        $container = static::getContainer();

        $this->entityManager = $container->get('doctrine')->getManager();
        $this->repository = $container->get(CityRepository::class);

        $this->entityManager->createQuery('DELETE FROM App\Entity\City')->execute();
    }

    public function testGetRandomCityWithPossibleStartingCities(): void
    {
        $this->loadTestCities();
        $city = $this->repository->getRandomCity();

        $this->assertNotNull($city);
        $this->assertTrue($city->isStartingCity());
    }

    public function testGetRandomCityWithoutPossiblieStartingCities(): void
    {
        $city = $this->repository->getRandomCity();

        $this->assertNull($city);
    }

    public function testGetRandomCityWithPossibleStartingCitiesFromCounry(): void
    {
        $this->loadTestCities();
        $city = $this->repository->getRandomCityFromCountry('PL');

        $this->assertNotNull($city);
        $this->assertEquals($city->getCountryCode(), 'PL');
        $this->assertTrue($city->isStartingCity());
    }

    public function testGetRandomCityWithoutPossibleStartingCitiesFromCounry(): void
    {
        $city = $this->repository->getRandomCityFromCountry('PL');

        $this->assertNull($city);
    }

    public function testFindCityByNameWithMatch(): void
    {
        $this->loadTestCities();
        $city = $this->repository->findCityByName('Warsaw');

        $this->assertTrue($city);
    }

    public function testFindCityByNameWithoutMatch(): void
    {
        $this->loadTestCities();
        $city = $this->repository->findCityByName('Berlin');

        $this->assertFalse($city);
    }

    public function testFindCityByNameWithEmptyDatabase(): void
    {
        $city = $this->repository->findCityByName('Warsaw');

        $this->assertFalse($city);
    }

    private function loadTestCities(): void
    {
        $warsaw = new City();
        $warsaw->setName('Warsaw');
        $warsaw->setCountryCode('PL');
        $warsaw->setLatitude(52.2297);
        $warsaw->setLongitude(21.0122);
        $warsaw->setIsStartingCity(true);

        $norilsk = new City();
        $norilsk->setName('Norilsk');
        $norilsk->setCountryCode('RU');
        $norilsk->setLatitude(69.3535);
        $norilsk->setLongitude(88.2027);
        $norilsk->setIsStartingCity(false);

        $this->entityManager->persist($warsaw);
        $this->entityManager->persist($norilsk);
        $this->entityManager->flush();
    }
}
