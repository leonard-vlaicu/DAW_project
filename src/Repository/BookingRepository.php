<?php

namespace App\Repository;

use App\Entity\Booking;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class BookingRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Booking::class);
    }

    /**
     * @return array<Booking>
     */
    public function getAllBookings(): array {
        return $this->findAll();
    }

    public function getBookingById($id): Booking|null {
        return $this->find($id);
    }

    public function save(Booking $booking): void {
        $this->getEntityManager()->persist($booking);
        $this->getEntityManager()->flush();
    }

    public function delete(Booking $booking): void {
        $this->getEntityManager()->remove($booking);
        $this->getEntityManager()->flush();
    }

    /**
     * @param $id
     * @return array<Booking>
     */
    public function findBookingsByUserId($id): array {
        return $this->createQueryBuilder('b')
            ->where('b.user = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $id
     * @return array<Booking>
     */
    public function findBookingsByBookId($id): array {
        return $this->createQueryBuilder('b')
            ->where('b.book = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult();
    }
}
