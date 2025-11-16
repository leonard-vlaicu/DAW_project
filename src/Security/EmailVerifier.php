<?php

namespace App\Security;

use App\Entity\User;
use App\Entity\User\SignatureType;
use App\Entity\User\UserSignatures;
use App\Exceptions\EmailSignatureVerifiedException;
use App\Exceptions\InvalidEmailSignatureException;
use App\Services\UserService;
use App\Utils\Utils;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\ExpiredSignatureException;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

class EmailVerifier {
    public function __construct(
        private VerifyEmailHelperInterface $verifyEmailHelper,
        private MailerInterface            $mailer,
        private EntityManagerInterface $entityManager,
        private ContainerBagInterface  $containerBag,
        private UserService            $userService) {
    }

    public function sendEmailResetPassword(string $verifyEmailRouteName, User $user, TemplatedEmail $email): void {
        $signatureComponents = $this->verifyEmailHelper->generateSignature(
            $verifyEmailRouteName,
            (string)$user->getId(),
            (string)$user->getEmail()
        );


    }

    public function sendEmailConfirmation(User $user, TemplatedEmail $email): void {
        $signature = md5($user->getEmail() . time());
        $signedDefaultUrl = $this->containerBag->get('app.url_env');

        $signedUrl = $signedDefaultUrl . '/verify/email?signature=' . $signature;

        $context = $email->getContext();
        $context['signedUrl'] = $signedUrl;

        $userSignature = new UserSignatures();
        $userSignature->setUser($user)
            ->setSignature($signature)
            ->setType(SignatureType::EMAIL)
            ->setCreatedOn(new \DateTime())
            ->setExpiresOn(new \DateTime('+1 day'));

        $user->getUserSignature()->add($userSignature);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $email->context($context);

        $this->mailer->send($email);
    }

    /**
     * @throws InvalidEmailSignatureException
     * @throws EmailSignatureVerifiedException
     * @throws ExpiredSignatureException
     */
    public function verifyEmailConfirmationFromRequest(Request $request): void {
        $user = $this->userService->getUserByEmailSignature($request->get('signature'));

        if ($user === null) {
            throw new InvalidEmailSignatureException("The link to verify your email is invalid.");
        }

        $requestSignature = $request->query->get('signature');
        $userEmailSignature = $user->getUserSignature()->filter(
            fn(UserSignatures $signature) => $signature->getType() === SignatureType::EMAIL)->first();

        if ($requestSignature === $userEmailSignature->getSignature() && $user->isVerified() === false) {
            if ($userEmailSignature->getExpiresOn() > new \DateTime()) {
                $user->setIsVerified(true);
                $user->getUserSignature()->removeElement($userEmailSignature);

                $this->entityManager->persist($user);
                $this->entityManager->flush();
            } else {
                Utils::sendVerificationEmail($user, $this);

                throw new ExpiredSignatureException("The link to verify your email has expired. A new link has been sent.");
            }
        }
    }
}
