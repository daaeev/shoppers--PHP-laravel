<?php

namespace Tests\Feature\Requests;

use App\Http\Requests\DeleteTeammate;
use App\Models\Teammate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class DeleteTeammateTest extends TestCase
{
    use RefreshDatabase;

    protected string $route = '/check-delete-teammate-validation-route';

    public function setUp(): void
    {
        parent::setUp();

        Route::post($this->route, function (DeleteTeammate $validate) {
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
        $team = Teammate::factory()->createOne();

        $data = [
            'id' => $team->id
        ];

        $response = $this->actingAs($this->user_admin)
            ->post($this->route, $data)
            ->assertOk();

        $response->assertSessionHasNoErrors();
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
            [null],
            ['string'],
            [123],
        ];
    }
}
