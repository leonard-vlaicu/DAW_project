<?php

namespace App\Entity;

use App\Repository\UserDetailsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserDetailsRepository::class)]
class UserDetails {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $firstName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $lastName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $phoneNumber = null;

    #[ORM\OneToOne(targetEntity: User::class, inversedBy: 'userDetails')]
    private ?User $user = null;

    public function getUser(): ?User {
        return $this->user;
    }

    public function setUser(?User $user): static {
        $this->user = $user;

        return $this;
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function setId(int $id): static {
        $this->id = $id;

        return $this;
    }

    public function getFirstName(): ?string {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): static {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): static {
        $this->lastName = $lastName;

        return $this;
    }

    public function getPhoneNumber(): ?string {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?string $phoneNumber): static {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }
}
