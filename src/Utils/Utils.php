<?php

namespace App\Utils;

use App\Entity\User;
use App\Security\EmailService;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;

class Utils {
    public static function sendVerificationEmail(User $user, EmailService $emailService): void {
        $emailService->sendEmailConfirmation($user,
            (new TemplatedEmail())
                ->from(new Address('no-reply@vla-library.it.com', 'VLA - Library'))
                ->to((string)$user->getEmail())
                ->subject('Please Confirm your Email')
                ->htmlTemplate('email/email_confirmation.html.twig')
        );
    }

    public static function sendResetPasswordEmail(User $user, EmailService $emailService): void {
        $emailService->sendEmailResetPassword($user, (
        (new TemplatedEmail())
            ->from(new Address('no-reply@vla-library.it.com', 'VLA - Library'))
            ->to((string)$user->getEmail())
            ->subject('Reset your password')
            ->htmlTemplate('email/email_reset_password.html.twig')
        ));

    }

    public static function generateUniqueHash(User $user): string {
        return md5($user->getEmail() . time());
    }
}
