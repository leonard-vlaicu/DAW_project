<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use App\Utils\Utils;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController {

    public function __construct(private EmailVerifier  $emailVerifier,
                                private UserRepository $userRepository) {
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
    public function verifyUserEmail(Request $request, TranslatorInterface $translator): Response {
        try {
            $user = $this->userRepository->findBySignature($request->get('signature'));

            if ($user === null) {
                $this->addFlash('verify_email_error', "The link to verify your email is invalid.");
                return $this->redirectToRoute('app_register');
            } else {
                $this->emailVerifier->handleEmailConfirmation($request, $user);
            }
        } catch (VerifyEmailExceptionInterface) {
            $this->addFlash('verify_email_error', "The link to verify your email has expired. A new link has been sent.");

            Utils::sendVerificationEmail($user, $this->emailVerifier);
            return $this->redirectToRoute('app_register');
        }

        $this->addFlash('success', 'Your email address has been verified. You can now log in');

        return $this->redirectToRoute('app_login');
    }
}
