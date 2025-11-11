<?php

namespace App\Utils;

use App\Entity\User;
use App\Security\EmailVerifier;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;

class Utils {
    public static function sendVerificationEmail(User $user, EmailVerifier $emailVerifier): void {
        $emailVerifier->sendEmailConfirmation('app_verify_email', $user,
            (new TemplatedEmail())
                ->from(new Address('no-reply@vla-library.it.com', 'VLA - Library'))
                ->to((string)$user->getEmail())
                ->subject('Please Confirm your Email')
                ->htmlTemplate('registration/email_confirmation.html.twig')
        );
    }
}
