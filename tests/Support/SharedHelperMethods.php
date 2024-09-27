<?php

namespace Tests\Support; // Or use Tests\Feature, depending on where you place it

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

abstract class SharedHelperMethods extends \Tests\TestCase
{
    use RefreshDatabase;

    // Helper for generating CSRF token
    protected function generateCsrfToken(): string
    {
        $this->startSession();
        return csrf_token();
    }

    // Helper to authenticate a user
    protected function authenticateUser($user = null): void
    {
        $user = $user ?? \App\Models\User::factory()->create();
        $this->actingAs($user);
    }

    // Setup test data for colours and countries
    protected function setupTestData()
    {
        $colours = \App\Models\Colour::factory()->count(5)->create();
        $country = \App\Models\Country::factory()->create();
        return compact('colours', 'country');
    }

    // Fake profile picture for user
    protected function fakeProfilePicture(): UploadedFile
    {
        return UploadedFile::fake()->image('avatar.jpg');
    }

    // Assert success message
    protected function assertSuccessMessage($response, $message = 'Item updated with success.')
    {
        $response->assertSessionHas('success_message', $message);
    }
}
