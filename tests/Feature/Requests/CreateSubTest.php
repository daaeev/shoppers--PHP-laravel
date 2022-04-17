<?php

namespace Tests\Feature\Requests;

use App\Http\Requests\ajax\CreateSub;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class CreateSubTest extends TestCase
{
    use RefreshDatabase;

    protected string $route = '/check-create-sub-validation-route';

    public function setUp(): void
    {
        parent::setUp();

        Route::post($this->route, function (CreateSub $validate) {
            return true;
        });

        $this->user = User::factory()->createOne();
    }

    public function testIfNotAuth()
    {
        $response = $this->post($this->route)->assertUnauthorized();
    }

    /**
     * @dataProvider successData
     */
    public function testSuccessData($email)
    {
        $this->actingAs($this->user)
            ->post($this->route, ['email' => $email])
            ->assertOk();
    }

    /**
     * @dataProvider failedData
     */
    public function testFailedData($email)
    {
        $this->actingAs($this->user)
            ->post($this->route, ['email' => $email])
            ->assertForbidden();
    }

    public function successData()
    {
        return [
            ['email@gmail.com'],
            ['email@inbox.ua'],
            ['email@yandex.ru'],
        ];
    }

    public function failedData()
    {
        return [
            [123],
            ['some string'],
            [''],
        ];
    }
}
