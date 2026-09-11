<?php

namespace Tests\Feature;

use App\Models\Availability;
use App\Models\Booking;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookPageSlotsTest extends TestCase
{
    use RefreshDatabase;

    public function test_book_page_shows_empty_state_when_no_availabilities(): void
    {
        $response = $this->get(route('book.index'));

        $response->assertStatus(200);
        $response->assertSee('Свободных слотов пока нет');
    }

    public function test_book_page_shows_free_slots_in_availability_windows(): void
    {
        $weekday = (int) now()->format('N');

        Availability::create([
            'weekday' => $weekday,
            'start_time' => '10:00',
            'end_time' => '11:00',
        ]);

        $futureSlot = now()->copy()->startOfDay()->setTime(10, 0);
        while ($futureSlot <= now()) {
            $futureSlot = $futureSlot->addWeek();
        }

        $response = $this->get(route('book.index'));

        $response->assertStatus(200);
        $response->assertSee($futureSlot->toDateTimeString());
        $response->assertSee($futureSlot->format('H:i').'–'.$futureSlot->copy()->addMinutes(30)->format('H:i'));
    }

    public function test_book_page_excludes_past_slots(): void
    {
        $now = now();
        $weekday = (int) $now->format('N');

        Availability::create([
            'weekday' => $weekday,
            'start_time' => '00:00',
            'end_time' => '23:59',
        ]);

        $pastTime = $now->copy()->subMinutes(60)->setMinutes(0)->setSeconds(0);
        if ((int) $pastTime->format('N') !== $weekday) {
            $this->expectNotToPerformAssertions();

            return;
        }

        $response = $this->get(route('book.index'));

        $response->assertStatus(200);
        $response->assertDontSee($pastTime->toDateTimeString());
    }

    public function test_book_page_excludes_booked_slots(): void
    {
        $today = now()->startOfDay();
        $weekday = (int) $today->format('N');

        Availability::create([
            'weekday' => $weekday,
            'start_time' => '10:00',
            'end_time' => '11:00',
        ]);

        $bookedStart = $today->copy()->addDay();
        while ((int) $bookedStart->format('N') !== $weekday) {
            $bookedStart = $bookedStart->addDay();
        }
        $bookedStart = $bookedStart->setTime(10, 0);

        if ($bookedStart <= now()) {
            $bookedStart = $bookedStart->addWeek();
        }

        Booking::create([
            'slot_start_at' => $bookedStart->toDateTimeString(),
            'invitee_name' => 'Гость',
            'invitee_email' => 'guest@example.com',
        ]);

        $response = $this->get(route('book.index'));

        $response->assertStatus(200);
        $response->assertDontSee($bookedStart->toDateTimeString());
        $response->assertSee($bookedStart->format('H:i').'–'.$bookedStart->copy()->addMinutes(30)->format('H:i'));
        $response->assertSee('aria-disabled="true"', false);
    }

    public function test_book_page_excludes_slots_outside_availability_windows(): void
    {
        $today = now()->startOfDay();
        $weekday = (int) $today->format('N');

        Availability::create([
            'weekday' => $weekday,
            'start_time' => '10:00',
            'end_time' => '10:30',
        ]);

        $future = $today->copy()->addDay();
        while ((int) $future->format('N') !== $weekday) {
            $future = $future->addDay();
        }
        if ($future <= now()) {
            $future = $future->addWeek();
        }

        $response = $this->get(route('book.index'));

        $response->assertStatus(200);
        $response->assertDontSee($future->setTime(11, 0)->toDateTimeString());
        $response->assertDontSee($future->setTime(9, 30)->toDateTimeString());
    }

    public function test_book_page_aligns_slots_to_half_hour_boundaries(): void
    {
        $today = now()->startOfDay();
        $weekday = (int) $today->format('N');

        Availability::create([
            'weekday' => $weekday,
            'start_time' => '10:15',
            'end_time' => '11:45',
        ]);

        $future = $today->copy()->setTime(10, 30);
        while ($future <= now()) {
            $future = $future->addWeek();
        }

        $response = $this->get(route('book.index'));

        $response->assertStatus(200);
        $response->assertSee($future->toDateTimeString());
        $response->assertSee($future->copy()->setTime(11, 0)->toDateTimeString());
        $response->assertDontSee($future->copy()->setTime(10, 15)->toDateTimeString());
        $response->assertDontSee($future->copy()->setTime(11, 30)->toDateTimeString());
    }
}
