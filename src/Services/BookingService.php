<?php

namespace App\Services;

use App\Entity\Booking;
use App\Repository\BookingRepository;

class BookingService {
    public function __construct(private BookingRepository $bookingRepository) {
    }

    /**
     * @return array<Booking>
     */
    public function getAllBookings(): array {
        return $this->bookingRepository->getAllBookings();
    }

    public function getBookingById($id): Booking|null {
        return $this->bookingRepository->getBookingById($id);
    }

    public function save(Booking $booking): void {
        $this->bookingRepository->save($booking);
    }

    public function delete(Booking $booking): void {
        $this->bookingRepository->delete($booking);
    }

    /**
     * @param $id
     * @return array<Booking>
     */
    public function findBookingsByUserId($id): array {
        return $this->bookingRepository->findBookingsByUserId($id);
    }

    /**
     * @param $id
     * @return array<Booking>
     */
    public function findBookingsByBookId($id): array {
        return $this->bookingRepository->findBookingsByBookId($id);
    }
}
