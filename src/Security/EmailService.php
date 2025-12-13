<?php

namespace App\Security;

use App\Entity\User;
use App\Entity\User\SignatureType;
use App\Entity\User\UserSignatures;
use App\Exceptions\EmailSignatureVerifiedException;
use App\Exceptions\InvalidSignatureException;
use App\Services\UserService;
use App\Utils\Utils;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\ExpiredSignatureException;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

class EmailService {
    public function __construct(
        private VerifyEmailHelperInterface $verifyEmailHelper,
        private MailerInterface            $mailer,
        private EntityManagerInterface $entityManager,
        private ContainerBagInterface  $containerBag,
        private UserService            $userService,
        private UrlGeneratorInterface  $urlGenerator) {
    }

    public function sendEmail(User $user, TemplatedEmail $email, SignatureType $type): void {
        $signature = Utils::generateUniqueHash($user);

        if ($type === SignatureType::EMAIL) {
            $signedUrl = $this->urlGenerator->generate('app_verify_email',
                ['signature' => $signature], UrlGeneratorInterface::ABSOLUTE_URL);
        } elseif ($type === SignatureType::PASSWORD) {
            $signedUrl = $this->urlGenerator->generate('app_reset_password',
                ['signature' => $signature], UrlGeneratorInterface::ABSOLUTE_URL);
        }

        $context = $email->getContext();
        $context['signedUrl'] = $signedUrl;

        $userSignature = new UserSignatures();
        $userSignature->setUser($user)
            ->setSignature($signature)
            ->setType($type)
            ->setCreatedOn(new \DateTime())
            ->setExpiresOn(new \DateTime('+1 day'));

        $user->getUserSignature()->add($userSignature);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $email->context($context);
        $this->mailer->send($email);
    }

    public function sendEmailResetPassword(User $user, TemplatedEmail $email): void {
        $this->sendEmail($user, $email, SignatureType::PASSWORD);
    }

    public function sendEmailConfirmation(User $user, TemplatedEmail $email): void {
        $this->sendEmail($user, $email, SignatureType::EMAIL);
    }

    /**
     * @throws InvalidSignatureException
     * @throws EmailSignatureVerifiedException
     * @throws ExpiredSignatureException
     */
    public function verifyEmailConfirmationFromRequest(Request $request): void {
        $user = $this->userService->getUserByEmailSignature($request->get('signature'));

        if ($user === null) {
            throw new InvalidSignatureException("The link to verify your email is invalid.");
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

    /**
     * @throws InvalidSignatureException
     * @throws ExpiredSignatureException
     */
    public function verifyPasswordResetFromRequest(Request $request): User {
        $user = $this->userService->getUserByPasswordSignature($request->get('signature'));

        if ($user === null) {
            throw new InvalidSignatureException("The link to reset your password is invalid.");
        }

        $requestSignature = $request->query->get('signature');
        $userPasswordSignature = $user->getUserSignature()->filter(
            fn(UserSignatures $signature) => $signature->getType() === SignatureType::PASSWORD)->first();

        if (!$userPasswordSignature instanceof UserSignatures) {
            throw new InvalidSignatureException("The link to reset your password is invalid.");
        }

        if ($requestSignature === $userPasswordSignature->getSignature()) {
            if ($userPasswordSignature->getExpiresOn() > new \DateTime()) {
                $user->getUserSignature()->removeElement($userPasswordSignature);
            } else {
                throw new ExpiredSignatureException("The link to reset your password has expired.");
            }
        }

        return $user;
    }
}
