<?php

namespace App\Service;

use App\Entity\Game;
use App\Entity\User;
use App\Enum\GameType;
use App\Repository\StatsRepository;
use Doctrine\ORM\EntityManagerInterface;

class StatsManager
{
    public function __construct(
        private StatsRepository $repository,
        private EntityManagerInterface $manager,
    ) {
    }

    public function updateStats(User $user, Game $game): void
    {
        $stats = $this->repository->getStatsByUser($user);
        $gamesPlayed = $stats->getGamesPlayed();
        $highScore = max($stats->getHighScore(), $game->getScore());
        $dailyStreak = GameType::DAILY === $game->getType() ? $stats->getDailyStreak() + 1 : $stats->getDailyStreak();

        $stats
            ->setGamesPlayed($gamesPlayed + 1)
            ->setHighScore($highScore)
            ->setDailyStreak($dailyStreak);

        $this->manager->flush();
    }
}
