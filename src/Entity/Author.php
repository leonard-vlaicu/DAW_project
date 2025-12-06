<?php

namespace App\Entity;

use App\Repository\AuthorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AuthorRepository::class)]
class Author {
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
        message: 'The author name cannot contain a number',
        match: false,
    )]
    private ?string $firstName = null;

    #[ORM\Column(length: 64)]
    #[Assert\Regex(
        pattern: '/\d/',
        message: 'The author name cannot contain a number',
        match: false,
    )]
    private ?string $lastName = null;

    #[ORM\ManyToMany(targetEntity: Book::class, mappedBy: 'authors')]
    private Collection $books;

    public function getBooks(): Collection {
        return $this->books;
    }

    public function addBook(Book $book): self {
        if (!$this->books->contains($book)) {
            $this->books->add($book);
            $book->addAuthor($this);
        }
        return $this;
    }

    public function removeBook(Book $book): self {
        if ($this->books->removeElement($book)) {
            $book->removeAuthor($this);
        }

        return $this;
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getFirstName(): ?string {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): self {
        $this->firstName = $firstName;
        return $this;
    }

    public function getLastName(): ?string {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): self {
        $this->lastName = $lastName;
        return $this;
    }
}

