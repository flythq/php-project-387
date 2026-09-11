<?php

namespace Tests\Feature;

use Tests\TestCase;

class SmokeTest extends TestCase
{
    public function test_health_check_endpoint_is_available(): void
    {
        $response = $this->get('/up');

        $response->assertStatus(200);
    }
}
