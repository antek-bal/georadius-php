<?php

namespace App\Entity;

use App\Repository\DailyChallengeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DailyChallengeRepository::class)]
class DailyChallenge
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, unique: true)]
    private ?\DateTimeImmutable $challengeDate = null;

    #[ORM\Column]
    private ?int $initialRadius = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?City $startingCity = null;

    /**
     * @var Collection<int, Game>
     */
    #[ORM\OneToMany(targetEntity: Game::class, mappedBy: 'dailyChallenge')]
    private Collection $games;

    public function __construct()
    {
        $this->games = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getChallengeDate(): ?\DateTimeImmutable
    {
        return $this->challengeDate;
    }

    public function setChallengeDate(\DateTimeImmutable $challengeDate): static
    {
        $this->challengeDate = $challengeDate;

        return $this;
    }

    public function getInitialRadius(): ?int
    {
        return $this->initialRadius;
    }

    public function setInitialRadius(int $initialRadius): static
    {
        $this->initialRadius = $initialRadius;

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

    /**
     * @return Collection<int, Game>
     */
    public function getGames(): Collection
    {
        return $this->games;
    }

    public function addGame(Game $game): static
    {
        if (!$this->games->contains($game)) {
            $this->games->add($game);
            $game->setDailyChallenge($this);
        }

        return $this;
    }

    public function removeGame(Game $game): static
    {
        if ($this->games->removeElement($game)) {
            // set the owning side to null (unless already changed)
            if ($game->getDailyChallenge() === $this) {
                $game->setDailyChallenge(null);
            }
        }

        return $this;
    }
}
