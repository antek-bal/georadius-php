<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\UserManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserManagerTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;
    private UserManager $userManager;
    private UserRepository $repository;

    protected function setUp(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $hasher = $this->createStub(UserPasswordHasherInterface::class);
        $hasher->method('hashPassword')->willReturn('fake_hashed_password');

        $em = $container->get(EntityManagerInterface::class);
        assert($em instanceof EntityManagerInterface);
        $this->entityManager = $em;

        $repository = $container->get(UserRepository::class);
        assert($repository instanceof UserRepository);
        $this->repository = $repository;

        $this->userManager = new UserManager($this->repository, $this->entityManager, $hasher);

        $this->entityManager->createQuery('DELETE FROM App\Entity\Game')->execute();
        $this->entityManager->createQuery('DELETE FROM App\Entity\Stats')->execute();
        $this->entityManager->createQuery('DELETE FROM App\Entity\User')->execute();
    }

    public function testSuccessfulRegisterUser(): void
    {
        $this->loadTestUser();

        $email = 'test_user@gmail.com';
        $username = 'test_user';

        $user = $this->userManager->register($email, 'password123!', $username);

        $this->assertNotNull($user);
        $this->assertEquals($user->getEmail(), $email);
        $this->assertEquals($user->getUsername(), $username);
    }

    public function testInvalidRegisterRepeatedEmail(): void
    {
        $this->loadTestUser();

        $email = 'johndoe@gmail.com';
        $username = 'test_user';

        $user = $this->userManager->register($email, 'password123!', $username);

        $this->assertNull($user);
    }

    public function testInvalidRegisterRepeatedUsername(): void
    {
        $this->loadTestUser();

        $email = 'test_user@gmai.com';
        $username = 'johndoe';

        $user = $this->userManager->register($email, 'password123!', $username);

        $this->assertNull($user);
    }

    private function loadTestUser(): void
    {
        $user = new User('johndoe@gmail.com', 'password123!', 'johndoe');

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}
