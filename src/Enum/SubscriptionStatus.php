<?php

namespace App\Enum;

enum SubscriptionStatus: string
{
    case ACTIVE = 'active';
    case TRIALING = 'trialing';
    case PAST_DUE = 'past_due';
    case CANCELED = 'canceled';
    case ENDED = 'ended';
    case INCOMPLETE = 'incomplete';
}