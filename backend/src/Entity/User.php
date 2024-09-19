<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $username = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $registrationDate = null;

    #[ORM\Column]
    private ?bool $activeSubcription = null;

    #[ORM\Column(length: 255)]
    private ?string $subcriptionType = null;

    #[ORM\Column]
    private ?int $daysRemaining = null;

    #[ORM\Column(type: Types::ARRAY)]
    private array $rol = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getRegistrationDate(): ?\DateTimeInterface
    {
        return $this->registrationDate;
    }

    public function setRegistrationDate(\DateTimeInterface $registrationDate): static
    {
        $this->registrationDate = $registrationDate;

        return $this;
    }

    public function isActiveSubcription(): ?bool
    {
        return $this->activeSubcription;
    }

    public function setActiveSubcription(bool $activeSubcription): static
    {
        $this->activeSubcription = $activeSubcription;

        return $this;
    }

    public function getSubcriptionType(): ?string
    {
        return $this->subcriptionType;
    }

    public function setSubcriptionType(string $subcriptionType): static
    {
        $this->subcriptionType = $subcriptionType;

        return $this;
    }

    public function getDaysRemaining(): ?int
    {
        return $this->daysRemaining;
    }

    public function setDaysRemaining(int $daysRemaining): static
    {
        $this->daysRemaining = $daysRemaining;

        return $this;
    }

    public function getRol(): array
    {
        return $this->rol;
    }

    public function setRol(array $rol): static
    {
        $this->rol = $rol;

        return $this;
    }
}
