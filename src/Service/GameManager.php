<?php

namespace App\Service;

use App\Entity\Game;
use App\Entity\User;
use App\Enum\GameType;
use App\Repository\CityRepository;
use Doctrine\ORM\EntityManagerInterface;

class GameManager
{
    private const BASE_RADIUS = 100;

    public function __construct(
        private CityRepository $repository,
        private EntityManagerInterface $manager,
        private DistanceCalculator $calculator,
        private StatsManager $statsManager,
    ) {
    }

    public function start(User $user, GameType $type, ?string $countryCode = null): ?Game
    {
        $city = GameType::FREE === $type ? $this->repository->getRandomCityFromCountry($countryCode) : $this->repository->getRandomCity();

        if (null === $city) {
            return null;
        }

        $game = new Game($user, $city, $type);

        $this->save($game);

        return $game;
    }

    public function guess(string $cityName, Game $game): bool
    {
        if ($game->getAttemptsLeft() <= 0) {
            return false;
        }

        $city = $this->repository->findCityByName($cityName);

        if (null === $city) {
            return false;
        }

        $startingCity = $game->getStartingCity();
        $round = $game->getCurrentRound();
        $attempts = $game->getAttemptsLeft();

        $distance = $this->calculator->calculate($city->getLatitude(), $city->getLongitude(), $startingCity->getLatitude(), $startingCity->getLongitude());
        $currentRadius = self::BASE_RADIUS * $round;

        if ($distance >= $currentRadius - self::BASE_RADIUS && $distance < $currentRadius) {
            $game->setCurrentRound(++$round);
            $game->setAttemptsLeft(3);
            $this->save($game);

            return true;
        }

        if (--$attempts < 1) {
            $game->setAttemptsLeft(0);
            $this->end($game, $round - 1);

            return false;
        }

        $game->setAttemptsLeft($attempts);
        $this->save($game);

        return false;
    }

    public function end(Game $game, int $score): void
    {
        $start = $game->getPlayedAt();
        $end = new \DateTimeImmutable();
        $duration = $end->getTimestamp() - $start->getTimestamp();

        $game
            ->setScore($score)
            ->setDurationSec($duration)
            ->setMaxRadius($score * self::BASE_RADIUS);

        $this->statsManager->updateStats($game->getUser(), $game);

        $this->save($game);
    }

    private function save(Game $game): void
    {
        $this->manager->persist($game);
        $this->manager->flush();
    }
}
