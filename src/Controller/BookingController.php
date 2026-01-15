<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Booking;
use App\Form\BookingFormType;
use App\Services\BookingService;
use App\Services\BookService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BookingController extends AbstractController {
    public function __construct(private BookService $bookService, private BookingService $bookingService) {
    }

    #[Route(path: '/book-listing', name: 'app_book_listing')]
    public function index(): Response {
        $books = $this->bookService->getAllOrderByIdAsc();

        return $this->render('book_listing/book_listing.html.twig', [
            'books' => $books
        ]);
    }

    #[Route(path: '/book-listing/view', name: 'app_bookings_view')]
    public function viewBookings(): Response {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $bookings = $this->bookingService->findBookingsByUserId($this->getUser()->getId());

        return $this->render('user/bookings.html.twig', [
            'bookings' => $bookings
        ]);
    }

    #[Route('/book-listing/delete/{id}', name: 'app_booking_delete')]
    public function deleteBooking(int $id): Response {
        $booking = $this->bookingService->getBookingById($id);

        if ($booking->getUser()->getId() !== $this->getUser() && $this->getUser()->getId()) {
            return $this->redirectToRoute('app_bookings_view');
        }

        try {
            $this->bookingService->delete($booking);
            $this->addFlash('success', 'Booking deleted successfully!');
        } catch (\Exception $e) {
            $this->addFlash('error', "Failed to delete booking: " . $e->getMessage());
        } finally {
            return $this->redirectToRoute('app_bookings_view');
        }
    }

    #[Route(path: '/book-listing/create/{id}', name: 'app_booking_create')]
    public function createBooking(Request $request, int $id): Response {
        $book = $this->bookService->getBookById($id);
        if ($this->getUser()) {
            $user = $this->getUser();
        } else {
            return $this->redirectToRoute('app_login');
        }

        $booking = new Booking();

        $booking->setBook($book);
        $booking->setUser($user);

        $form = $this->createForm(BookingFormType::class, $booking);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->bookingService->save($booking);
            $this->addFlash('success', 'Booking created successfully!');

            return $this->redirectToRoute('app_book_listing');
        }

        return $this->render('book_listing/create_booking.html.twig', [
            'createBookingForm' => $form,
            'book' => $book
        ]);
    }

    #[Route('/booking/unavailable-dates/{id}', name: 'app_booking_unavailable_dates', methods: ['GET'])]
    public function getUnavailableDates(Book $book): JsonResponse {
        $bookings = $book->getBookings();

        $unavailableDates = [];
        $copies = $book->getCopies();

        $dateBookings = [];

        foreach ($bookings as $booking) {
            $begin = $booking->getBeginDate();
            $end = $booking->getEndDate();

            $current = clone $begin;
            while ($current <= $end) {
                $dateKey = $current->format('Y-m-d');
                if (!isset($dateBookings[$dateKey])) {
                    $dateBookings[$dateKey] = 0;
                }
                $dateBookings[$dateKey]++;
                $current->modify('+1 day');
            }
        }

        foreach ($dateBookings as $date => $bookingCount) {
            if ($bookingCount >= $copies) {
                $unavailableDates[] = $date;
            }
        }

        return new JsonResponse($unavailableDates);
    }
}
