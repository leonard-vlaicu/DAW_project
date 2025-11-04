<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BookListingController extends AbstractController {
    #[Route(path: '/book-listing', name: 'app_book_listing')]
    public function index(): Response {
        return $this->render('book_listing/book_listing.html.twig');
    }
}
