<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $hasher,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $users = [
            [
                'email' => 'andrew@example.com',
                'password' => 'password123!',
                'username' => 'andrewG',
                'roles' => ['ROLE_ADMIN'],
            ],
            [
                'email' => 'johndoe@gmail.com',
                'password' => 'pass_123456',
                'username' => 'JohnDoe',
                'roles' => ['ROLE_USER'],
            ],
        ];

        foreach ($users as $data) {
            $user = new User($data['email'], '', $data['username'], $data['roles']);

            $hashedPassword = $this->hasher->hashPassword($user, $data['password']);
            $user->setPassword($hashedPassword);

            $manager->persist($user);
        }

        $manager->flush();
    }
}
