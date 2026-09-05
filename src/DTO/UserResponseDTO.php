<?php

declare(strict_types=1);

namespace App\DTO;

use App\Entity\User;

final readonly class UserResponseDTO
{
    public function __construct(
        public int $id,
        public string $email,
        public string $username,
        public array $roles,
    ) {
    }

    public static function fromEntity(User $user): self
    {
        return new self(
            id: (int) $user->getId(),
            email: $user->getEmail(),
            username: $user->getUsername(),
            roles: $user->getRoles(),
        );
    }
}
