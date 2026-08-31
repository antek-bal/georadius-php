<?php

declare(strict_types=1);

namespace App\Enum;

enum SubscriptionType: string
{
    case MONTHLY = 'monthly';
    case YEARLY = 'yearly';
    case TRIAL = 'trial';
}
