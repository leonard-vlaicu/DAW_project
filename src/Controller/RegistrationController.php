<?php

namespace App\Controller;

use App\Entity\User;
use App\Exceptions\EmailSignatureVerifiedException;
use App\Exceptions\InvalidEmailSignatureException;
use App\Form\RegistrationFormType;
use App\Security\EmailVerifier;
use App\Services\UserService;
use App\Utils\Utils;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use SymfonyCasts\Bundle\VerifyEmail\Exception\ExpiredSignatureException;

class RegistrationController extends AbstractController {

    public function __construct(private EmailVerifier  $emailVerifier,
                                private UserService $userService) {
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security, EntityManagerInterface $entityManager): Response {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_main');
        }

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();

            // encode the plain password
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            $entityManager->persist($user);
            $entityManager->flush();

            Utils::sendVerificationEmail($user, $this->emailVerifier);

            return $this->render('registration/post_register.html.twig');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request): Response {
        try {
            $this->emailVerifier->verifyEmailConfirmationFromRequest($request);
        } catch (InvalidEmailSignatureException|EmailSignatureVerifiedException|ExpiredSignatureException $e) {
            $this->addFlash('verify_email_error', $e->getMessage());

            return $this->redirectToRoute('app_register');
        }
        $this->addFlash('success', 'Your email address has been verified. You can now log in');

        return $this->redirectToRoute('app_login');
    }
}
