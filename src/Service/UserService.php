<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * @author Daisy Greenway
 * @author Marin Taverniers
 */
class UserService {
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher) {
        $this->passwordHasher = $passwordHasher;
    }

    public function setPassword(User &$user, string $plainPassword) {
        $password = $this->passwordHasher->hashPassword($user, $plainPassword);
        $user->setPassword($password);
    }
}
