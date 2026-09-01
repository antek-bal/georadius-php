<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Entity\City;
use App\Entity\Game;
use App\Entity\User;
use App\Enum\GameType;
use App\Repository\StatsRepository;
use App\Service\StatsManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class StatsManagerTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;
    private StatsManager $statsManager;
    private StatsRepository $repository;

    protected function setUp(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $em = $container->get(EntityManagerInterface::class);
        assert($em instanceof EntityManagerInterface);
        $this->entityManager = $em;

        $repository = $container->get(StatsRepository::class);
        assert($repository instanceof StatsRepository);
        $this->repository = $repository;

        $this->statsManager = new StatsManager($this->repository, $this->entityManager);

        $this->entityManager->createQuery('DELETE FROM App\Entity\Game')->execute();
        $this->entityManager->createQuery('DELETE FROM App\Entity\Stats')->execute();
        $this->entityManager->createQuery('DELETE FROM App\Entity\User')->execute();
    }

    public function testUpdateStatsIncrementsGamesAndScore(): void
    {
        $user = new User('test@test.com', 'password', 'tester');
        $this->entityManager->persist($user);

        $city = new City('Warszawa', 'PL', 52.2297, 21.0122, true);
        $this->entityManager->persist($city);
        $this->entityManager->flush();

        $game = new Game($user, $city, GameType::FREE);
        $game->setScore(5);
        $this->entityManager->persist($game);
        $this->entityManager->flush();

        $this->statsManager->updateStats($user, $game);

        $stats = $user->getStats();

        $this->assertEquals(1, $stats->getGamesPlayed());
        $this->assertEquals(5, $stats->getHighScore());
    }
}
