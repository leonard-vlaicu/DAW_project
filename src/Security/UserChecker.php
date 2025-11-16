<?php

namespace App\Security;

use App\Entity\User as AppUser;
use App\Exceptions\AccountNotVerifiedException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface {
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger) {
        $this->logger = $logger;
    }

    public function checkPreAuth(UserInterface $user): void {
    }

    public function checkPostAuth(UserInterface $user): void {
        $this->logger->debug("Checking user post");

        if (!$user instanceof AppUser) {
            return;
        }

        if (!$user->isVerified()) {
            $this->logger->debug("Checking user not verified");
            throw new AccountNotVerifiedException("Your account is not verified. Please check your email for the verification link.");
        }
    }
}
