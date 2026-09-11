<?php

namespace App\Services\Scheduling;

use Carbon\CarbonImmutable;

final class Slot
{
    public function __construct(
        public readonly CarbonImmutable $start_at,
        public readonly CarbonImmutable $end_at,
        public readonly bool $is_available = true,
    ) {}
}
