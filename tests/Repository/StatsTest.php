<?php

declare(strict_types=1);

namespace App\Tests\Repository;

use App\Entity\User;
use App\Repository\StatsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class StatsTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;
    private StatsRepository $repository;

    protected function setup(): void
    {
        self::bootKernel();

        $container = static::getContainer();

        $this->entityManager = $container->get('doctrine')->getManager();
        $this->repository = $container->get(StatsRepository::class);

        $this->entityManager->createQuery('DELETE FROM App\Entity\Stats')->execute();
        $this->entityManager->createQuery('DELETE FROM App\Entity\User')->execute();
    }

    public function testGetStatsWithExistingUser(): void
    {
        $user = $this->loadTestUsers();
        $stats = $this->repository->getStatsByUser($user);

        $this->assertNotNull($stats);
        $this->assertEquals($stats->getUser(), $user);
    }

    public function testGetStatsWithoutExistingUser(): void
    {
        $user = new User('abc@test.com', 'pass1234!!!', 'abc', ['ROLE_USER']);
        $stats = $this->repository->getStatsByUser($user);

        $this->assertNull($stats);
    }

    private function loadTestUsers(): User
    {
        $userTest = new User('johndoe@gmail.com', 'password123!', 'johndoe', ['ROLE_USER']);
        
        $this->entityManager->persist($userTest);
        $this->entityManager->flush();

        return $userTest;
    }
}