<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_returns_successful_response(): void
    {
        $response = $this->get(route('home.index'));

        $response->assertStatus(200);
        $response->assertSee('Записаться на звонок');
        $response->assertSee(route('book.index'));
    }

    public function test_book_page_returns_successful_response(): void
    {
        $response = $this->get(route('book.index'));

        $response->assertStatus(200);
    }
}
