<?php

namespace App\Services;

use App\Entity\User;
use App\Repository\UserRepository;

class UserService {
    public function __construct(private UserRepository $userRepository) {
    }

    public function getUserByEmailSignature($emailSignature): ?User {
        return $this->userRepository->findOneByEmailSignature($emailSignature);
    }

    public function getUserByPasswordSignature($passwordSignature): ?User {
        return $this->userRepository->findOneByPasswordSignature($passwordSignature);
    }
}
