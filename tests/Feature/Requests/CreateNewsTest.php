<?php

namespace Tests\Feature\Requests;

use App\Http\Requests\CreateNews;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class CreateNewsTest extends TestCase
{
    use RefreshDatabase;

    protected string $route = '/check-create-news-validation-route';

    public function setUp(): void
    {
        parent::setUp();

        Route::post($this->route, function (CreateNews $validate) {
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
        $data = [
            'title' => 'Some title',
            'content' => 'Some content'
        ];

        $response = $this->actingAs($this->user_admin)
            ->post($this->route, $data)
            ->assertOk();

        $response->assertSessionDoesntHaveErrors();
    }

    /**
     * @dataProvider failedData
     */
    public function testFailedData($title, $content)
    {
        $data = [
            'title' => $title,
            'content' => $content
        ];

        $response = $this->actingAs($this->user_admin)
            ->post($this->route, $data)
            ->assertRedirect(route('home'));

        $response->assertSessionHasErrors();
    }

    public function failedData()
    {
        return [
            ['', 'Content'],
            ['Title', ''],
            [123, 'Content'],
            ['Title', 123],
        ];
    }
}
