<?php

namespace App\Controller;

use App\Entity\User;
use App\Exceptions\EmailSignatureVerifiedException;
use App\Exceptions\InvalidSignatureException;
use App\Form\RegistrationFormType;
use App\Security\EmailService;
use App\Services\UserService;
use App\Utils\Utils;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use SymfonyCasts\Bundle\VerifyEmail\Exception\ExpiredSignatureException;

class RegistrationController extends AbstractController {

    public function __construct(private EmailService $emailService,
                                private UserService           $userService,
                                private ContainerBagInterface $containerBag,
                                private LoggerInterface       $logger) {
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security, EntityManagerInterface $entityManager): Response {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_main');
        }

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        $adminEmails = explode(",", $this->containerBag->get('app.admin_emails'));

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();

            foreach ($adminEmails as $adminEmail) {
                if ($user->getEmail() === $adminEmail) {
                    $user->setRoles(['ROLE_ADMIN']);
                }
            }

            // encode the plain password
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            $entityManager->persist($user);
            $entityManager->flush();

            Utils::sendVerificationEmail($user, $this->emailService);

            $this->addFlash('success', 'Please check your email to verify your account.');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request): Response {
        try {
            $this->emailService->verifyEmailConfirmationFromRequest($request);
        } catch (InvalidSignatureException|EmailSignatureVerifiedException|ExpiredSignatureException $e) {
            $this->addFlash('verify_email_error', $e->getMessage());

            return $this->redirectToRoute('app_register');
        }
        $this->addFlash('success', 'Your email address has been verified. You can now log in');

        return $this->redirectToRoute('app_login');
    }
}
