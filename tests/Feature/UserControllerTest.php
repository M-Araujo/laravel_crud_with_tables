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

        $this->assertCount(5, $items, 'Expected 5 items, but found ' . $items->count());

        $expectedIds = $users->pluck('id')->sort()->values()->all();
        $actualIds = $items->pluck('id')->sort()->values()->all();

        $this->assertEquals($expectedIds, $actualIds, 'The IDs of the items do not match the expected IDs');
    }


    public function testCreateDisplaysForm()
    {
        $colours = Colour::factory()->count(5)->create();
        $countries = Country::factory()->count(3)->create();

        $response = $this->get('/users/create');

        $response->assertStatus(200);
        $response->assertViewIs('users.edit');
        $response->assertViewHas('colours');
        $response->assertViewHas('countries');

        $this->assertCount(5, $response->viewData('colours'));
        $this->assertCount(3, $response->viewData('countries'));

        $expectedColourIds = $colours->pluck('id')->sort()->values()->all();
        $actualColourIds = $response->viewData('colours')->pluck('id')->sort()->values()->all();
        $this->assertEquals($expectedColourIds, $actualColourIds);

        $expectedCountryIds = $countries->pluck('id')->sort()->values()->all();
        $actualCountryIds = $response->viewData('countries')->pluck('id')->sort()->values()->all();
        $this->assertEquals($expectedCountryIds, $actualCountryIds);
    }




    public function testStoreSuccessfully()
    {

        $this->withoutExceptionHandling();
        Storage::fake('local');

        $this->authenticateUser();
        $testData = $this->setupTestData();

        $csrfToken = $this->generateCsrfToken();
        $profilePicture = $this->fakeProfilePicture();
        $profilePictureFileName = time() . '.' . $profilePicture->getClientOriginalExtension();

        $data = [
            '_token' => $csrfToken,
            'name' => 'Alice Johnson',
            'email' => 'alice.johnson@example.com',
            'password' => 'Password@123',
            'password_confirmation' => 'Password@123',
            'has_kids' => 1,
            'country_id' => $testData['country']->id,
            'colours_id' => $testData['colours']->pluck('id')->toArray(),
            'picture' => $profilePicture,
        ];


        $response = $this->post('/users', $data);

        $response->assertRedirect('/users');
        $this->assertSuccessMessage($response);

        $createdUser = User::where('email', 'alice.johnson@example.com')->first();
        $this->assertNotNull($createdUser);

        Storage::disk('local')->assertExists('public/users/' . $profilePictureFileName);

        $this->assertEquals(
            Config::get('app.url') . '/storage/users/' . $profilePictureFileName,
            $createdUser->picture
        );

    }




    public function testUpdateUserSuccessfully()
    {

        $this->withoutExceptionHandling();

        $user = User::factory()->create();
        $colours = Colour::factory()->count(3)->create();
        $country = Country::factory()->create();
        Storage::fake('local');

        $authenticatedUser = User::factory()->create();
        $this->actingAs($authenticatedUser);

        $csrfToken = csrf_token();

        $uploadedFile = UploadedFile::fake()->image('new_avatar.jpg');

        $customFileName = time() . '.' . $uploadedFile->getClientOriginalExtension();

        $data = [
            '_token' => $csrfToken,
            'name' => 'Updated Name',
            'email' => 'updated.email@example.com',
            'has_kids' => 0,
            'country_id' => $country->id,
            'colours_id' => $colours->pluck('id')->toArray(),
            'picture' => $uploadedFile,
        ];


        $response = $this->put("/users/{$user->id}", $data);

        $response->assertRedirect('/users');
        $response->assertSessionHas('success_message', 'Item updated with success.');

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

        $storageDiskMock = \Mockery::mock(\Illuminate\Contracts\Filesystem\Filesystem::class);

        Storage::shouldReceive('disk')
            ->with('local')
            ->andReturn($storageDiskMock);

        $user = User::factory()->create();
        $profilePictureFileName = time() . '.jpg';
        $profilePicturePath = 'public/users/' . $profilePictureFileName;

        $storageDiskMock->shouldReceive('exists')
            ->with($profilePicturePath)
            ->andReturn(true);

        $storageDiskMock->shouldReceive('delete')
            ->with($profilePicturePath)
            ->andReturn(true);

        $user->update(['picture' => $profilePicturePath]);

        $csrfToken = csrf_token();

        $response = $this->delete("/users/{$user->id}", ['_token' => $csrfToken]);

        $response->assertStatus(200);
        $response->assertSessionHas('success_message', 'Item deleted with success.');

        $this->assertDatabaseMissing('users', ['id' => $user->id]);

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
