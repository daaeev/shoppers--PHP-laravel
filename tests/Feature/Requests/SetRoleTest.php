<?php

namespace Tests\Feature\Requests;

use App\Http\Requests\UserSetRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class SetRoleTest extends TestCase
{
    use RefreshDatabase;

    protected string $route = '/check-set-role-validation-route';

    public function setUp(): void
    {
        parent::setUp();

        Route::post($this->route, function (UserSetRole $validation) {
            return true;
        });
    }

    /**
     * @dataProvider postFailedDataProvider
     */
    public function testPostDataValidationFailed($id, $role)
    {
        $response = $this->post($this->route, [
                'id' => $id,
                'role' => $role,
            ])
            ->assertRedirect(route('home'));

        $response->assertSessionHasErrors();
    }

    public function postFailedDataProvider()
    {
        $role_to_set = User::$status_banned;

        return [
            ['string', $role_to_set],
            [1, 'string'],
            [123, $role_to_set],
            [1, 123],
            [1.2, $role_to_set],
            [1, 1.2],
            [null, $role_to_set],
            [1, null],
        ];
    }

    public function testPostDataValidationSuccess()
    {
        $id = User::factory()->createOne()->id;
        $role = User::$status_banned;

        $response = $this->post($this->route, [
            'id' => $id,
            'role' => $role,
        ])->assertOk();

        $response->assertSessionHasNoErrors();
    }
}
