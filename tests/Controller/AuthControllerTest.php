<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class AuthControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);

        $this->entityManager->createQuery('DELETE FROM App\Entity\Stats')->execute();
        $this->entityManager->createQuery('DELETE FROM App\Entity\Game')->execute();
        $this->entityManager->createQuery('DELETE FROM App\Entity\User')->execute();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
    }

    public function testRegisterSuccess(): void
    {
        $this->client->request(
            'POST',
            '/api/register',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'email' => 'test@test.pl',
                'password' => 'password123!',
                'username' => 'test',
            ])
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $this->assertResponseHeaderSame('content-type', 'application/json');

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('id', $responseData);
        $this->assertSame('test@test.pl', $responseData['email']);
        $this->assertSame('test', $responseData['username']);

        $userInDb = $this->entityManager->getRepository(User::class)->findOneBy(['email' => 'test@test.pl']);
        $this->assertNotNull($userInDb);
        $this->assertSame('test', $userInDb->getUsername());
    }

    public function testRegisterReturnsConflictWhenUserAlreadyExists(): void
    {
        $existingUser = new User('johndoe@gmail.com', 'password12333', 'johndoe');
        $this->entityManager->persist($existingUser);
        $this->entityManager->flush();

        $this->client->request(
            'POST',
            '/api/register',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'email' => 'johndoe@gmail.com',
                'password' => 'password123!',
                'username' => 'john_doe',
            ])
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_CONFLICT);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertSame('User with this email or username already exists.', $responseData['message']);
    }
}
