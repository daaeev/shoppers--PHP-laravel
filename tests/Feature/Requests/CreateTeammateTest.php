<?php

namespace Tests\Feature\Requests;

use App\Http\Requests\CreateTeammate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Tests\TestCase;

class CreateTeammateTest extends TestCase
{
    use RefreshDatabase;

    protected string $route = '/check-create-teammate-validation-route';

    public function setUp(): void
    {
        parent::setUp();

        Route::post($this->route, function (CreateTeammate $validate) {
            return true;
        });

        $this->image = new UploadedFile(
            dirname(__DIR__) . '/test_files/image.png',
            'image.png',
            'image/*',
            null,
            true
        );
        $this->user_admin = User::factory()->createOne(['status' => User::$status_admin]);
    }

    public function testIfNotAuth()
    {
        $response = $this->post($this->route)->assertForbidden();
    }

    public function testIfUserNotAdmin()
    {
        $user = User::factory()->createOne();

        $response = $this->actingAs($user)->post($this->route)->assertForbidden();
    }

    public function testSuccessData()
    {
        $data = [
            'full_name' => 'Name',
            'position' => 'programmer',
            'description' => 'Lorem',
            'image' => $this->image,
        ];

        $response = $this->actingAs($this->user_admin)
            ->post($this->route, $data)
            ->assertOk();

        $response->assertSessionHasNoErrors();
    }

    public function testFailedData()
    {
        $data = $this->failedData();

        foreach ($data as $req_data) {
            $response = $this->actingAs($this->user_admin)
                ->post($this->route, $data)
                ->assertRedirect(route('home'));

            $response->assertSessionHasErrors();
        }
    }

    protected function failedData()
    {
        return [
            [
                'position' => 'programmer',
                'description' => 'Lorem',
                'image' => $this->image,
            ],
            [
                'full_name' => Str::random(31),
                'position' => 'programmer',
                'description' => 'Lorem',
                'image' => $this->image,
            ],
            [
                'full_name' => 'Name',
                'description' => 'Lorem',
                'image' => $this->image,
            ],
            [
                'full_name' => 'Name',
                'position' => Str::random(31),
                'description' => 'Lorem',
                'image' => $this->image,
            ],
            [
                'full_name' => 'Name',
                'position' => 'programmer',
                'image' => $this->image,
            ],
            [
                'full_name' => 'Name',
                'position' => 'programmer',
                'description' => Str::random(256),
                'image' => $this->image,
            ],
            [
                'full_name' => 'Name',
                'position' => 'programmer',
                'description' => 'Lorem',
            ],
            [
                'full_name' => 'Name',
                'position' => 'programmer',
                'description' => 'Lorem',
                'image' => 'not file'
            ],
        ];
    }
}
