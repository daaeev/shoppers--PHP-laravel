<?php

namespace Tests\Feature\Requests;

use App\Http\Requests\SendNews;
use App\Models\News;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class SendNewsTest extends TestCase
{
    use RefreshDatabase;

    protected string $route = '/check-send-news-validation-route';

    public function setUp(): void
    {
        parent::setUp();

        Route::post($this->route, function (SendNews $validate) {
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
        $news = News::factory()->createOne();

        $data = ['id' => $news->id];

        $response = $this->actingAs($this->user_admin)
            ->post($this->route, $data)
            ->assertOk();

        $response->assertSessionHasNoErrors();
    }

    /**
     * @dataProvider failedData
     */
    public function testfailedData($id)
    {
        $data = ['id' => $id];

        $response = $this->actingAs($this->user_admin)
            ->post($this->route, $data)
            ->assertRedirect(route('home'));

        $response->assertSessionHasErrors();
    }

    public function failedData()
    {
        return [
            [123],
            ['string'],
            [null]
        ];
    }
}
