<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Entity\City;
use App\Entity\Game;
use App\Entity\User;
use App\Enum\GameType;
use App\Repository\CityRepository;
use App\Service\DistanceCalculator;
use App\Service\GameManager;
use App\Service\StatsManager;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class GameManagerTest extends TestCase
{
    private User $user;
    private City $startCity;

    protected function setUp(): void
    {
        $this->user = new User('player@example.com', 'password', 'Player');
        $this->startCity = new City('Warsaw', 'PL', 52.2297, 21.0122, true);
    }

    private function createGameManager(
        ?CityRepository $cityRepository = null,
        ?DistanceCalculator $calculator = null,
        ?StatsManager $statsManager = null,
        ?EntityManagerInterface $entityManager = null,
    ): GameManager {
        $em = $entityManager ?? $this->createStub(EntityManagerInterface::class);
        $cityRepo = $cityRepository ?? $this->createStub(CityRepository::class);
        $calc = $calculator ?? $this->createStub(DistanceCalculator::class);
        $stats = $statsManager ?? $this->createStub(StatsManager::class);

        return new GameManager($cityRepo, $em, $calc, $stats);
    }

    public function testStartGameSuccessfully(): void
    {
        $cityRepo = $this->createStub(CityRepository::class);
        $cityRepo->method('getRandomCityFromCountry')->willReturn($this->startCity);

        $gameManager = $this->createGameManager(cityRepository: $cityRepo);
        $game = $gameManager->start($this->user, GameType::FREE, 'PL');

        $this->assertInstanceOf(Game::class, $game);
        $this->assertSame($this->user, $game->getUser());
        $this->assertSame($this->startCity, $game->getStartingCity());
        $this->assertSame(GameType::FREE, $game->getType());
        $this->assertSame(1, $game->getCurrentRound());
        $this->assertSame(3, $game->getAttemptsLeft());
    }

    public function testStartGameReturnsNullWhenNoCityFound(): void
    {
        $cityRepo = $this->createStub(CityRepository::class);
        $cityRepo->method('getRandomCityFromCountry')->willReturn(null);

        $gameManager = $this->createGameManager(cityRepository: $cityRepo);
        $game = $gameManager->start($this->user, GameType::FREE, 'XX');

        $this->assertNull($game);
    }

    public function testGuessReturnsFalseWhenGameOver(): void
    {
        $game = new Game($this->user, $this->startCity, GameType::FREE);
        $game->setAttemptsLeft(0);

        $gameManager = $this->createGameManager();
        $result = $gameManager->guess('Radom', $game);

        $this->assertFalse($result);
    }

    public function testGuessReturnsFalseWhenCityNotFound(): void
    {
        $game = new Game($this->user, $this->startCity, GameType::FREE);

        $cityRepo = $this->createStub(CityRepository::class);
        $cityRepo->method('findCityByName')->willReturn(null);

        $gameManager = $this->createGameManager(cityRepository: $cityRepo);
        $result = $gameManager->guess('NieistniejaceMiasto', $game);

        $this->assertFalse($result);
        $this->assertSame(3, $game->getAttemptsLeft());
    }

    public function testGuessCorrectCityAdvancesToNextRound(): void
    {
        $game = new Game($this->user, $this->startCity, GameType::FREE);
        $game->setCurrentRound(1);
        $game->setAttemptsLeft(2);

        $guessedCity = new City('Radom', 'PL', 51.4027, 21.1471, false);

        $cityRepo = $this->createStub(CityRepository::class);
        $cityRepo->method('findCityByName')->willReturn($guessedCity);

        $calc = $this->createStub(DistanceCalculator::class);
        $calc->method('calculate')->willReturn(95.0);

        $gameManager = $this->createGameManager(cityRepository: $cityRepo, calculator: $calc);
        $result = $gameManager->guess('Radom', $game);

        $this->assertTrue($result);
        $this->assertSame(2, $game->getCurrentRound());
        $this->assertSame(3, $game->getAttemptsLeft());
    }

    public function testGuessWrongCityDecrementsAttempts(): void
    {
        $game = new Game($this->user, $this->startCity, GameType::FREE);
        $game->setCurrentRound(1);
        $game->setAttemptsLeft(3);

        $guessedCity = new City('Gdańsk', 'PL', 54.3520, 18.6466, false);

        $cityRepo = $this->createStub(CityRepository::class);
        $cityRepo->method('findCityByName')->willReturn($guessedCity);

        $calc = $this->createStub(DistanceCalculator::class);
        $calc->method('calculate')->willReturn(280.0);

        $gameManager = $this->createGameManager(cityRepository: $cityRepo, calculator: $calc);
        $result = $gameManager->guess('Gdańsk', $game);

        $this->assertFalse($result);
        $this->assertSame(1, $game->getCurrentRound());
        $this->assertSame(2, $game->getAttemptsLeft());
    }

    public function testGuessWrongCityEndsGame(): void
    {
        $game = new Game($this->user, $this->startCity, GameType::FREE);
        $game->setCurrentRound(3);
        $game->setAttemptsLeft(1);

        $guessedCity = new City('Szczecin', 'PL', 53.4285, 14.5528, false);

        $cityRepo = $this->createStub(CityRepository::class);
        $cityRepo->method('findCityByName')->willReturn($guessedCity);

        $calc = $this->createStub(DistanceCalculator::class);
        $calc->method('calculate')->willReturn(450.0);

        $statsManager = $this->createStub(StatsManager::class);

        $gameManager = $this->createGameManager(
            cityRepository: $cityRepo,
            calculator: $calc,
            statsManager: $statsManager
        );

        $result = $gameManager->guess('Szczecin', $game);

        $this->assertFalse($result);
        $this->assertSame(0, $game->getAttemptsLeft());
        $this->assertSame(2, $game->getScore());
        $this->assertSame(200, $game->getMaxRadius());
        $this->assertGreaterThanOrEqual(0, $game->getDurationSec());
    }

    public function testEndDirectlyUpdatesStatsAndSavesGame(): void
    {
        $game = new Game($this->user, $this->startCity, GameType::FREE);
        $statsManager = $this->createStub(StatsManager::class);

        $gameManager = $this->createGameManager(statsManager: $statsManager);
        $gameManager->end($game, 5);

        $this->assertSame(5, $game->getScore());
        $this->assertSame(500, $game->getMaxRadius());
    }
}
