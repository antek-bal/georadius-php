<?php

namespace App\Entity;

use App\Repository\GameRepository;
use Doctrine\ORM\Mapping as ORM;

use App\Enum\GameType;

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
    private ?string $type = null;

    #[ORM\Column]
    private ?int $score = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $playedAt = null;

    #[ORM\Column]
    private ?int $durationSec = null;

    #[ORM\Column]
    private ?float $maxRadius = null;

    public function __construct()
    {
        $this->playedAt = new \DateTimeImmutable();
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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
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

    public function getMaxRadius(): ?float
    {
        return $this->maxRadius;
    }

    public function setMaxRadius(float $maxRadius): static
    {
        $this->maxRadius = $maxRadius;

        return $this;
    }
}
