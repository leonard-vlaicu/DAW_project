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
}
