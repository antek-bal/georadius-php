<?php

namespace App\DataFixtures;

use App\Entity\Stats;
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
            $user = new User();
            $user->setEmail($data['email']);
            $user->setUsername($data['username']);
            $user->setRoles($data['roles']);

            $hashedPassword = $this->hasher->hashPassword($user, $data['password']);
            $user->setPassword($hashedPassword);

            $manager->persist($user);

            $stats = new Stats();
            $stats->setUser($user);
            $stats->setGamesPlayed(0);
            $stats->setHighScore(0);
            $stats->setDailyStreak(0);

            $manager->persist($stats);
        }

        $manager->flush();
    }
}
