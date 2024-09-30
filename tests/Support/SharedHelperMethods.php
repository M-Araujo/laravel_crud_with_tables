<?php

namespace Tests\Support;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Mockery;
use App\Models\User;

abstract class SharedHelperMethods extends \Tests\TestCase
{
    use RefreshDatabase;

    protected function generateCsrfToken(): string
    {
        $this->startSession();
        return csrf_token();
    }


    protected function authenticateUser($user = null): void
    {
        $user = $user ?? User::factory()->create();
        $this->actingAs($user);
    }

    protected function setupTestData()
    {
        $colours = \App\Models\Colour::factory()->count(5)->create();
        $country = \App\Models\Country::factory()->create();
        return compact('colours', 'country');
    }


    protected function fakeProfilePicture(string $fileName = 'avatar.jpg'): UploadedFile
    {
        return UploadedFile::fake()->image($fileName);
    }

    protected function assertSuccessMessage($response, $message = 'Item updated with success.')
    {
        $response->assertSessionHas('success_message', $message);
    }

    protected function mockStorageDisk($profilePicturePath): Filesystem
    {
        $storageDiskMock = Mockery::mock(Filesystem::class);

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


    protected function mockDeleteStorageDisk($profilePicturePath): Filesystem
    {
        $storageDiskMock = Mockery::mock(Filesystem::class);

        $storageDiskMock->shouldReceive('exists')
            ->with($profilePicturePath)
            ->andReturn(true);

        $storageDiskMock->shouldReceive('delete')
            ->with($profilePicturePath)
            ->andReturn(true);

        Storage::shouldReceive('disk')
            ->with('local')
            ->andReturn($storageDiskMock);

        return $storageDiskMock;
    }

    protected function storeFileAndVerify($profilePicturePath, $profilePictureFileName)
    {
        Storage::disk('local')->assertExists('public/users/' . $profilePictureFileName);
        $this->assertTrue(Storage::disk('local')->exists($profilePicturePath), 'Profile picture was not stored.');
    }


    protected function generateProfilePictureAndCsrfToken(): array
    {
        $this->startSession();

        $csrfToken = csrf_token();

        $profilePicture = UploadedFile::fake()->image('avatar.jpg');

        return [
            'csrf_token' => $csrfToken,
            'profile_picture' => $profilePicture
        ];
    }

}
