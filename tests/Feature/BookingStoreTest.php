<?php

namespace Tests\Feature;

use App\Models\Availability;
use App\Models\Booking;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingStoreTest extends TestCase
{
    use RefreshDatabase;

    private function futureSlotOnWeekday(int $hour, int $minute = 0): array
    {
        $now = now();
        $weekday = (int) $now->format('N');

        Availability::create([
            'weekday' => $weekday,
            'start_time' => '00:00',
            'end_time' => '23:59',
        ]);

        $slot = $now->copy()->startOfDay()->setTime($hour, $minute);
        while ($slot <= $now) {
            $slot = $slot->addWeek();
        }

        return [$slot->toDateTimeString(), $weekday];
    }

    public function test_store_creates_booking_and_redirects_to_success(): void
    {
        [$slotStart] = $this->futureSlotOnWeekday(10, 0);

        $response = $this->post(route('book.store'), [
            'slot_start_at' => $slotStart,
            'invitee_name' => 'Анна',
            'invitee_email' => 'anna@example.com',
        ]);

        $booking = Booking::first();
        $response->assertRedirect(route('book.success', $booking));
        $this->assertDatabaseHas('bookings', [
            'slot_start_at' => $slotStart,
            'invitee_name' => 'Анна',
            'invitee_email' => 'anna@example.com',
        ]);
    }

    public function test_success_page_shows_booking_details(): void
    {
        [$slotStart] = $this->futureSlotOnWeekday(10, 0);

        $booking = Booking::create([
            'slot_start_at' => $slotStart,
            'invitee_name' => 'Анна',
            'invitee_email' => 'anna@example.com',
        ]);

        $response = $this->get(route('book.success', $booking));

        $response->assertStatus(200);
        $response->assertSee('Запись подтверждена');
        $response->assertSee('Анна');
        $response->assertSee('anna@example.com');
    }

    public function test_double_booking_rejected_with_friendly_error(): void
    {
        [$slotStart] = $this->futureSlotOnWeekday(10, 0);

        $this->post(route('book.store'), [
            'slot_start_at' => $slotStart,
            'invitee_name' => 'Первый',
            'invitee_email' => 'first@example.com',
        ]);

        $response = $this->post(route('book.store'), [
            'slot_start_at' => $slotStart,
            'invitee_name' => 'Второй',
            'invitee_email' => 'second@example.com',
        ]);

        $response->assertSessionHasErrors(['slot_start_at']);
        $this->assertDatabaseCount('bookings', 1);
    }

    public function test_unique_constraint_prevents_duplicate_at_db_level(): void
    {
        [$slotStart] = $this->futureSlotOnWeekday(10, 0);

        Booking::create([
            'slot_start_at' => $slotStart,
            'invitee_name' => 'Первый',
            'invitee_email' => 'first@example.com',
        ]);

        $this->expectException(UniqueConstraintViolationException::class);

        Booking::create([
            'slot_start_at' => $slotStart,
            'invitee_name' => 'Второй',
            'invitee_email' => 'second@example.com',
        ]);
    }

    public function test_store_rejects_missing_name(): void
    {
        [$slotStart] = $this->futureSlotOnWeekday(10, 0);

        $response = $this->post(route('book.store'), [
            'slot_start_at' => $slotStart,
            'invitee_email' => 'anna@example.com',
        ]);

        $response->assertSessionHasErrors(['invitee_name']);
        $this->assertDatabaseCount('bookings', 0);
    }

    public function test_store_rejects_invalid_email(): void
    {
        [$slotStart] = $this->futureSlotOnWeekday(10, 0);

        $response = $this->post(route('book.store'), [
            'slot_start_at' => $slotStart,
            'invitee_name' => 'Анна',
            'invitee_email' => 'not-an-email',
        ]);

        $response->assertSessionHasErrors(['invitee_email']);
        $this->assertDatabaseCount('bookings', 0);
    }

    public function test_store_rejects_past_slot(): void
    {
        $this->futureSlotOnWeekday(10, 0);

        $past = now()->subHour()->toDateTimeString();

        $response = $this->post(route('book.store'), [
            'slot_start_at' => $past,
            'invitee_name' => 'Анна',
            'invitee_email' => 'anna@example.com',
        ]);

        $response->assertSessionHasErrors(['slot_start_at']);
        $this->assertDatabaseCount('bookings', 0);
    }

    public function test_store_rejects_slot_outside_availability_window(): void
    {
        $now = now();
        $weekday = (int) $now->format('N');

        Availability::create([
            'weekday' => $weekday,
            'start_time' => '10:00',
            'end_time' => '10:30',
        ]);

        $outsideSlot = $now->copy()->startOfDay()->setTime(11, 0);
        while ($outsideSlot <= $now) {
            $outsideSlot = $outsideSlot->addWeek();
        }

        $response = $this->post(route('book.store'), [
            'slot_start_at' => $outsideSlot->toDateTimeString(),
            'invitee_name' => 'Анна',
            'invitee_email' => 'anna@example.com',
        ]);

        $response->assertSessionHasErrors(['slot_start_at']);
        $this->assertDatabaseCount('bookings', 0);
    }

    public function test_store_rejects_missing_slot(): void
    {
        $this->futureSlotOnWeekday(10, 0);

        $response = $this->post(route('book.store'), [
            'invitee_name' => 'Анна',
            'invitee_email' => 'anna@example.com',
        ]);

        $response->assertSessionHasErrors(['slot_start_at']);
        $this->assertDatabaseCount('bookings', 0);
    }
}
