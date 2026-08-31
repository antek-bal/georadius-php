<?php

namespace App\Entity;

use App\Repository\CityRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CityRepository::class)]
class City
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 2)]
    private ?string $countryCode = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 7)]
    private ?float $latitude = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 7)]
    private ?float $longitude = null;

    #[ORM\Column]
    private ?bool $isStartingCity = null;

    public function __construct(string $name, string $countryCode, float $latitude, float $longitude, bool $isStartingCity)
    {
        $this->name = $name;
        $this->countryCode = $countryCode;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->isStartingCity = $isStartingCity;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getCountryCode(): ?string
    {
        return $this->countryCode;
    }

    public function setCountryCode(string $countryCode): static
    {
        $this->countryCode = $countryCode;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(float $latitude): static
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude): static
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function isStartingCity(): ?bool
    {
        return $this->isStartingCity;
    }

    public function setIsStartingCity(bool $isStartingCity): static
    {
        $this->isStartingCity = $isStartingCity;

        return $this;
    }
}
