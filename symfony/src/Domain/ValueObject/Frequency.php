<?php

namespace App\Domain\ValueObject;

enum Frequency: string
{
    case DAILY = 'daily';
    case WEEKLY = 'weekly';
}
