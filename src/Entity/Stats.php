<?php

namespace App\Entity;

use App\Entity\User;
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
    private ?User $user = null;

    #[ORM\Column]
    private ?int $gamesPlayed = 0;

    #[ORM\Column]
    private ?int $highScore = 0;

    #[ORM\Column]
    private ?int $dailyStreak = 0;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getGamesPlayed(): ?int
    {
        return $this->gamesPlayed;
    }

    public function setGamesPlayed(int $gamesPlayed): static
    {
        $this->gamesPlayed = $gamesPlayed;

        return $this;
    }

    public function getHighScore(): ?int
    {
        return $this->highScore;
    }

    public function setHighScore(int $highScore): static
    {
        $this->highScore = $highScore;

        return $this;
    }

    public function getDailyStreak(): ?int
    {
        return $this->dailyStreak;
    }

    public function setDailyStreak(int $dailyStreak): static
    {
        $this->dailyStreak = $dailyStreak;

        return $this;
    }
}
