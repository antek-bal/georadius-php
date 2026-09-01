<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\StatsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StatsRepository::class)]
class Stats
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'stats', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    #[ORM\Column]
    private int $gamesPlayed;

    #[ORM\Column]
    private int $highScore;

    #[ORM\Column]
    private int $dailyStreak;

    public function __construct(User $user)
    {
        $this->user = $user;
        $this->gamesPlayed = 0;
        $this->highScore = 0;
        $this->dailyStreak = 0;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getGamesPlayed(): int
    {
        return $this->gamesPlayed;
    }

    public function setGamesPlayed(int $gamesPlayed): static
    {
        $this->gamesPlayed = $gamesPlayed;

        return $this;
    }

    public function getHighScore(): int
    {
        return $this->highScore;
    }

    public function setHighScore(int $highScore): static
    {
        $this->highScore = $highScore;

        return $this;
    }

    public function getDailyStreak(): int
    {
        return $this->dailyStreak;
    }

    public function setDailyStreak(int $dailyStreak): static
    {
        $this->dailyStreak = $dailyStreak;

        return $this;
    }
}
