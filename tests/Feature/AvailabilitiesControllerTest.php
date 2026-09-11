<?php

namespace Tests\Feature;

use App\Models\Availability;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AvailabilitiesControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_successful_response_with_empty_state(): void
    {
        $response = $this->get(route('availabilities.index'));

        $response->assertStatus(200);
        $response->assertSee('Окна доступности');
        $response->assertSee('Добавить окно');
        $response->assertSee('Окна доступности не заданы');
    }

    public function test_create_returns_successful_response(): void
    {
        $response = $this->get(route('availabilities.create'));

        $response->assertStatus(200);
        $response->assertSee('Добавить окно доступности');
    }

    public function test_store_creates_availability_and_redirects(): void
    {
        $response = $this->post(route('availabilities.store'), [
            'weekday' => 1,
            'start_time' => '10:00',
            'end_time' => '18:00',
        ]);

        $response->assertRedirect(route('availabilities.index'));
        $this->assertDatabaseHas('availabilities', [
            'weekday' => 1,
        ]);
        $availability = Availability::first();
        $this->assertSame('10:00', $availability->start_time->format('H:i'));
        $this->assertSame('18:00', $availability->end_time->format('H:i'));
    }

    public function test_store_rejects_invalid_weekday(): void
    {
        $response = $this->post(route('availabilities.store'), [
            'weekday' => 9,
            'start_time' => '10:00',
            'end_time' => '18:00',
        ]);

        $response->assertSessionHasErrors('weekday');
        $this->assertDatabaseCount('availabilities', 0);
    }

    public function test_store_rejects_end_time_not_after_start(): void
    {
        $response = $this->post(route('availabilities.store'), [
            'weekday' => 1,
            'start_time' => '18:00',
            'end_time' => '10:00',
        ]);

        $response->assertSessionHasErrors('end_time');
        $this->assertDatabaseCount('availabilities', 0);
    }

    public function test_store_rejects_missing_fields(): void
    {
        $response = $this->post(route('availabilities.store'), []);

        $response->assertSessionHasErrors(['weekday', 'start_time', 'end_time']);
        $this->assertDatabaseCount('availabilities', 0);
    }

    public function test_edit_returns_successful_response(): void
    {
        $availability = Availability::create([
            'weekday' => 2,
            'start_time' => '09:00',
            'end_time' => '17:00',
        ]);

        $response = $this->get(route('availabilities.edit', $availability));

        $response->assertStatus(200);
        $response->assertSee('Изменить окно доступности');
    }

    public function test_update_modifies_availability_and_redirects(): void
    {
        $availability = Availability::create([
            'weekday' => 2,
            'start_time' => '09:00',
            'end_time' => '17:00',
        ]);

        $response = $this->put(route('availabilities.update', $availability), [
            'weekday' => 3,
            'start_time' => '11:00',
            'end_time' => '19:00',
        ]);

        $response->assertRedirect(route('availabilities.index'));
        $availability->refresh();
        $this->assertSame(3, $availability->weekday);
        $this->assertSame('11:00', $availability->start_time->format('H:i'));
        $this->assertSame('19:00', $availability->end_time->format('H:i'));
    }

    public function test_destroy_removes_availability_and_redirects(): void
    {
        $availability = Availability::create([
            'weekday' => 2,
            'start_time' => '09:00',
            'end_time' => '17:00',
        ]);

        $response = $this->delete(route('availabilities.destroy', $availability));

        $response->assertRedirect(route('availabilities.index'));
        $this->assertDatabaseMissing('availabilities', ['id' => $availability->id]);
    }
}
