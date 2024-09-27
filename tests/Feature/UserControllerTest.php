<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\Concerns\InteractsWithSession;
use Tests\TestCase;
use App\Models\User;
use App\Models\Colour;
use App\Models\Country;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Tests\Support\SharedHelperMethods;
use Log;

class UserControllerTest extends SharedHelperMethods
{
    use RefreshDatabase;
    use InteractsWithSession;


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

        // Act: Send a GET request to the index route
        $response = $this->get('/users');

        // Assert: Verify the response is correct
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

        // Act: Send a GET request to the create route
        $response = $this->get('/users/create');

        // Assert: Verify the response
        $response->assertStatus(200);
        $response->assertViewIs('users.edit');
        $response->assertViewHas('colours');
        $response->assertViewHas('countries');

        // Check that the correct data is passed
        $this->assertCount(5, $response->viewData('colours'));
        $this->assertCount(3, $response->viewData('countries'));

        // Additional assertions for checking IDs
        $expectedColourIds = $colours->pluck('id')->sort()->values()->all();
        $actualColourIds = $response->viewData('colours')->pluck('id')->sort()->values()->all();
        $this->assertEquals($expectedColourIds, $actualColourIds);

        $expectedCountryIds = $countries->pluck('id')->sort()->values()->all();
        $actualCountryIds = $response->viewData('countries')->pluck('id')->sort()->values()->all();
        $this->assertEquals($expectedCountryIds, $actualCountryIds);
    }



    /*
        public function testStoreSuccessfully()
        {
            $this->startSession();
            $this->withoutExceptionHandling();
            Storage::fake('local'); // Fake the 'local' disk for testing file storage

            // Arrange: Create necessary data
            $colours = Colour::factory()->count(3)->create();
            $country = Country::factory()->create();


            // Generate a CSRF token
            $csrfToken = csrf_token();

            // Simulate a file upload for the user's profile picture
            $profilePicture = UploadedFile::fake()->image('avatar.jpg');
            $profilePictureFileName = time() . '.' . $profilePicture->getClientOriginalExtension();

            $data = [
                '_token' => $csrfToken, // Explicitly add CSRF token here
                'name' => 'Alice Johnson',
                'email' => 'alice.johnson@example.com',
                'password' => 'Password@123',
                'password_confirmation' => 'Password@123', // Use password_confirmation instead of password_confirm
                'has_kids' => 1,
                'country_id' => $country->id,
                'colours_id' => $colours->pluck('id')->toArray(),
                'picture' => $profilePicture, // UploadedFile instance
            ];

            // Act: Send a POST request to the store method
            $response = $this->post('/users', $data);

            // Assert: Check that the response redirects and has the success message
            $response->assertRedirect('/users');
            $response->assertSessionHas('success_message', 'Item updated with success.');

            // Assert: Verify the user was created in the database
            $createdUser = User::where('email', 'alice.johnson@example.com')->first();
            $this->assertNotNull($createdUser);

            //  dump(Storage::disk('local')->allFiles()); // Should show files in the fake disk
            //  dump(File::files(storage_path('app/public/storage/testing_users')));

            $this->app->detectEnvironment(fn() => 'testing');
            // Assert the file was stored in the 'public' disk with the correct custom name
            Storage::disk('local')->assertExists('public/users/' . $profilePictureFileName);

            // Assert the picture value in the database matches the expected custom filename
            $this->assertEquals(Config::get('app.url') . Storage::url('public/users/' . $profilePictureFileName), $createdUser->picture);
        }
    */
    public function testStoreSuccessfully()
    {
        // Start session and prevent exception handling to see full errors
        $this->startSession();
        $this->withoutExceptionHandling();

        // Fake the 'local' storage to prevent actual file creation
        Storage::fake('local');

        // Arrange: Use shared helper methods for generating test data
        $this->authenticateUser();  // Authenticate a user for this request
        $testData = $this->setupTestData();  // Set up test countries and colors

        // Use shared helper for CSRF token and profile picture
        $csrfToken = $this->generateCsrfToken();
        $profilePicture = $this->fakeProfilePicture();  // Generate a fake profile picture
        $profilePictureFileName = time() . '.' . $profilePicture->getClientOriginalExtension();

        $data = [
            '_token' => $csrfToken,  // Add CSRF token
            'name' => 'Alice Johnson',
            'email' => 'alice.johnson@example.com',
            'password' => 'Password@123',
            'password_confirmation' => 'Password@123',
            'has_kids' => 1,
            'country_id' => $testData['country']->id,
            'colours_id' => $testData['colours']->pluck('id')->toArray(),
            'picture' => $profilePicture,  // Use the fake profile picture
        ];

        // Act: Send POST request to store the user
        $response = $this->post('/users', $data);

        // Assert: Check that the response redirects and has the success message
        $response->assertRedirect('/users');
        $this->assertSuccessMessage($response);  // Use helper to check session message

        // Assert: Verify that the user was created in the database
        $createdUser = User::where('email', 'alice.johnson@example.com')->first();
        $this->assertNotNull($createdUser);

        // Assert: The file was stored in the 'local' disk with the correct custom name
        Storage::disk('local')->assertExists('public/users/' . $profilePictureFileName);

        // Assert: The picture value in the database matches the expected custom filename
        $this->assertEquals(
            Config::get('app.url') . Storage::url('public/users/' . $profilePictureFileName),
            $createdUser->picture
        );
    }


    public function testUpdateUserSuccessfully()
    {
        $this->startSession();  // Start the session for CSRF handling
        $this->withoutExceptionHandling();  // Disable exception handling to see errors

        // Arrange: Create a user and related data
        $user = User::factory()->create();
        $colours = Colour::factory()->count(3)->create();
        $country = Country::factory()->create();
        Storage::fake('local');  // Fake the 'local' disk for testing

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
        $this->assertEquals(Config::get('app.url') . Storage::url('public/users/' . $customFileName), $user->picture);

        // Assert the file was stored in the 'local' disk with the correct custom name
        Storage::disk('local')->assertExists('public/users/' . $customFileName);  // Assert that the custom file name exists

        // Assert the country relationship is updated correctly in the pivot table
        $this->assertDatabaseHas('user_countries', [
            'user_id' => $user->id,
            'country_id' => $country->id,
        ]);
    }



    public function testDeleteUserSuccessfully()
    {
        $this->startSession();
        $this->withoutExceptionHandling();

        // Mock the disk instance, not the entire Storage facade
        $storageDiskMock = \Mockery::mock(\Illuminate\Contracts\Filesystem\Filesystem::class);

        // When Storage::disk('local') is called, return our mocked disk
        \Illuminate\Support\Facades\Storage::shouldReceive('disk')
            ->with('local')
            ->andReturn($storageDiskMock);

        // Arrange: Create a user and simulate a stored profile picture
        $user = User::factory()->create();
        $profilePictureFileName = time() . '.jpg';
        $profilePicturePath = 'public/users/' . $profilePictureFileName;

        // Simulate the file existing before deletion
        $storageDiskMock->shouldReceive('exists')
            ->with($profilePicturePath)
            ->andReturn(true);

        // Simulate the file being deleted
        $storageDiskMock->shouldReceive('delete')
            ->with($profilePicturePath)
            ->andReturn(true);

        // Update the user's picture field in the database with the relative path
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


    protected function tearDown(): void
    {
        if (ob_get_length()) {
            ob_end_clean();
        }


        \Mockery::close();
        parent::tearDown();
    }
}
