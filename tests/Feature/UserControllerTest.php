<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\Concerns\InteractsWithSession;
use App\Models\User;
use App\Models\Colour;
use App\Models\Country;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Tests\Support\SharedHelperMethods;
use Mockery;
use Log;

class UserControllerTest extends SharedHelperMethods
{
    use RefreshDatabase;
    use InteractsWithSession;

    protected function setUp(): void
    {
        parent::setUp();
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        ob_start();
        $this->startSession();
    }

    protected function generateCsrfToken(): string
    {
        return csrf_token();
    }


    public function testIndexDisplaysUserList()
    {
        $this->withoutExceptionHandling();

        $users = User::factory()->count(5)->create();

        $response = $this->get('/users');

        $response->assertStatus(200);
        $response->assertViewIs('users.list');
        $response->assertViewHas('items');

        $items = $response->viewData('items');
        foreach ($items as $item) {
            $this->assertArrayHasKey('id', $item->toArray());
            $this->assertArrayHasKey('name', $item->toArray());
            $this->assertArrayHasKey('email', $item->toArray());
            $this->assertArrayHasKey('picture', $item->toArray());
        }

        $this->assertCount(5, $items);
        $expectedIds = $users->pluck('id')->sort()->values()->all();
        $actualIds = $items->pluck('id')->sort()->values()->all();
        $this->assertEquals($expectedIds, $actualIds);
    }

    public function testCreateDisplaysForm()
    {
        $colours = Colour::factory()->count(5)->create();
        $countries = Country::factory()->count(3)->create();

        $response = $this->get('/users/create');

        $response->assertStatus(200);
        $response->assertViewIs('users.edit');
        $response->assertViewHas(['colours', 'countries']);
        $this->assertCount(5, $response->viewData('colours'));
        $this->assertCount(3, $response->viewData('countries'));
    }

    public function testStoreSuccessfully()
    {
        $formData = $this->generateProfilePictureAndCsrfToken();

        $csrfToken = $formData['csrf_token'];
        $profilePicture = $formData['profile_picture'];

        Storage::fake('local');

        $this->authenticateUser();

        $colours = Colour::factory()->count(3)->create();
        $country = Country::factory()->create();

        $profilePictureFileName = time() . '.' . $profilePicture->getClientOriginalExtension();

        $data = [
            '_token' => $csrfToken,
            'name' => 'Alice Johnson',
            'email' => 'alice.johnson@example.com',
            'password' => 'Password@123',
            'password_confirmation' => 'Password@123',
            'has_kids' => 1,
            'country_id' => $country->id,
            'colours_id' => $colours->pluck('id')->toArray(),
            'picture' => $profilePicture,
        ];

        $response = $this->post('/users', $data);

        $response->assertRedirect('/users');
        $response->assertSessionHas('success_message', 'Item created successfully.');

        $createdUser = User::where('email', 'alice.johnson@example.com')->first();
        $this->assertNotNull($createdUser);

        Storage::disk('local')->assertExists('public/users/' . $profilePictureFileName);

        $this->assertEquals(Config::get('app.url') . '/storage/users/' . $profilePictureFileName, $createdUser->picture);
    }


    public function testUpdateUserSuccessfully()
    {
        $this->withoutExceptionHandling();
        Storage::fake('local');

        $user = User::factory()->create();
        $colours = Colour::factory()->count(3)->create();
        $country = Country::factory()->create();

        $authenticatedUser = User::factory()->create();
        $this->actingAs($authenticatedUser);

        $uploadedFile = UploadedFile::fake()->image('new_avatar.jpg');
        $customFileName = time() . '.' . $uploadedFile->getClientOriginalExtension();

        $data = [
            '_token' => csrf_token(),
            'name' => 'Updated Name',
            'email' => 'updated.email@example.com',
            'has_kids' => 0,
            'country_id' => $country->id,
            'colours_id' => $colours->pluck('id')->toArray(),
            'picture' => $uploadedFile,
        ];

        $response = $this->put("/users/{$user->id}", $data);

        $response->assertRedirect('/users');
        $response->assertSessionHas('success_message');

        $user->refresh();
        $this->assertEquals(Config::get('app.url') . Storage::url('public/users/' . $customFileName), $user->picture);
        Storage::disk('local')->assertExists('public/users/' . $customFileName);

        $this->assertDatabaseHas('user_countries', [
            'user_id' => $user->id,
            'country_id' => $country->id,
        ]);
    }

    public function testDeleteUserSuccessfully()
    {
        $this->withoutExceptionHandling();

        $storageDiskMock = Mockery::mock(\Illuminate\Contracts\Filesystem\Filesystem::class);
        Storage::shouldReceive('disk')->with('local')->andReturn($storageDiskMock);

        $user = User::factory()->create();
        $profilePictureFileName = time() . '.jpg';
        $profilePicturePath = 'public/users/' . $profilePictureFileName;

        $storageDiskMock->shouldReceive('exists')->with($profilePicturePath)->andReturn(true);
        $storageDiskMock->shouldReceive('delete')->with($profilePicturePath)->andReturn(true);

        $user->update(['picture' => $profilePicturePath]);

        $response = $this->delete("/users/{$user->id}", ['_token' => csrf_token()]);

        $response->assertStatus(200);
        $response->assertSessionHas('success_message');
        $this->assertDatabaseMissing('users', ['id' => $user->id]);

        $storageDiskMock->shouldHaveReceived('delete')->with($profilePicturePath)->once();
    }


    /*
    public function testStoreFailsWithValidationErrors()
    {
        // Start session and prevent exception handling to see the full error
        $this->startSession();
        $this->withoutExceptionHandling();

        // Act: Submit invalid data
        $response = $this->post('/users', [
            'name' => '',  // Invalid, should trigger validation error
            'email' => 'not-an-email',  // Invalid email format
            'password' => 'short',  // Too short password
        ]);

        // Assert: Check that the session contains validation errors
        $response->assertSessionHasErrors(['name', 'email', 'password']);

        // Optional: Check if the session has an error message
        $response->assertSessionHas('error_message', 'Oops, something is wrong.');

        // Assert: The user should not be created in the database
        $this->assertDatabaseMissing('users', ['email' => 'not-an-email']);
    }

*/




    protected function tearDown(): void
    {
        if (ob_get_length()) {
            ob_end_clean();
        }

        Mockery::close();
        parent::tearDown();
    }
}
