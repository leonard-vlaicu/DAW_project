<?php

namespace App\Services;

use App\Entity\User;
use App\Repository\UserRepository;

class UserService {
    public function __construct(private UserRepository $userRepository) {
    }

    public function getUserByEmail($email): ?User {
        return $this->userRepository->findOneBy(['email' => $email]);
    }

    public function getUserByEmailSignature($emailSignature): ?User {
        return $this->userRepository->findOneByEmailSignature($emailSignature);
    }

    public function getUserByPasswordSignature($passwordSignature): ?User {
        return $this->userRepository->findOneByPasswordSignature($passwordSignature);
    }

    public function save(User $user): void {
        $this->userRepository->save($user);
    }
}
