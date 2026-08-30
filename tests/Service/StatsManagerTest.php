<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Entity\Game;
use App\Entity\User;
use App\Enum\GameType;
use App\Service\StatsManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class StatsManagerTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;
    private StatsManager $statsManager;

    protected function setUp(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $this->entityManager = $container->get(EntityManagerInterface::class);
        $this->statsManager = $container->get(StatsManager::class);

        $this->entityManager->createQuery('DELETE FROM App\Entity\Game')->execute();
        $this->entityManager->createQuery('DELETE FROM App\Entity\Stats')->execute();
        $this->entityManager->createQuery('DELETE FROM App\Entity\User')->execute();
    }

    public function testUpdateStatsIncrementsGamesAndScore(): void
    {
        $user = new User('test@test.com', 'password', 'tester', ['ROLE_USER']);
        $this->entityManager->persist($user);
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