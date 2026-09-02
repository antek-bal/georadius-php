<?php

declare(strict_types=1);

namespace App\Tests\Repository;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;
    private UserRepository $repository;

    protected function setUp(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $em = $container->get(EntityManagerInterface::class);
        assert($em instanceof EntityManagerInterface);
        $this->entityManager = $em;

        $repository = $container->get(UserRepository::class);
        assert($repository instanceof UserRepository);
        $this->repository = $repository;

        $this->entityManager->createQuery('DELETE FROM App\Entity\Stats')->execute();
        $this->entityManager->createQuery('DELETE FROM App\Entity\User')->execute();
    }

    public function testCheckUserExistsByEmailWithUser(): void
    {
        $this->loadTestUser();

        $exists = $this->repository->existsByEmail('johndoe@gmail.com');

        $this->assertTrue($exists);
    }

    public function testCheckUserExistsByEmailWithoutUser(): void
    {
        $exists = $this->repository->existsByEmail('jonhdoe@gmail.com');

        $this->assertFalse($exists);
    }

    public function testCheckUserExistsByUsernameWithUser(): void
    {
        $this->loadTestUser();

        $exists = $this->repository->existsByUsername('johndoe');

        $this->assertTrue($exists);
    }

    public function testCheckUserExistsByUsernameWithoutUser(): void
    {
        $exists = $this->repository->existsByUsername('jonhdoe');

        $this->assertFalse($exists);
    }

    private function loadTestUser(): void
    {
        $user = new User('johndoe@gmail.com', 'password123!', 'johndoe');

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}
