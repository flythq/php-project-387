<?php

namespace Tests\Feature\Api;

use Tests\TestCase;

class SmokeTest extends TestCase
{
    public function test_list_availability_returns_200(): void
    {
        $response = $this->getJson('/api/availability?from=2025-01-01T00:00:00Z&to=2025-01-02T00:00:00Z');

        $response->assertStatus(200);
        $response->assertJsonIsArray();
    }

    public function test_list_availability_requires_query_params(): void
    {
        $response = $this->getJson('/api/availability');

        $response->assertStatus(422);
    }

    public function test_create_booking_returns_201(): void
    {
        $response = $this->postJson('/api/bookings', [
            'slotId' => '00000000-0000-0000-0000-000000000000',
            'invitee' => [
                'name' => 'Jane Doe',
                'email' => 'jane@example.com',
            ],
        ]);

        $response->assertStatus(201);
    }

    public function test_create_booking_validates_body(): void
    {
        $response = $this->postJson('/api/bookings', [
            'slotId' => 'not-a-uuid',
            'invitee' => [
                'name' => 'Jane Doe',
            ],
        ]);

        $response->assertStatus(422);
    }

    public function test_get_booking_returns_404_for_unknown_id(): void
    {
        $response = $this->getJson('/api/bookings/00000000-0000-0000-0000-000000000000');

        $response->assertStatus(404);
    }
}
