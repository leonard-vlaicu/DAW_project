<?php

namespace App\Entity;

use App\Repository\BookRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BookRepository::class)]
#[UniqueEntity(fields: ['isbn'], message: 'This ISBN already exists')]
class Book {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    #[ORM\Column(length: 64)]
    private ?string $title = null;
    #[ORM\Column]
    #[Assert\Regex("/\d*$/")]
    #[Assert\GreaterThanOrEqual(1500)]
    private ?int $year;
    #[ORM\Column]
    #[Assert\Regex("/\d*$/")]
    #[Assert\GreaterThanOrEqual(1)]
    private ?int $pages;
    #[ORM\Column(length: 13)]
    #[Assert\Regex("/\d*$/")]
    private ?string $isbn;
    #[ORM\Column]
    private ?int $copies;
    #[ORM\ManyToMany(targetEntity: Genre::class, inversedBy: 'books', cascade: ['persist'])]
    #[ORM\JoinTable(name: 'book_genre')]
    private Collection $genres;
    #[ORM\ManyToMany(targetEntity: Author::class, inversedBy: 'books', cascade: ['persist'])]
    #[ORM\JoinTable(name: 'book_author')]
    private Collection $authors;

    public function __construct() {
        $this->genres = new ArrayCollection();
        $this->authors = new ArrayCollection();
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getTitle(): ?string {
        return $this->title;
    }

    public function setTitle(?string $title): Book {
        $this->title = $title;
        return $this;
    }

    public function getYear(): ?int {
        return $this->year;
    }

    public function setYear(?int $year): Book {
        $this->year = $year;
        return $this;
    }

    public function getPages(): ?int {
        return $this->pages;
    }

    public function setPages(?int $pages): Book {
        $this->pages = $pages;
        return $this;
    }

    public function getIsbn(): ?string {
        return $this->isbn;
    }

    public function setIsbn(?string $isbn): Book {
        $this->isbn = $isbn;
        return $this;
    }

    public function getCopies(): ?int {
        return $this->copies;
    }

    public function setCopies(?int $copies): Book {
        $this->copies = $copies;
        return $this;
    }

    public function getGenres(): Collection {
        return $this->genres;
    }

    public function addGenre(Genre $genre): static {
        if (!$this->genres->contains($genre)) {
            $this->genres->add($genre);
        }

        return $this;
    }

    public function removeGenre(Genre $genre): static {
        $this->genres->removeElement($genre);

        return $this;
    }

    public function getAuthors(): Collection {
        return $this->authors;
    }

    public function addAuthor(Author $author): static {
        if (!$this->authors->contains($author)) {
            $this->authors->add($author);
        }

        return $this;
    }

    public function removeAuthor(Author $author): static {
        $this->authors->removeElement($author);

        return $this;
    }
}
