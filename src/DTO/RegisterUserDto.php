<?php

declare(strict_types=1);

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class RegisterUserDto
{
    public function __construct(
        #[Assert\NotBlank(message: 'Email address cannot be blank.')]
        #[Assert\Email(message: 'The email address is not valid.')]
        #[Assert\Length(max: 180, maxMessage: 'The email address cannot be longer than {{ limit }} characters.')]
        public string $email,

        #[Assert\NotBlank(message: 'Password cannot be blank.')]
        #[Assert\Length(
            min: 8,
            max: 4096,
            minMessage: 'The password must be at least {{ limit }} characters long.',
            maxMessage: 'The password is too long.'
        )]
        public string $password,

        #[Assert\NotBlank(message: 'Username cannot be blank.')]
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
        public string $username,
    ) {
    }
}
