<?php

namespace App\Services\Scheduling;

use App\Models\Availability;
use App\Models\Booking;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

class SlotGenerator
{
    public function __construct(private readonly int $slotMinutes = 30) {}

    /**
     * @param  Collection<int, Availability>  $availabilities
     * @return Collection<int, Slot>
     */
    public function generate(Collection $availabilities, CarbonImmutable $horizonStart, CarbonImmutable $horizonEnd): Collection
    {
        $slots = collect();

        foreach ($availabilities as $availability) {
            $day = $horizonStart->startOfDay();

            while ($day <= $horizonEnd) {
                if ((int) $day->format('N') === (int) $availability->weekday) {
                    $slots = $slots->merge(
                        $this->slotsForDay($availability, $day, $horizonStart, $horizonEnd)
                    );
                }

                $day = $day->addDay();
            }
        }

        $booked = Booking::whereIn('slot_start_at', $slots->map->start_at->map->toDateTimeString())->pluck('slot_start_at');

        return $slots
            ->sortBy(fn (Slot $slot) => $slot->start_at->toDateTimeString())
            ->map(fn (Slot $slot) => new Slot(
                $slot->start_at,
                $slot->end_at,
                $booked->doesntContain($slot->start_at->toDateTimeString()),
            ))
            ->values();
    }

    /**
     * @return Collection<int, Slot>
     */
    private function slotsForDay(Availability $availability, CarbonImmutable $day, CarbonImmutable $horizonStart, CarbonImmutable $horizonEnd): Collection
    {
        $windowStart = $day->setTimeFromTimeString($availability->start_time->format('H:i'));
        $windowEnd = $day->setTimeFromTimeString($availability->end_time->format('H:i'));

        $slotStart = $this->alignToHalfHour($windowStart);
        $slots = collect();

        while ($slotStart->copy()->addMinutes($this->slotMinutes) <= $windowEnd) {
            if ($slotStart >= $horizonStart && $slotStart <= $horizonEnd) {
                $slots->push(new Slot(
                    $slotStart->copy(),
                    $slotStart->copy()->addMinutes($this->slotMinutes),
                    true,
                ));
            }

            $slotStart = $slotStart->addMinutes($this->slotMinutes);
        }

        return $slots;
    }

    private function alignToHalfHour(CarbonImmutable $time): CarbonImmutable
    {
        $minute = (int) $time->format('i');

        if ($minute === 0) {
            return $time;
        }

        if ($minute <= 30) {
            return $time->setMinutes(30)->setSeconds(0);
        }

        return $time->addHour()->setMinutes(0)->setSeconds(0);
    }
}
