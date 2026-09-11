<?php

namespace Tests\Feature;

use App\Mail\BookingCreatedMail;
use App\Models\Availability;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class BookingNotificationTest extends TestCase
{
    use RefreshDatabase;

    private function validSlotStart(): string
    {
        $now = now();
        $weekday = (int) $now->format('N');

        Availability::create([
            'weekday' => $weekday,
            'start_time' => '00:00',
            'end_time' => '23:59',
        ]);

        $slot = $now->copy()->startOfDay()->setTime(10, 0);
        while ($slot <= $now) {
            $slot = $slot->addWeek();
        }

        return $slot->toDateTimeString();
    }

    public function test_booking_creation_sends_email_to_guest_and_host(): void
    {
        Mail::fake();

        $slotStart = $this->validSlotStart();

        $this->post(route('book.store'), [
            'slot_start_at' => $slotStart,
            'invitee_name' => 'Анна',
            'invitee_email' => 'anna@example.com',
        ]);

        Mail::assertSent(BookingCreatedMail::class, 2);
        Mail::assertSent(BookingCreatedMail::class, fn (BookingCreatedMail $mail) => $mail->hasTo('anna@example.com'));
        Mail::assertSent(BookingCreatedMail::class, fn (BookingCreatedMail $mail) => $mail->hasTo(config('booking.host_email')));
    }

    public function test_booking_email_has_correct_subject_and_host_context(): void
    {
        Mail::fake();

        $slotStart = $this->validSlotStart();

        $this->post(route('book.store'), [
            'slot_start_at' => $slotStart,
            'invitee_name' => 'Анна',
            'invitee_email' => 'anna@example.com',
        ]);

        Mail::assertSent(BookingCreatedMail::class, fn (BookingCreatedMail $mail) => $mail->envelope()->subject === 'Запись на звонок создана');
        Mail::assertSent(BookingCreatedMail::class, fn (BookingCreatedMail $mail) => $mail->forHost === true);
        Mail::assertSent(BookingCreatedMail::class, fn (BookingCreatedMail $mail) => $mail->forHost === false);
    }

    public function test_no_email_sent_when_booking_rejected(): void
    {
        Mail::fake();

        $this->post(route('book.store'), [
            'slot_start_at' => now()->subHour()->toDateTimeString(),
            'invitee_name' => 'Анна',
            'invitee_email' => 'anna@example.com',
        ]);

        Mail::assertNothingSent();
    }
}
