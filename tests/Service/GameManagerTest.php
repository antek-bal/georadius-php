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
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class GameManagerTest extends TestCase
{
    private CityRepository&MockObject $cityRepository;
    private EntityManagerInterface&MockObject $entityManager;
    private DistanceCalculator&MockObject $distanceCalculator;
    private StatsManager&MockObject $statsManager;
    private GameManager $gameManager;

    private User $user;
    private City $startCity;

    protected function setUp(): void
    {
        $this->cityRepository = $this->createMock(CityRepository::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->distanceCalculator = $this->createMock(DistanceCalculator::class);
        $this->statsManager = $this->createMock(StatsManager::class);

        $this->gameManager = new GameManager(
            $this->cityRepository,
            $this->entityManager,
            $this->distanceCalculator,
            $this->statsManager
        );

        $this->user = new User('player@example.com', 'password', 'Player', ['ROLE_USER']);
        $this->startCity = new City('Warsaw', 'PL', 52.2297, 21.0122, true);
    }

    public function testStartGameSuccessfully(): void
    {
        $this->cityRepository
            ->expects($this->once())
            ->method('getRandomCityFromCountry')
            ->with('PL')
            ->willReturn($this->startCity);

        $this->entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(Game::class));

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $game = $this->gameManager->start($this->user, GameType::FREE, 'PL');

        $this->assertInstanceOf(Game::class, $game);
        $this->assertSame($this->user, $game->getUser());
        $this->assertSame($this->startCity, $game->getStartingCity());
        $this->assertSame(GameType::FREE, $game->getType());
        $this->assertSame(1, $game->getCurrentRound());
        $this->assertSame(3, $game->getAttemptsLeft());
    }

    public function testStartGameReturnsNullWhenNoCityFound(): void
    {
        $this->cityRepository
            ->expects($this->once())
            ->method('getRandomCityFromCountry')
            ->with('XX')
            ->willReturn(null);

        $this->entityManager->expects($this->never())->method('persist');
        $this->entityManager->expects($this->never())->method('flush');

        $game = $this->gameManager->start($this->user, GameType::FREE, 'XX');

        $this->assertNull($game);
    }

    public function testGuessReturnsFalseWhenGameOver(): void
    {
        $game = new Game($this->user, $this->startCity, GameType::FREE);
        $game->setAttemptsLeft(0);

        $this->cityRepository->expects($this->never())->method('findCityByName');

        $result = $this->gameManager->guess('Radom', $game);

        $this->assertFalse($result);
    }

    public function testGuessReturnsFalseWhenCityNotFound(): void
    {
        $game = new Game($this->user, $this->startCity, GameType::FREE);

        $this->cityRepository
            ->expects($this->once())
            ->method('findCityByName')
            ->with('NieistniejaceMiasto')
            ->willReturn(null);

        $result = $this->gameManager->guess('NieistniejaceMiasto', $game);

        $this->assertFalse($result);
        $this->assertSame(3, $game->getAttemptsLeft());
    }

    public function testGuessCorrectCityAdvancesToNextRound(): void
    {
        $game = new Game($this->user, $this->startCity, GameType::FREE);
        $game->setCurrentRound(1);
        $game->setAttemptsLeft(2);

        $guessedCity = new City('Radom', 'PL', 51.4027, 21.1471, false);

        $this->cityRepository
            ->expects($this->once())
            ->method('findCityByName')
            ->with('Radom')
            ->willReturn($guessedCity);

        $this->distanceCalculator
            ->expects($this->once())
            ->method('calculate')
            ->with(51.4027, 21.1471, 52.2297, 21.0122)
            ->willReturn(95.0);

        $this->entityManager->expects($this->once())->method('persist')->with($game);
        $this->entityManager->expects($this->once())->method('flush');

        $result = $this->gameManager->guess('Radom', $game);

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

        $this->cityRepository
            ->expects($this->once())
            ->method('findCityByName')
            ->with('Gdańsk')
            ->willReturn($guessedCity);

        $this->distanceCalculator
            ->expects($this->once())
            ->method('calculate')
            ->willReturn(280.0);

        $this->entityManager->expects($this->once())->method('persist')->with($game);
        $this->entityManager->expects($this->once())->method('flush');

        $result = $this->gameManager->guess('Gdańsk', $game);

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

        $this->cityRepository
            ->expects($this->once())
            ->method('findCityByName')
            ->with('Szczecin')
            ->willReturn($guessedCity);

        $this->distanceCalculator
            ->expects($this->once())
            ->method('calculate')
            ->willReturn(450.0);

        $this->statsManager
            ->expects($this->once())
            ->method('updateStats')
            ->with($this->user, $game);

        $this->entityManager->expects($this->once())->method('persist')->with($game);
        $this->entityManager->expects($this->once())->method('flush');

        $result = $this->gameManager->guess('Szczecin', $game);

        $this->assertFalse($result);
        $this->assertSame(0, $game->getAttemptsLeft());
        $this->assertSame(2, $game->getScore());
        $this->assertSame(200, $game->getMaxRadius());
        $this->assertGreaterThanOrEqual(0, $game->getDurationSec());
    }

    public function testEndDirectlyUpdatesStatsAndSavesGame(): void
    {
        $game = new Game($this->user, $this->startCity, GameType::FREE);

        $this->statsManager
            ->expects($this->once())
            ->method('updateStats')
            ->with($this->user, $game);

        $this->entityManager->expects($this->once())->method('persist')->with($game);
        $this->entityManager->expects($this->once())->method('flush');

        $this->gameManager->end($game, 5);

        $this->assertSame(5, $game->getScore());
        $this->assertSame(500, $game->getMaxRadius());
    }
}