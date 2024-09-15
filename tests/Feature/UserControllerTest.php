<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Ensure output buffering is managed if necessary
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        ob_start();
    }

    public function testIndexDisplaysUserList()
    {
        $this->withoutExceptionHandling();

        // Arrange: Create some users
        $users = User::factory()->count(5)->create();

        // Use one of the created users for authentication
        $this->actingAs($users->first());

        // Act: Send a GET request to the index route
        $response = $this->get('/users');

        // Assert: Verify the response is correct
        $response->assertStatus(200);
        $response->assertViewIs('users.list');
        $response->assertViewHas('items');

        $items = $response->viewData('items');

        // Check the count
        $this->assertCount(5, $items, 'Expected 5 items, but found ' . $items->count());

        // Check the IDs
        $expectedIds = $users->pluck('id')->sort()->values()->all();
        $actualIds = $items->pluck('id')->sort()->values()->all();

        $this->assertEquals($expectedIds, $actualIds, 'The IDs of the items do not match the expected IDs');
    }


    protected function tearDown(): void
    {
        if (ob_get_length()) {
            ob_end_clean();
        }
        parent::tearDown();
    }
}
