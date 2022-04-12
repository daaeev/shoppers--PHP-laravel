<?php

namespace Tests\Feature\Requests;

use App\Http\Requests\CreateSize;
use App\Models\Size;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Tests\TestCase;

class CreateSizeTest extends TestCase
{
    use RefreshDatabase;

    protected string $route = '/check-create-size-validation-route';

    public function setUp(): void
    {
        parent::setUp();

        Route::post($this->route, function (CreateSize $validate) {
            return true;
        });

        $this->user_admin = User::factory()->createOne(['status' => User::$status_admin]);
    }

    public function testIfNotAuth()
    {
        $response = $this->post($this->route)->assertForbidden();

        $response->assertSessionHasNoErrors();
    }

    public function testIfUserNotAdmin()
    {
        $user = User::factory()->createOne();

        $response = $this->actingAs($user)->post($this->route)->assertForbidden();

        $response->assertSessionHasNoErrors();
    }

    public function testSuccessData()
    {
        $name = Str::random();

        $response = $this->actingAs($this->user_admin)->post($this->route, [
            'name' => $name,
        ])->assertOk();

        $response->assertSessionHasNoErrors();
    }

    public function testFailedData()
    {
        $data = $this->failedData();

        foreach ($data as $name_el) {
            $response = $this->actingAs($this->user_admin)->post($this->route, [
                'name' => $name_el,
            ])
                ->assertRedirect(route('home'));

            $response->assertSessionHasErrors();
        }
    }

    protected function failedData()
    {
        $size = Size::factory()->createOne();

        return [
            null,
            $size->name,
            Str::random(256),
        ];
    }
}
