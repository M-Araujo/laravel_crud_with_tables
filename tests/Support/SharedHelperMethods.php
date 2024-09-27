<?php

namespace Tests\Support; // Or use Tests\Feature, depending on where you place it

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;
use Mockery;
use App\Models\User;

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



    // Mock storage disk
    protected function mockStorageDisk($profilePicturePath)
    {
        // Create a mock for the storage disk
        $storageDiskMock = Mockery::mock(Filesystem::class);

        // Define what happens when exists() and delete() are called on the mock
        $storageDiskMock->shouldReceive('exists')
            ->with($profilePicturePath)
            ->andReturn(true);

        $storageDiskMock->shouldReceive('put')
            ->with($profilePicturePath, Mockery::type('string'))
            ->andReturn(true);

        Storage::shouldReceive('disk')
            ->with('local')
            ->andReturn($storageDiskMock);

        return $storageDiskMock;
    }



    public function testDeleteUserSuccessfully()
    {
        $this->startSession();
        $this->withoutExceptionHandling();

        // Mock the storage disk and handle file paths
        $profilePictureFileName = time() . '.jpg';
        $profilePicturePath = 'public/users/' . $profilePictureFileName;
        $storageDiskMock = $this->mockStorageDisk($profilePicturePath);

        // Arrange: Create a user and simulate a stored profile picture
        $user = User::factory()->create();
        $user->update(['picture' => $profilePicturePath]);

        // Generate a valid CSRF token
        $csrfToken = csrf_token();

        // Act: Send a DELETE request to the destroy method
        $response = $this->delete("/users/{$user->id}", ['_token' => $csrfToken]);

        // Assert: Verify the response status and session message using the $response object
        $response->assertStatus(200);
        $response->assertSessionHas('success_message', 'Item deleted with success.');

        // Assert: The user was deleted from the database
        $this->assertDatabaseMissing('users', ['id' => $user->id]);

        // Verify that the delete method was called on the mocked disk
        $storageDiskMock->shouldHaveReceived('delete')->with($profilePicturePath)->once();
    }
}
