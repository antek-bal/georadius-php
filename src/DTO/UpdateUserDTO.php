<?php

declare(strict_types=1);

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

#[Assert\Expression(
    expression: 'this.username !== null or this.password !== null',
    message: 'At least one field (username or password) must be provided.'
)]
final readonly class UpdateUserDTO
{
    public function __construct(
        #[Assert\Length(
            min: 3,
            max: 30,
            minMessage: 'The username must be at least {{ limit }} characters long.',
            maxMessage: 'Username cannot be longer than {{ limit }} characters.'
        )]
        #[Assert\Regex(
            pattern: '/^[a-zA-Z0-9_-]+$/',
            message: 'The username may only contain letters, numbers, dashes and underscores.'
        )]
        public ?string $username = null,

        #[Assert\Length(
            min: 8,
            max: 4096,
            minMessage: 'The password must be at least {{ limit }} characters long.',
            maxMessage: 'The password is too long.'
        )]
        public ?string $password = null,
    ) {
    }
}
