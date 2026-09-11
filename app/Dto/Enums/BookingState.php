<?php

namespace App\Dto\Enums;

enum BookingState: string
{
    case Confirmed = 'confirmed';
    case Canceled = 'canceled';
}
