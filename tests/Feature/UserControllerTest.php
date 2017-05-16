<?php
namespace Tests\Feature;
use App\User;
use Faker\Factory;
use Illuminate\Http\UploadedFile;
use Storage;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
/**
 * Class UserControllerTest.
 *
 * @package Tests\Feature
 */
class UserControllerTest extends TestCase
{
    use DatabaseMigrations;
    /**
     * test users are created ok.
     *
     * @return void
     */
    public function testUsersAreCreatedOk()
    {
        // Prepare
        Storage::fake('general');

        $faker = Factory::create();
        $user = [
            'name' => $name = $faker->name,
            'email' => $email = $faker->unique()->safeEmail ,
            'password' => $password = bcrypt('secret'),
            'file' => UploadedFile::fake()->image('olaqase.png')
        ];
        //Login as authorized user and Execute
        $authorizedUser = factory(User::class)->create();
        $response = $this->actingAs($authorizedUser,'api')->json('POST', 'api/v1/user', $user);
        // Assert
        $response
            ->assertStatus(200)
            ->assertJson([
                'created' => true,
            ]);
        $this->assertDatabaseHas('users', [
            'name' => $name,
            'email' => $email ,
            'password' => $password ,
            'file' => 'olaqase.png',
        ]);

        Storage::disk('general')->assertExists('olaqase.png');
    }
}