<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\UpdateUserDTO;
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

    public function edit(User $user, UpdateUserDTO $dto): ?User
    {
        if (null !== $dto->password && !$this->hasher->isPasswordValid($user, $dto->password)) {
            $user->setPassword($this->hasher->hashPassword($user, $dto->password));
        }

        if (null !== $dto->username && $dto->username !== $user->getUsername()) {
            if ($this->repository->existsByUsername($dto->username)) {
                return null;
            }
            $user->setUsername($dto->username);
        }

        $this->manager->flush();

        return $user;
    }

    public function delete(User $user): void
    {
        $this->repository->deleteUser($user);
    }
}
