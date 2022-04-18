<?php

namespace Tests\Feature\Requests;

use App\Http\Requests\SetMessageStatus;
use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class SetMessageStatusTest extends TestCase
{
    use RefreshDatabase;

    protected string $route = '/create-coupon-validation-route';

    public function setUp(): void
    {
        parent::setUp();

        Route::post($this->route, function (SetMessageStatus $validate) {
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
        $mess = Message::factory()->createOne();

        $response = $this->actingAs($this->user_admin)
            ->post($this->route, ['id' => $mess->id])
            ->assertOk();

        $response->assertSessionDoesntHaveErrors();
    }

    /**
     * @dataProvider failedData
     */
    public function testFailedData($id)
    {
        $response = $this->actingAs($this->user_admin)
            ->post($this->route, ['id' => $id])
            ->assertRedirect(route('home'));

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
