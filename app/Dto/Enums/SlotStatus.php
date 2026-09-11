<?php

namespace App\Dto\Enums;

enum SlotStatus: string
{
    case Available = 'available';
    case Booked = 'booked';
}
