<?php

namespace App\Service;

use App\Entity\Game;
use App\Entity\User;
use App\Enum\GameType;
use App\Repository\CityRepository;
use Doctrine\ORM\EntityManagerInterface;

class GameStart
{
    public function __construct(
        private CityRepository $repository,
        private EntityManagerInterface $manager
    ) {}


    public function start(User $user, GameType $type, ?string $countryCode = null): ?Game
    {
        $city = $type === GameType::FREE ? $this->repository->getRandomCityFromCountry($countryCode) : $this->repository->getRandomCity();

        if (null === $city) {
            return null;
        }

        $game = new Game($user, $city, $type);

        $this->manager->persist($game);
        $this->manager->flush();

        return $game;
    }
}
