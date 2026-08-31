<?php

declare(strict_types=1);

namespace App\Enum;

enum GameType: string
{
    case FREE = 'free';
    case DAILY = 'daily';
}
