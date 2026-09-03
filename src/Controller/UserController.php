<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\UpdateUserDTO;
use App\Entity\User;
use App\Service\UserManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/api', name: 'api_')]
class UserController extends AbstractController
{
    public function __construct(
        private readonly UserManager $userManager,
    ) {
    }

    #[Route('/users/me', name: 'edit', methods: ['PATCH'])]
    public function edit(#[CurrentUser] User $user, #[MapRequestPayload] UpdateUserDTO $dto): JsonResponse
    {
        $editedUser = $this->userManager->edit($user, $dto);

        if (null === $editedUser) {
            return $this->json(['message' => 'Username is already taken.'], Response::HTTP_CONFLICT);
        }

        return $this->json([
            'id' => $editedUser->getId(),
            'email' => $editedUser->getEmail(),
            'username' => $editedUser->getUsername(),
            'roles' => $editedUser->getRoles(),
        ]);
    }

    #[Route('/users/me', name: 'get', methods: ['GET'])]
    public function get(#[CurrentUser] User $user): JsonResponse
    {
        return $this->json([
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'username' => $user->getUsername(),
            'roles' => $user->getRoles(),
        ]);
    }

    #[Route('/users/me', name: 'delete', methods: ['DELETE'])]
    public function delete(#[CurrentUser] User $user): JsonResponse
    {
        $this->userManager->delete($user);

        return $this->json(['message' => 'User deleted successfully.'], Response::HTTP_NO_CONTENT);
    }
}
