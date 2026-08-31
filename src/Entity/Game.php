<?php

declare(strict_types=1);

namespace App\Entity;

use App\Enum\GameType;
use App\Repository\GameRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GameRepository::class)]
class Game
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'games')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'games')]
    private ?DailyChallenge $dailyChallenge = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?City $startingCity = null;

    #[ORM\Column(enumType: GameType::class)]
    private ?GameType $type = null;

    #[ORM\Column]
    private ?int $score = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $playedAt = null;

    #[ORM\Column]
    private ?int $durationSec = null;

    #[ORM\Column]
    private ?int $maxRadius = null;

    #[ORM\Column]
    private ?int $currentRound = null;

    #[ORM\Column]
    private ?int $attemptsLeft = null;

    public function __construct(User $user, City $startingCity, GameType $type)
    {
        $this->user = $user;
        $this->startingCity = $startingCity;
        $this->type = $type;
        $this->playedAt = new \DateTimeImmutable();
        $this->score = 0;
        $this->durationSec = 0;
        $this->maxRadius = 0;
        $this->currentRound = 1;
        $this->attemptsLeft = 3;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getDailyChallenge(): ?DailyChallenge
    {
        return $this->dailyChallenge;
    }

    public function setDailyChallenge(?DailyChallenge $dailyChallenge): static
    {
        $this->dailyChallenge = $dailyChallenge;

        return $this;
    }

    public function getStartingCity(): ?City
    {
        return $this->startingCity;
    }

    public function setStartingCity(?City $startingCity): static
    {
        $this->startingCity = $startingCity;

        return $this;
    }

    public function getType(): ?GameType
    {
        return $this->type;
    }

    public function setType(GameType $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getScore(): ?int
    {
        return $this->score;
    }

    public function setScore(int $score): static
    {
        $this->score = $score;

        return $this;
    }

    public function getPlayedAt(): ?\DateTimeImmutable
    {
        return $this->playedAt;
    }

    public function setPlayedAt(\DateTimeImmutable $playedAt): static
    {
        $this->playedAt = $playedAt;

        return $this;
    }

    public function getDurationSec(): ?int
    {
        return $this->durationSec;
    }

    public function setDurationSec(int $durationSec): static
    {
        $this->durationSec = $durationSec;

        return $this;
    }

    public function getMaxRadius(): ?int
    {
        return $this->maxRadius;
    }

    public function setMaxRadius(int $maxRadius): static
    {
        $this->maxRadius = $maxRadius;

        return $this;
    }

    public function getCurrentRound(): ?int
    {
        return $this->currentRound;
    }

    public function setCurrentRound(int $currentRound): static
    {
        $this->currentRound = $currentRound;

        return $this;
    }

    public function getAttemptsLeft(): ?int
    {
        return $this->attemptsLeft;
    }

    public function setAttemptsLeft(int $attemptsLeft): static
    {
        $this->attemptsLeft = $attemptsLeft;

        return $this;
    }
}
