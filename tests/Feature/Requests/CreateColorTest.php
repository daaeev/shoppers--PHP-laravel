<?php

namespace Tests\Feature\Requests;

use App\Http\Requests\CreateColor;
use App\Models\Color;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Tests\TestCase;

class CreateColorTest extends TestCase
{
    use RefreshDatabase;

    protected string $route = '/check-create-color-validation-route';

    public function setUp(): void
    {
        parent::setUp();

        Route::post($this->route, function (CreateColor $validate) {
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
        $hex = '#000000';

        $response = $this->actingAs($this->user_admin)->post($this->route, [
            'name' => $name,
            'hex' => $hex,
        ])->assertOk();

        $response->assertSessionHasNoErrors();
    }

    public function testFailedData()
    {
        $data = $this->failedData();

        foreach ($data as list($name, $hex)) {
            $response = $this->actingAs($this->user_admin)->post($this->route, [
                'name' => $name,
                'hex' => $hex,
            ])->assertRedirect(route('home'));

            $response->assertSessionHasErrors();
        }
    }

    protected function failedData()
    {
        $col = Color::factory()->createOne();

        return [
            [null, $col->hex],
            [$col->name, $col->hex],
            [Str::random(256), $col->hex],
            [$col->name, null],
            [$col->name, $col->hex],
            [$col->name, Str::random(11)],
        ];
    }
}
