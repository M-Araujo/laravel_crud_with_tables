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



    public function testStoreFailsWithMultipleValidationErrors()
    {
        $this->startSession();
        $csrfToken = csrf_token();

        $response = $this->post('/users', [
            '_token' => $csrfToken,
            'name' => '',  // Missing name
            'email' => 'invalid-email', // Invalid email
            'password' => 'short', // Password too short
            'password_confirmation' => 'does-not-match', // Mismatched password confirmation

        ]);

        $response->assertSessionHasErrors(['name', 'email', 'password', 'password_confirmation']);
    }


    public function testStoreSuccessfullyCreatesUser()
    {
        // Start session and generate CSRF token
        $this->startSession();
        $csrfToken = csrf_token();

        // Dynamically create a country and colours
        $country = Country::factory()->create(); // Create a country dynamically
        $colours = Colour::factory()->count(2)->create(); // Create two colours dynamically

        $validData = [
            '_token' => $csrfToken,
            'name' => 'Alice Johnson',
            'email' => 'alice.johnson@example.com',
            'password' => 'Password@123',
            'password_confirmation' => 'Password@123',
            'has_kids' => 1,
            'country_id' => $country->id,
            'colours_id' => $colours->pluck('id')->toArray(),
        ];

        $response = $this->post('/users', $validData);

        $response->assertRedirect('/users');
        $response->assertSessionHas('success_message', 'Item created successfully.');

        $this->assertDatabaseHas('users', [
            'email' => 'alice.johnson@example.com',
            'has_kids' => 1,
        ]);

        $createdUser = User::where('email', 'alice.johnson@example.com')->first();

        $this->assertEquals($country->id, $createdUser->country->country_id);
        $this->assertCount(2, $createdUser->colours);
    }


    public function testStoreFailsWithDuplicateEmail()
    {
        $this->startSession();
        $csrfToken = csrf_token();

        // Create an existing user
        $existingUser = User::factory()->create(['email' => 'existing@example.com']);

        $response = $this->post('/users', [
            '_token' => $csrfToken,
            'name' => 'New User',
            'email' => 'existing@example.com', // Duplicate email
            'password' => 'Password@123',
            'password_confirmation' => 'Password@123',
        ]);

        $response->assertSessionHasErrors(['email']); // Assert email validation error
    }

    public function testStoreFailsWithInvalidImageUpload()
    {
        $this->startSession();
        $csrfToken = csrf_token();

        $country = Country::factory()->create();
        $colours = Colour::factory()->count(2)->create();

        // Upload an invalid image file (e.g., a text file)
        $invalidPicture = UploadedFile::fake()->create('document.txt', 100); // Invalid file type

        $response = $this->post('/users', [
            '_token' => $csrfToken,
            'name' => 'Alice Johnson',
            'email' => 'alice.johnson@example.com',
            'password' => 'Password@123',
            'password_confirmation' => 'Password@123',
            'has_kids' => 1,
            'country_id' => $country->id,
            'colours_id' => $colours->pluck('id')->toArray(),
            'picture' => $invalidPicture, // Invalid picture type
        ]);

        $response->assertSessionHasErrors(['picture']);
    }

    public function testStoreSuccessfullyWithoutProfilePicture()
    {
        $this->startSession();
        $csrfToken = csrf_token();

        $country = Country::factory()->create();
        $colours = Colour::factory()->count(2)->create();

        $validData = [
            '_token' => $csrfToken,
            'name' => 'Alice Johnson',
            'email' => 'alice.johnson@example.com',
            'password' => 'Password@123',
            'password_confirmation' => 'Password@123',
            'has_kids' => 1,
            'country_id' => $country->id,
            'colours_id' => $colours->pluck('id')->toArray(),
            // No 'picture' field here
        ];

        $response = $this->post('/users', $validData);

        $response->assertRedirect('/users');
        $response->assertSessionHas('success_message', 'Item created successfully.');

        $this->assertDatabaseHas('users', ['email' => 'alice.johnson@example.com']);
    }


    public function testUpdateUserSuccessfullyWithoutChangingPicture()
    {
        $this->withoutExceptionHandling();
        Storage::fake('local');

        $oldPicturePath = 'public/users/old_avatar.jpg';
        $user = User::factory()->create(['picture' => $oldPicturePath]);
        $colours = Colour::factory()->count(3)->create();
        $country = Country::factory()->create();

        $authenticatedUser = User::factory()->create();
        $this->actingAs($authenticatedUser);

        $data = [
            '_token' => csrf_token(),
            'name' => 'Updated Name',
            'email' => 'updated.email@example.com',
            'has_kids' => 0,
            'country_id' => $country->id,
            'colours_id' => $colours->pluck('id')->toArray(),
            // No 'picture' field, keeping the old picture
        ];

        $response = $this->put("/users/{$user->id}", $data);

        $response->assertRedirect('/users');
        $response->assertSessionHas('success_message');

        $user->refresh();

        $expectedPictureUrl = Config::get('app.url') . '/storage/users/old_avatar.jpg';
        $this->assertEquals($expectedPictureUrl, $user->picture);
    }


    protected function tearDown(): void
    {
        if (ob_get_length()) {
            ob_end_clean();
        }

        Mockery::close();
        parent::tearDown();
    }
}
