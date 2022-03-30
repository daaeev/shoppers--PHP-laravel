<?php

namespace Tests\Feature\Requests;

use App\Http\Requests\CreateCategory;
use App\Models\Category;
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
    }

    public function testSuccessData()
    {
        $name = Str::random();

        $response = $this->post($this->route, [
            'name' => $name,
        ])->assertOk();

        $response->assertSessionHasNoErrors();
    }

    public function testFailedData()
    {
        $category = Category::factory()->createOne();

        $data = [
            null,
            $category->name,
            Str::random(256),
        ];

        foreach ($data as $name_el) {
            $response = $this->post($this->route, [
                'name' => $name_el,
            ])
                ->assertRedirect(route('home'));

            $response->assertSessionHasErrors();
        }
    }
}