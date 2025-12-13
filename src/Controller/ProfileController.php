<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\UserFormType;
use App\Services\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProfileController extends AbstractController {
    public function __construct(private readonly UserService $userService) {
    }

    #[Route('/profile', name: 'app_profile')]
    public function index(Request $request): Response {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $user = $this->getUser();

        $isAdmin = in_array('ROLE_ADMIN', $user->getRoles());

        $form = $this->createForm(UserFormType::class, $user, [
            'isAdmin' => $isAdmin
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && !$isAdmin) {
            $this->userService->save($user);

            $this->addFlash('success', 'Your profile has been updated successfully.');
        }

        return $this->render('profile/index.html.twig', [
            'profileForm' => $form->createView(),
            'isAdmin' => $isAdmin
        ]);
    }
}
