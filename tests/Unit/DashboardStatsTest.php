<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Colour;
use App\Models\Country;
use App\Services\DashboardStats;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class DashboardStatsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Colour::factory()->count(5)->create();
        Country::factory()->count(3)->create();
        User::factory()->count(10)->create(['has_kids' => 1]);
        User::factory()->count(20)->create(['has_kids' => 0]);
    }

    #[Test]
    public function testStatsCalculation()
    {
        $statsService = new DashboardStats();
        $stats = $statsService->stats();


        $this->assertEquals(30, $stats['total_users'], 'Checks the total number of users');
        $this->assertEquals(5, $stats['total_colours'], 'Checks the total number of colours');
        $this->assertEquals(3, $stats['total_countries'], 'Checks the total number of countries');
        $this->assertEquals(10, $stats['has_kids'], 'Checks the number of users with kids');
        //$this->assertIsArray($stats['user_colours'], 'Ensures user colours data is an array');

        $this->assertNotNull($stats['user_colours'], 'user_colours should not be null');
        $this->assertTrue(is_array($stats['user_colours']) || $stats['user_colours'] instanceof \Illuminate\Support\Collection, 'user_colours should be an array or Collection');


        //$this->assertIsArray($stats['user_countries'], 'Ensures user countries data is an array');

        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $stats['user_countries'], 'Ensures user countries data is a Collection');
        $this->assertNotEmpty($stats['user_countries'], 'user_countries should not be empty');

        $this->assertCount(2, $stats['has_kids_chartdata'], 'Ensures data is grouped correctly by has_kids');
    }
}
