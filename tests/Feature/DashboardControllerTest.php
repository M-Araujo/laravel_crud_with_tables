<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DashboardControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        while (ob_get_level() > 0) {  // Ensure all buffers are cleared before each test
            ob_end_clean();
        }
        ob_start();  // Start output buffering
    }

    #[Test]
    public function testUserIndexViewCanBeRendered()
    {
        $response = $this->get('/');  // Adjust the URL as necessary

        // Assert: Make assertions to ensure outcomes meet expectations
        $response->assertStatus(200);  // Check if the HTTP status is 200
        $response->assertViewIs('dashboard');  // Ensure the correct view is being returned
        $response->assertViewHas('stats');  // Ensure data is being passed to the view

    }

    protected function tearDown(): void
    {
        if (ob_get_length()) {
            ob_end_clean();  // Ensure the buffer is cleaned up
        }
        parent::tearDown();
    }
}
