<?php

namespace App\Controller;

use App\Exceptions\InvalidSignatureException;
use App\Form\ForgotPasswordFormType;
use App\Form\ResetPasswordFormType;
use App\Security\EmailService;
use App\Services\UserService;
use App\Utils\Utils;
use LogicException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use SymfonyCasts\Bundle\VerifyEmail\Exception\ExpiredSignatureException;

class LoginController extends AbstractController {
    public function __construct(private EmailService    $emailVerifier,
                                private UserService     $userService,
                                private LoggerInterface $logger, private readonly EmailService $emailService
    ) {
    }

    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_main');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('login/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route(path: '/forgot-password', name: 'app_forgot_password')]
    public function forgotPassword(Request $request): Response {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_main');
        }

        $form = $this->createForm(ForgotPasswordFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();

            $user = $this->userService->getUserByEmail($email);
            if ($user !== null) {
                Utils::sendResetPasswordEmail($user, $this->emailVerifier);
            }

            $this->addFlash('success', 'If an account exists with this email, you will receive a link to reset your password.');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('login/forgot_password.html.twig', [
            'forgottenPasswordForm' => $form,
        ]);
    }

    #[Route(path: '/reset-password', name: 'app_reset_password')]
    public function resetPassword(Request $request, UserPasswordHasherInterface $userPasswordHasher): Response {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_main');
        }

        try {
            $user = $this->emailService->verifyPasswordResetFromRequest($request);
        } catch (InvalidSignatureException|ExpiredSignatureException $e) {
            $this->addFlash('error', $e->getMessage());

            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(ResetPasswordFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            $this->userService->save($user);

            $this->addFlash('success', 'Your password has been reset successfully.');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('login/reset_password.html.twig', [
            'resetPasswordForm' => $form,
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void {
        throw new LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route(path: '/resend-email', name: 'app_resend_email')]
    public function resendEmail(): void {

    }
}
