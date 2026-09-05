<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class UserControllerTest extends WebTestCase
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

    private function createAndLoginUser(
        string $email = 'johndoe@gmail.com',
        string $username = 'johndoe',
        string $password = 'password123!',
    ): User {
        $user = new User($email, $password, $username);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->client->loginUser($user);

        return $user;
    }

    public function testGetMeSuccess(): void
    {
        $user = $this->createAndLoginUser();

        $this->client->request('GET', '/api/users/me');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertResponseHeaderSame('content-type', 'application/json');

        $content = $this->client->getResponse()->getContent();
        $this->assertIsString($content);

        $responseData = json_decode($content, true);
        $this->assertIsArray($responseData);
        $this->assertSame($user->getEmail(), $responseData['email']);
        $this->assertSame($user->getUsername(), $responseData['username']);
    }

    public function testEditMeSuccess(): void
    {
        $this->createAndLoginUser();

        $payload = json_encode([
            'username' => 'john_doe_updated',
        ]);
        assert(is_string($payload));

        $this->client->request(
            'PATCH',
            '/api/users/me',
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
        $this->assertSame('john_doe_updated', $responseData['username']);

        $em = static::getContainer()->get(EntityManagerInterface::class);
        assert($em instanceof EntityManagerInterface);
        $userInDb = $em->getRepository(User::class)->findOneBy(['email' => 'johndoe@gmail.com']);

        $this->assertNotNull($userInDb);
        $this->assertSame('john_doe_updated', $userInDb->getUsername());
    }

    public function testEditMeConflictWhenUsernameAlreadyTaken(): void
    {
        $otherUser = new User('other_johndoe@gmail.com', 'password123!', 'taken_johndoe');
        $this->entityManager->persist($otherUser);
        $this->entityManager->flush();

        $this->createAndLoginUser('johndoe@gmail.com', 'johndoe');

        $payload = json_encode([
            'username' => 'taken_johndoe',
        ]);
        assert(is_string($payload));

        $this->client->request(
            'PATCH',
            '/api/users/me',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            $payload
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_CONFLICT);
        $this->assertResponseHeaderSame('content-type', 'application/json');

        $content = $this->client->getResponse()->getContent();
        $this->assertIsString($content);

        $responseData = json_decode($content, true);
        $this->assertIsArray($responseData);
        $this->assertSame('Username is already taken.', $responseData['message']);
    }

    public function testDeleteMeSuccess(): void
    {
        $this->createAndLoginUser('johndoe@gmail.com', 'johndoe');

        $this->client->request('DELETE', '/api/users/me');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertResponseHeaderSame('content-type', 'application/json');

        $content = $this->client->getResponse()->getContent();
        $this->assertIsString($content);

        $responseData = json_decode($content, true);
        $this->assertIsArray($responseData);
        $this->assertSame('User deleted successfully.', $responseData['message']);

        $em = static::getContainer()->get(EntityManagerInterface::class);
        assert($em instanceof EntityManagerInterface);
        $userInDb = $em->getRepository(User::class)->findOneBy(['email' => 'johndoe@gmail.com']);

        $this->assertNull($userInDb);
    }
}
