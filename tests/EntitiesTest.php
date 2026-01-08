<?php

namespace App\Tests;

use App\Entity\Book;
use App\Entity\Booking;
use App\Entity\User;
use App\Kernel;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class EntitiesTest extends KernelTestCase {
    protected static function getKernelClass(): string {
        return Kernel::class;
    }

    public function testBookingRelation(): void {
        $user = new User();
        $book = new Book();
        $booking = new Booking();

        $booking->setUser($user);
        $booking->setBook($book);

        $user->addBooking($booking);

        $this->assertCount(1, $user->getBookings());
        $this->assertSame($user, $user->getBookings()->first()->getUser());
    }

    public function testBidirectionalSync(): void {
        $user = new User();
        $book = new Book();
        $booking = new Booking();

        $booking->setUser($user);
        $booking->setBook($book);

        $user->addBooking($booking);

        $this->assertTrue($user->getBookings()->contains($booking));
        $this->assertTrue($book->getBookings()->contains($booking));
        $this->assertCount(1, $user->getBookings());
    }
}
