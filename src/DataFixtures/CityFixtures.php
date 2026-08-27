<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

use App\Entity\City;

class CityFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $cities = [
            [
                'name' => 'Warsaw',
                'countryCode' => 'PL',
                'latitude' => 52.22977,
                'longitude' => 21.01178,
                'isStartingCity' => true
            ],
            [
                'name' => 'Barcelona',
                'countryCode' => 'ES',
                'latitude' => 41.38879,
                'longitude' => 2.15899,
                'isStartingCity' => true
            ],
            [
                'name' => 'New York',
                'countryCode' => 'US',
                'latitude' => 40.71427,
                'longitude' => -74.00597,
                'isStartingCity' => true
            ],
            [
                'name' => 'Norilsk',
                'countryCode' => 'RU',
                'latitude' => 69.3535,
                'longitude' => 88.2027,
                'isStartingCity' => false
            ],
            [
                'name' => 'Lagos',
                'countryCode' => 'NG',
                'latitude' => 6.45407,
                'longitude' => 3.39467,
                'isStartingCity' => true
            ]
        ];

        foreach ($cities as $city) {
            $cityExample = new City();
            $cityExample->setName($city['name']);
            $cityExample->setCountryCode(($city['countryCode']));
            $cityExample->setLatitude($city['latitude']);
            $cityExample->setLongitude($city['longitude']);
            $cityExample->setIsStartingCity($city['isStartingCity']);

            $manager->persist($cityExample);
        }

        $manager->flush();
    }
}
