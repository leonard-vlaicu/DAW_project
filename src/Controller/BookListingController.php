<?php

declare(strict_types=1);

namespace App\Controller;

use App\Services\BookService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BookListingController extends AbstractController {
    public function __construct(private BookService $bookService) {
    }

    #[Route(path: '/book-listing', name: 'app_book_listing')]
    public function index(): Response {
        $books = $this->bookService->getAllOrderByIdAsc();

        return $this->render('book_listing/book_listing.html.twig', [
            'books' => $books
        ]);
    }

    #[Route(path: '/book-listing/create/{id}', name: 'app_booking_create')]
    public function createBooking(int $id): Response {
        return new Response("Booking created for book with id: " . $id);
    }


}
