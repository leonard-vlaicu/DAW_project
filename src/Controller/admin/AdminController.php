<?php

declare(strict_types=1);

namespace App\Controller\admin;

use App\Services\GenreService;
use App\Services\UserService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdminController extends AbstractController {
    public function __construct(private UserService     $userService,
                                private GenreService    $genreService,
                                private LoggerInterface $logger) {
    }

    #[Route('/admin', name: 'app_admin_index')]
    public function index(): Response {
        return $this->render('admin/index.html.twig');
    }

    #[Route('/admin/authors', name: 'app_admin_authors')]
    public function authors(): Response {
        return $this->render('admin/author/authors.html.twig');
    }

    #[Route('/admin/books', name: 'app_admin_books')]
    public function books(): Response {
        return $this->render('admin/books.html.twig');
    }

    #[Route('/admin/bookings', name: 'app_admin_bookings')]
    public function orders(): Response {
        return $this->render('admin/bookings.html.twig');
    }

    #[Route('/admin/users', name: 'app_admin_users')]
    public function users(): Response {
        return $this->render('admin/users.html.twig');
    }
}
