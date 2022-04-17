<?php

namespace Tests\Feature\Requests;

use App\Http\Requests\CreateCategory;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Tests\TestCase;

class CreateCategoryTest extends TestCase
{
    use RefreshDatabase;

    protected string $route = '/check-create-category-validation-route';

    public function setUp(): void
    {
        parent::setUp();

        Route::post($this->route, function (CreateCategory $validate) {
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
            ])->assertRedirect(route('home'));

            $response->assertSessionHasErrors();
        }
    }

    protected function failedData()
    {
        $category = Category::factory()->createOne();

        return [
            null,
            $category->name,
            Str::random(256),
        ];
    }
}
