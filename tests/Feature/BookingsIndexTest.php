<?php

namespace Tests\Feature;

use App\Models\Booking;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingsIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_shows_empty_state_when_no_bookings(): void
    {
        $response = $this->get(route('bookings.index'));

        $response->assertStatus(200);
        $response->assertSee('Записей нет');
    }

    public function test_index_lists_bookings_sorted_by_slot_start_at_asc(): void
    {
        $earlier = Booking::create([
            'slot_start_at' => '2026-09-10 10:00:00',
            'invitee_name' => 'Борис',
            'invitee_email' => 'boris@example.com',
        ]);

        $later = Booking::create([
            'slot_start_at' => '2026-09-12 12:00:00',
            'invitee_name' => 'Анна',
            'invitee_email' => 'anna@example.com',
        ]);

        $response = $this->get(route('bookings.index'));

        $response->assertStatus(200);
        $response->assertSee('Записи на звонки');
        $response->assertSee('10.09.2026 10:00');
        $response->assertSee('12.09.2026 12:00');
        $response->assertSee('Борис');
        $response->assertSee('boris@example.com');
        $response->assertSee('Анна');
        $response->assertSee('anna@example.com');

        $content = $response->getContent();
        $this->assertLessThan(
            strpos($content, '12.09.2026 12:00'),
            strpos($content, '10.09.2026 10:00'),
            'Раньняя запись должна идти раньше поздней в выводе.'
        );
    }
}
