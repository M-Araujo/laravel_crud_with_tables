<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Colour;
use App\Models\Country;
use App\Services\DashboardStats;
use PHPUnit\Framework\Attributes\Test;

class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        ob_start();
    }

    #[Test]
    public function testUserIndexViewCanBeRendered()
    {
        $response = $this->get('/'); // Adjust the URL as necessary

        // Assert: Make assertions to ensure outcomes meet expectations
        $response->assertStatus(200); // Check if the HTTP status is 200
        $response->assertViewIs('dashboard'); // Ensure the correct view is being returned
        $response->assertViewHas('stats'); // Ensure data is being passed to the view
    }

    #[Test]
    public function testDashboardStatsAreCorrect()
    {
        // Arrange: Set up known conditions

        // Create countries
        Country::factory()->count(3)->create();

        // Create colours
        Colour::factory()->count(5)->create();

        // Create users with and without kids
        User::factory()->count(10)->create(['has_kids' => 1]);
        User::factory()->count(20)->create(['has_kids' => 0]);

        // Act: Perform the action
        $response = $this->get('/');

        // Assert: Check that the stats are correctly passed to the view
        $response->assertViewHas('stats', function ($stats) {
            return
                $stats['total_users'] === 30 &&
                $stats['total_countries'] === 3 &&
                $stats['total_colours'] === 5 &&
                $stats['has_kids'] === 10;
        });
    }

    protected function tearDown(): void
    {
        if (ob_get_length()) {
            ob_end_clean();
        }
        parent::tearDown();
    }
}