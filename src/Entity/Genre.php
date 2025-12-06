<?php

namespace App\Entity;

use App\Repository\GenreRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: GenreRepository::class)]
#[UniqueEntity(fields: ['name'], message: 'This genre already exists')]
class Genre {
    public function __construct() {
        $this->books = new ArrayCollection();
    }

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

    #[ORM\ManyToMany(targetEntity: Book::class, mappedBy: 'genres')]
    private Collection $books;

    /**
     * @return Collection
     */
    public function getBooks(): Collection {
        return $this->books;
    }

    public function addBook(Book $book): self {
        if (!$this->books->contains($book)) {
            $this->books->add($book);
            $book->addGenre($this);
        }

        return $this;
    }

    public function removeBook(Book $book): self {
        if ($this->books->removeElement($book)) {
            $book->removeGenre($this);
        }

        return $this;
    }

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
