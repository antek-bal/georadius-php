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
        $em = static::getContainer()->get(EntityManagerInterface::class);
        assert($em instanceof EntityManagerInterface);
        $this->entityManager = $em;

        $this->entityManager->createQuery('DELETE FROM App\Entity\Stats')->execute();
        $this->entityManager->createQuery('DELETE FROM App\Entity\Game')->execute();
        $this->entityManager->createQuery('DELETE FROM App\Entity\User')->execute();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
    }

    protected function registerUser(): void
    {
        $payload = json_encode([
            'email' => 'test@test.pl',
            'password' => 'password123!',
            'username' => 'test',
        ]);
        assert(is_string($payload));

        $this->client->request(
            'POST',
            '/api/register',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            $payload
        );
    }

    public function testRegisterSuccess(): void
    {
        $this->registerUser();

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $this->assertResponseHeaderSame('content-type', 'application/json');

        $content = $this->client->getResponse()->getContent();
        $this->assertIsString($content);

        $responseData = json_decode($content, true);
        $this->assertIsArray($responseData);
        $this->assertArrayHasKey('id', $responseData);
        $this->assertSame('test@test.pl', $responseData['email']);
        $this->assertSame('test', $responseData['username']);

        $em = static::getContainer()->get(EntityManagerInterface::class);
        assert($em instanceof EntityManagerInterface);
        $userInDb = $em->getRepository(User::class)->findOneBy(['email' => 'test@test.pl']);

        $this->assertNotNull($userInDb);
        $this->assertSame('test', $userInDb->getUsername());
    }

    public function testRegisterReturnsConflictWhenUserAlreadyExists(): void
    {
        $existingUser = new User('johndoe@gmail.com', 'password12333', 'johndoe');
        $this->entityManager->persist($existingUser);
        $this->entityManager->flush();

        $payload = json_encode([
            'email' => 'johndoe@gmail.com',
            'password' => 'password123!',
            'username' => 'john_doe',
        ]);
        assert(is_string($payload));

        $this->client->request(
            'POST',
            '/api/register',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            $payload
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_CONFLICT);

        $content = $this->client->getResponse()->getContent();
        $this->assertIsString($content);

        $responseData = json_decode($content, true);
        $this->assertIsArray($responseData);
        $this->assertSame('User with this email or username already exists.', $responseData['message']);
    }

    public function testLoginSuccessWithEmail(): void
    {
        $this->registerUser();
        $payload = json_encode([
            'email' => 'test@test.pl',
            'password' => 'password123!',
        ]);
        assert(is_string($payload));

        $this->client->request(
            'POST',
            '/api/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            $payload
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertResponseHeaderSame('content-type', 'application/json');

        $content = $this->client->getResponse()->getContent();
        $this->assertIsString($content);

        $responseData = json_decode($content, true);
        $this->assertIsArray($responseData);
        $this->assertArrayHasKey('user', $responseData);
        $this->assertArrayHasKey('token', $responseData);
    }

    // public function testLoginSuccessWithUsername(): void
    // {
    //     $this->registerUser();
    //     $payload = json_encode([
    //         'username' => 'test',
    //         'password' => 'password123!',
    //     ]);
    //     assert(is_string($payload));

    //     $this->client->request(
    //         'POST',
    //         '/api/login',
    //         [],
    //         [],
    //         ['CONTENT_TYPE' => 'application/json'],
    //         $payload
    //     );

    //     $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    //     $this->assertResponseHeaderSame('content-type', 'application/json');

    //     $content = $this->client->getResponse()->getContent();
    //     $this->assertIsString($content);

    //     $responseData = json_decode($content, true);
    //     $this->assertIsArray($responseData);
    //     $this->assertArrayHasKey('user', $responseData);
    //     $this->assertArrayHasKey('token', $responseData);
    // }

    public function testLoginInvalidCredentials(): void
    {
        $this->registerUser();
        $payload = json_encode([
            'email' => 'johndoe@test.pl',
            'password' => 'password123!',
        ]);
        assert(is_string($payload));

        $this->client->request(
            'POST',
            '/api/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            $payload
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
        $this->assertResponseHeaderSame('content-type', 'application/json');
    }
}
