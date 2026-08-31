<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserManager
{
    public function __construct(
        private UserRepository $repository,
        private EntityManagerInterface $manager,
        private readonly UserPasswordHasherInterface $hasher,
    ) {
    }

    public function register(string $email, string $password, string $username): ?User
    {
        if ($this->repository->existsByEmail($email) || $this->repository->existsByUsername($username)) {
            return null;
        }

        $user = new User($email, '', $username);
        $hashedPassword = $this->hasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);

        $this->manager->persist($user);
        $this->manager->flush();

        return $user;
    }
}
