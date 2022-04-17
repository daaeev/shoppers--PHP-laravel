<?php

namespace Tests\Feature\Requests;

use App\Http\Requests\EditTeammate;
use App\Models\Teammate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Tests\TestCase;

class EditTeammateTest extends TestCase
{
    use RefreshDatabase;

    protected string $route = '/check-edit-teammate-validation-route';

    public function setUp(): void
    {
        parent::setUp();

        Route::post($this->route, function (EditTeammate $validate) {
            return true;
        });

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
        $team = Teammate::factory()->createOne();

        $image = new UploadedFile(
            dirname(__DIR__) . '/test_files/image.png',
            'image.png',
            'image/*',
            null,
            true
        );

        $data = [
            'id' => $team->id,
            'full_name' => 'edited',
            'position' => 'edited',
            'description' => 'edited',
            'image' => $image,
        ];

        $response = $this->actingAs($this->user_admin)
            ->post($this->route, $data)
            ->assertOk();

        $response->assertSessionHasNoErrors();

        $data = [
            'id' => $team->id,
            'full_name' => 'edited',
            'position' => 'edited',
            'description' => 'edited',
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
                ->post($this->route, $req_data)
                ->assertRedirect(route('home'));

            $response->assertSessionHasErrors();
        }
    }

    protected function failedData()
    {
        $team = Teammate::factory()->createOne();

        return [
            [
                'full_name' => 'edited',
                'position' => 'edited',
                'description' => 'edited',
            ],
            [
                'id' => 123,
                'full_name' => 'edited',
                'position' => 'edited',
                'description' => 'edited',
            ],
            [
                'id' => $team->id,
                'full_name' => Str::random(31),
                'position' => 'edited',
                'description' => 'edited',
            ],
            [
                'id' => $team->id,
                'position' => 'edited',
                'description' => 'edited',
            ],
            [
                'id' => $team->id,
                'full_name' => 'edited',
                'position' => Str::random(31),
                'description' => 'edited',
            ],
            [
                'id' => $team->id,
                'full_name' => 'edited',
                'description' => 'edited',
            ],
            [
                'id' => $team->id,
                'full_name' => 'edited',
                'position' => 'edited',
                'description' => Str::random(256),
            ],
            [
                'id' => $team->id,
                'full_name' => 'edited',
                'position' => 'edited',
            ],
            [
                'id' => $team->id,
                'full_name' => 'edited',
                'position' => 'edited',
                'description' => 'edited',
                'image' => 'not file',
            ],
        ];
    }
}
