<?php

namespace App\Entity;


use App\Repository\BookingRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BookingRepository::class)]
class Booking {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'datetime')]
    private ?DateTimeInterface $beginDate = null;

    #[ORM\Column(type: 'datetime')]
    private ?DateTimeInterface $endDate = null;

    #[ORM\ManyToOne(targetEntity: Book::class, inversedBy: 'bookings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Book $book = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'bookings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    public function getId(): ?int {
        return $this->id;
    }

    public function getBeginDate(): ?DateTimeInterface {
        return $this->beginDate;
    }

    public function setBeginDate(?DateTimeInterface $beginDate): Booking {
        $this->beginDate = $beginDate;
        return $this;
    }

    public function getEndDate(): ?DateTimeInterface {
        return $this->endDate;
    }

    public function setEndDate(?DateTimeInterface $endDate): Booking {
        $this->endDate = $endDate;
        return $this;
    }

    public function getBook(): ?Book {
        return $this->book;
    }

    public function setBook(?Book $book): Booking {
        if ($book !== null && !$book->getBookings()->contains($this)) {
            $book->addBooking($this);
        }

        return $this;
    }

    public function getUser(): ?User {
        return $this->user;
    }

    public function setUser(?User $user): Booking {
        if ($user !== null && !$user->getBookings()->contains($this)) {
            $user->addBooking($this);
        }

        return $this;
    }
}
