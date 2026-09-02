<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\RegisterUserDto;
use App\Entity\User;
use App\Service\UserManager;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/api', name: 'api_')]
class AuthController extends AbstractController
{
    public function __construct(
        private readonly UserManager $userManager,
    ) {
    }

    #[Route('/register', name: 'register', methods: ['POST'])]
    public function register(#[MapRequestPayload] RegisterUserDto $dto): JsonResponse
    {
        $user = $this->userManager->register(
            $dto->email,
            $dto->password,
            $dto->username
        );

        if (null === $user) {
            return $this->json(
                ['message' => 'User with this email or username already exists.'],
                Response::HTTP_CONFLICT
            );
        }

        return $this->json([
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'username' => $user->getUsername(),
        ], Response::HTTP_CREATED);
    }

    #[Route('/login', name: 'login', methods: ['POST'])]
    public function login(#[CurrentUser] ?User $user, JWTTokenManagerInterface $jwtManager): Response
    {
        if (null === $user) {
            return $this->json([
                'message' => 'Missing credentials.',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $token = $jwtManager->create($user);

        return $this->json([
            'user' => $user->getUserIdentifier(),
            'token' => $token,
        ]);
    }

    #[Route('/me', name: 'me', methods: ['GET'])]
    public function me(#[CurrentUser] ?User $user): JsonResponse
    {
        if (null === $user) {
            return $this->json(['message' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        return $this->json([
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'username' => $user->getUsername(),
            'roles' => $user->getRoles(),
        ]);
    }
}
