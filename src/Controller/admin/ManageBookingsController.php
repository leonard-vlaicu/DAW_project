<?php

namespace App\Controller\admin;

use App\Services\BookingService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ManageBookingsController extends AbstractController {
    public function __construct(private readonly BookingService $bookingService) {
    }

    #[Route('/admin/bookings', name: 'app_admin_bookings')]
    public function bookings(): Response {
        $bookings = $this->bookingService->getAllBookings();

        return $this->render('admin/bookings/bookings.html.twig', [
            'bookings' => $bookings,
        ]);
    }

    #[Route('/bookings/delete/{id}', name: 'app_booking_delete')]
    public function deleteBooking(int $id): Response {
        $booking = $this->bookingService->getBookingById($id);

        try {
            $this->bookingService->delete($booking);
            $this->addFlash('success', 'Booking deleted successfully!');
        } catch (\Exception $e) {
            $this->addFlash('error', "Failed to delete booking: " . $e->getMessage());
        } finally {
            return $this->redirectToRoute('app_admin_bookings');
        }
    }
}
