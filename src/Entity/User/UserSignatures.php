<?php

namespace App\Entity\User;

use App\Entity\User;
use App\Repository\UserSignatureRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserSignatureRepository::class)]
class UserSignatures {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 512)]
    private ?string $signature = null;

    #[ORM\Column]
    private ?DateTime $createdOn = null;

    #[ORM\Column]
    private ?DateTime $expiresOn = null;

    #[ORM\Column(enumType: SignatureType::class)]
    private ?SignatureType $type = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'userSignature')]
    private ?User $user = null;

    public function getId(): ?int {
        return $this->id;
    }

    public function setId(?int $id): UserSignatures {
        $this->id = $id;
        return $this;
    }

    public function getSignature(): ?string {
        return $this->signature;
    }

    public function setSignature(?string $signature): UserSignatures {
        $this->signature = $signature;
        return $this;
    }

    public function getExpiresOn(): ?DateTime {
        return $this->expiresOn;
    }

    public function setExpiresOn(?DateTime $expiresOn): UserSignatures {
        $this->expiresOn = $expiresOn;
        return $this;
    }

    public function getType(): ?SignatureType {
        return $this->type;
    }

    public function setType(?SignatureType $type): UserSignatures {
        $this->type = $type;
        return $this;
    }

    public function getUser(): ?User {
        return $this->user;
    }

    public function setUser(?User $user): UserSignatures {
        $this->user = $user;
        return $this;
    }

    public function getCreatedOn(): ?DateTime {
        return $this->createdOn;
    }

    public function setCreatedOn(?DateTime $createdOn): UserSignatures {
        $this->createdOn = $createdOn;
        return $this;
    }
}
