<?php

namespace App\Entity;

use App\Repository\GenreRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: GenreRepository::class)]
#[UniqueEntity(fields: ['name'], message: 'This genre already exists')]
class Genre {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 64)]
    #[Assert\Regex(
        pattern: '/\d/',
        message: 'The genre name cannot contain a number',
        match: false,
    )]
    private ?string $name = null;

    public function getId(): ?int {
        return $this->id;
    }

    public function getName(): ?string {
        return $this->name;
    }
    public function setName(?string $name): Genre {
        $this->name = $name;
        return $this;
    }
}
