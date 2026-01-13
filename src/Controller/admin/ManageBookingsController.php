<?php

namespace App\Controller\admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ManageBookingsController extends AbstractController {
    public function __construct() {
    }

    #[Route('/admin/bookings', name: 'app_admin_bookings')]
    public function bookings(): Response {
        return new Response('Hello World!');
    }
}
