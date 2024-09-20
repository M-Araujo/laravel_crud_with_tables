<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Colour;
use App\Models\Country;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Log;

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


    public function testCreateDisplaysForm()
    {
        // Arrange: Create some colours and countries
        $colours = Colour::factory()->count(5)->create();
        $countries = Country::factory()->count(3)->create();

        // Use one of the users for authentication
        $user = User::factory()->create();
        $this->actingAs($user);

        // Act: Send a GET request to the create route
        $response = $this->get('/users/create');

        // Assert: Verify the response
        $response->assertStatus(200);
        $response->assertViewIs('users.edit'); // Assuming 'users.edit' is used for both create and edit
        $response->assertViewHas('colours');
        $response->assertViewHas('countries');

        // Optionally, check that the correct data is passed
        $this->assertCount(5, $response->viewData('colours'));
        $this->assertCount(3, $response->viewData('countries'));
    }




    public function testStoreSuccessfullyCreatesUserWithCsrf()
    {
        $this->startSession();
        $this->withoutExceptionHandling();

        // Arrange
        $colours = Colour::factory()->count(3)->create();
        $country = Country::factory()->create();
        Storage::fake('public');

        $authenticatedUser = User::factory()->create();

        // Simulate a logged-in user
        $this->actingAs($authenticatedUser);

        // Generate a CSRF token
        $csrfToken = csrf_token();

        $uploadedFile = UploadedFile::fake()->image('avatar.jpg');

        $data = [
            '_token' => $csrfToken, // Explicitly add CSRF token here
            'name' => 'Alice Johnson',
            'email' => 'alice.johnson@example.com',
            'password' => 'Password@123',
            'password_confirm' => 'Password@123',
            'has_kids' => 1,
            'country_id' => $country->id,
            'colours_id' => $colours->pluck('id')->toArray(),
            'picture' => $uploadedFile, // UploadedFile instance
        ];

        // Act: Send a POST request
        $response = $this->post('/users', $data);

        // Assert: Verify the response and database
        $response->assertRedirect('/users');
        $response->assertSessionHas('success_message', 'Item updated with success.');

        // Check that the user was created correctly
        $createdUser = User::where('email', 'alice.johnson@example.com')->first();
        $this->assertNotNull($createdUser);
    }




    /*


    
    public function testUpdateUserSuccessfully()
    {
        $this->startSession();  // Start the session for CSRF handling
        $this->withoutExceptionHandling();  // Disable exception handling to see errors

        // Arrange: Create a user and related data
        $user = User::factory()->create();
        $colours = Colour::factory()->count(3)->create();
        $country = Country::factory()->create();
        //   Storage::fake('public');  // Fake the 'public' disk

        // Simulate a logged-in user
        $authenticatedUser = User::factory()->create();
        $this->actingAs($authenticatedUser);

        // Generate a CSRF token
        $csrfToken = csrf_token();

        // Fake the image upload
        $uploadedFile = UploadedFile::fake()->image('new_avatar.jpg');

        // Generate a custom file name (same as in the controller)
        $customFileName = time() . '.' . $uploadedFile->getClientOriginalExtension();

        // Prepare the data for the update request
        $data = [
            '_token' => $csrfToken,  // Add CSRF token
            'name' => 'Updated Name',
            'email' => 'updated.email@example.com',
            'has_kids' => 0,
            'country_id' => $country->id,
            'colours_id' => $colours->pluck('id')->toArray(),
            'picture' => $uploadedFile,  // UploadedFile instance for the picture
        ];

        // Act: Send a PUT request to update the user
        $response = $this->put("/users/{$user->id}", $data);

        // Assert: Verify the response and database
        $response->assertRedirect('/users');
        $response->assertSessionHas('success_message', 'Item updated with success.');

        // Check that the user was updated in the database with the correct picture path
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
            'email' => 'updated.email@example.com',
            'has_kids' => 0,
            'picture' => 'public/users/' . $customFileName,  // Check for the correct custom file name
        ]);

        // Assert the file was stored in the 'public' disk with the correct custom name
        Storage::disk('public')->assertExists('users/' . $customFileName);  // Assert that the custom file name exists

        // Assert the country relationship is updated correctly in the pivot table
        $this->assertDatabaseHas('user_countries', [
            'user_id' => $user->id,
            'country_id' => $country->id,
        ]);
    }



    ///// funçao que funciona
    */



    public function testUpdateUserSuccessfully()
    {
        $this->startSession();  // Start the session for CSRF handling
        $this->withoutExceptionHandling();  // Disable exception handling to see errors

        // Arrange: Create a user and related data
        $user = User::factory()->create();
        $colours = Colour::factory()->count(3)->create();
        $country = Country::factory()->create();
        //  Storage::fake('public');  // Fake the 'public' disk for testing

        // Simulate a logged-in user
        $authenticatedUser = User::factory()->create();
        $this->actingAs($authenticatedUser);

        // Generate a CSRF token
        $csrfToken = csrf_token();

        // Fake the image upload
        $uploadedFile = UploadedFile::fake()->image('new_avatar.jpg');

        // Generate a custom file name (same as in the controller)
        $customFileName = time() . '.' . $uploadedFile->getClientOriginalExtension();

        // Prepare the data for the update request
        $data = [
            '_token' => $csrfToken,  // Add CSRF token
            'name' => 'Updated Name',
            'email' => 'updated.email@example.com',
            'has_kids' => 0,
            'country_id' => $country->id,
            'colours_id' => $colours->pluck('id')->toArray(),
            'picture' => $uploadedFile,  // UploadedFile instance for the picture
        ];

        // Act: Send a PUT request to update the user
        $response = $this->put("/users/{$user->id}", $data);

        // Assert: Verify the response and database
        $response->assertRedirect('/users');
        $response->assertSessionHas('success_message', 'Item updated with success.');

        // Reload the user to check the updated database value
        $user->refresh();

        // Assert the picture value in the database matches the expected custom filename
        $this->assertEquals(Config::get('app.url') . Storage::url('users/' . $customFileName), $user->picture);

        // Assert the file was stored in the 'public' disk with the correct custom name
        Storage::disk('public')->assertExists('users/' . $customFileName);  // Assert that the custom file name exists

        // Assert the country relationship is updated correctly in the pivot table
        $this->assertDatabaseHas('user_countries', [
            'user_id' => $user->id,
            'country_id' => $country->id,
        ]);
    }













    protected function tearDown(): void
    {
        if (ob_get_length()) {
            ob_end_clean();
        }
        parent::tearDown();
    }
}
