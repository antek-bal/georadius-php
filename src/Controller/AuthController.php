<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\RegisterUserDto;
use App\Service\UserManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

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
}
