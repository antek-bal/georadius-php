<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\UpdateUserDTO;
use App\DTO\UserResponseDTO;
use App\Entity\User;
use App\Service\UserManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/api/users', name: 'api_users_')]
class UserController extends AbstractController
{
    public function __construct(
        private readonly UserManager $userManager,
    ) {
    }

    #[Route('/me', name: 'get', methods: ['GET'])]
    public function get(#[CurrentUser] User $user): JsonResponse
    {
        return $this->json(UserResponseDTO::fromEntity($user));
    }

    #[Route('/me', name: 'edit', methods: ['PATCH'])]
    public function edit(#[CurrentUser] User $user, #[MapRequestPayload] UpdateUserDTO $dto): JsonResponse
    {
        $editedUser = $this->userManager->edit($user, $dto);

        if (null === $editedUser) {
            return $this->json(['message' => 'Username is already taken.'], Response::HTTP_CONFLICT);
        }

        return $this->json(UserResponseDTO::fromEntity($editedUser));
    }

    #[Route('/me', name: 'delete', methods: ['DELETE'])]
    public function delete(#[CurrentUser] User $user): JsonResponse
    {
        $this->userManager->delete($user);

        return $this->json(['message' => 'User deleted successfully.'], Response::HTTP_OK);
    }
}
