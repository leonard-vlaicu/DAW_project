<?php

namespace App\Entity;

use App\Entity\User\UserSignatures;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
#[UniqueEntity(fields: ['phoneNumber'], message: 'There is already an account with this phone number')]
class User implements UserInterface, PasswordAuthenticatedUserInterface {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column]
    private bool $isVerified = false;

    #[ORM\Column]
    private bool $forcePasswordChange = false;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Regex(
        pattern: '/\d/',
        message: 'Your first name cannot contain a number',
        match: false,
    )]
    private ?string $firstName = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Regex(
        pattern: '/\d/',
        message: 'Your last name cannot contain a number',
        match: false,
    )]
    private ?string $lastName = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Regex("/\d*$/")]
    private ?string $phoneNumber = null;

    #[ORM\OneToMany(targetEntity: UserSignatures::class, mappedBy: 'user', cascade: ['persist'], orphanRemoval: true)]
    private Collection $userSignature;

    public function getFirstName(): ?string {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): User {
        $this->firstName = $firstName;
        return $this;
    }

    public function getLastName(): ?string {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): User {
        $this->lastName = $lastName;
        return $this;
    }

    public function getPhoneNumber(): ?string {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?string $phoneNumber): User {
        $this->phoneNumber = $phoneNumber;
        return $this;
    }

    public function isForcePasswordChange(): bool {
        return $this->forcePasswordChange;
    }

    public function setForcePasswordChange(bool $forcePasswordChange): void {
        $this->forcePasswordChange = $forcePasswordChange;
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getEmail(): ?string {
        return $this->email;
    }

    public function setEmail(string $email): static {
        $this->email = $email;

        return $this;
    }

    public function getUserIdentifier(): string {
        return (string)$this->email;
    }

    public function getRoles(): array {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static {
        $this->roles = $roles;

        return $this;
    }

    public function getPassword(): ?string {
        return $this->password;
    }

    public function setPassword(string $password): static {
        $this->password = $password;

        return $this;
    }

    public function __serialize(): array {
        $data = (array)$this;
        $data["\0" . self::class . "\0password"] = hash('crc32c', $this->password);

        return $data;
    }

    #[\Deprecated]
    public function eraseCredentials(): void {
        // @deprecated, to be removed when upgrading to Symfony 8
    }

    public function isVerified(): bool {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getUserSignature(): Collection {
        return $this->userSignature;
    }

    public function addUserSignature(UserSignatures $userSignature): self {
        if (!$this->userSignature->contains($userSignature)) {
            $this->userSignature->add($userSignature);
            $userSignature->setUser($this);
        }

        return $this;
    }

    public function removeUserSignature(UserSignatures $userSignature): self {
        if ($this->userSignature->removeElement($userSignature) && $userSignature->getUser() === $this) {
            $userSignature->setUser(null);
        }

        return $this;
    }

    public function __construct() {
        $this->userSignature = new ArrayCollection();
    }
}
