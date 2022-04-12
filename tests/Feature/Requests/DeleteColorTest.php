<?php

namespace Tests\Feature\Requests;

use App\Http\Requests\DeleteColor;
use App\Models\Color;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class DeleteColorTest extends TestCase
{
    use RefreshDatabase;

    protected string $route = '/check-delete-color-validation-route';

    public function setUp(): void
    {
        parent::setUp();

        Route::post($this->route, function (DeleteColor $validate) {
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
        $category = Color::factory()->createOne();

        $response = $this->actingAs($this->user_admin)->post($this->route, [
            'id' => $category->id,
        ])->assertOk();

        $response->assertSessionHasNoErrors();
    }

    /**
     * @dataProvider failedData
     */
    public function testFailedData($id)
    {
        $response = $this->actingAs($this->user_admin)->post($this->route, [
            'id' => $id,
        ])->assertRedirect(route('home'));

        $response->assertSessionHasErrors();
    }

    public function failedData()
    {
        return [
            [1.2],
            [-1],
            [123],
            ['string'],
            [null],
        ];
    }
}
