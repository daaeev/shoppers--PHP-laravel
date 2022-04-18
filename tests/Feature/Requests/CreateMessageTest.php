<?php

namespace Tests\Feature\Requests;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Tests\TestCase;
use App\Http\Requests\CreateMessage;

class CreateMessageTest extends TestCase
{
    use RefreshDatabase;

    protected string $route = '/create-message-validation-route';

    public function setUp(): void
    {
        parent::setUp();

        Route::post($this->route, function (CreateMessage $validate) {
            return true;
        });

        $this->user = User::factory()->createOne();
    }

    public function testIfNotAuth()
    {
        $response = $this->post($this->route)->assertForbidden();
    }

    /**
     * @dataProvider successData
     */
    public function testSuccessData($data)
    {
        $response = $this->actingAs($this->user)
            ->post($this->route, $data)
            ->assertOk();

        $response->assertSessionHasNoErrors();
    }

    /**
     * @dataProvider failedData
     */
    public function testFailedData($data)
    {
        $response = $this->actingAs($this->user)
            ->post($this->route, $data)
            ->assertRedirect(route('home'));

        $response->assertSessionHasErrors();
    }

    public function successData()
    {
        return [
            [[
                'first_name' => 'Name',
                'last_name' => 'Name',
                'email' => 'test@lrvl.app',
                'title' => 'title',
                'content' => 'content',
            ]],
            [[
                'first_name' => 'Name',
                'last_name' => 'Name',
                'email' => 'test@lrvl.app',
                'content' => 'content',
            ]],
        ];
    }

    public function failedData()
    {
        return [
            [[
                'first_name' => Str::random(31),
                'last_name' => 'Name',
                'email' => 'test@lrvl.app',
                'title' => 'title',
                'content' => 'content',
            ]],
            [[
                'last_name' => 'Name',
                'email' => 'test@lrvl.app',
                'title' => 'title',
                'content' => 'content',
            ]],
            [[
                'first_name' => 'Name',
                'last_name' => Str::random(31),
                'email' => 'test@lrvl.app',
                'title' => 'title',
                'content' => 'content',
            ]],
            [[
                'first_name' => 'Name',
                'email' => 'test@lrvl.app',
                'title' => 'title',
                'content' => 'content',
            ]],
            [[
                'first_name' => 'Name',
                'last_name' => 'Name',
                'email' => 'not email',
                'title' => 'title',
                'content' => 'content',
            ]],
            [[
                'first_name' => 'Name',
                'last_name' => 'Name',
                'title' => 'title',
                'content' => 'content',
            ]],
            [[
                'first_name' => 'Name',
                'last_name' => 'Name',
                'email' => 'test@lrvl.app',
                'title' => 123,
                'content' => 'content',
            ]],
            [[
                'first_name' => 'Name',
                'last_name' => 'Name',
                'email' => 'test@lrvl.app',
                'title' => 'title',
            ]],
            [[
                'first_name' => 'Name',
                'last_name' => 'Name',
                'email' => 'test@lrvl.app',
                'title' => 'title',
                'content' => 123,
            ]],
        ];
    }
}
