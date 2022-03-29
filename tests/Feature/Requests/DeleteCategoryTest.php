<?php

namespace Tests\Feature\Requests;

use App\Http\Requests\DeleteCategory;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class DeleteCategoryTest extends TestCase
{
    use RefreshDatabase;

    protected string $route = '/check-delete-category-validation-route';

    public function setUp(): void
    {
        parent::setUp();

        Route::post($this->route, function (DeleteCategory $validate) {
            return true;
        });
    }

    public function testSuccessData()
    {
        $category = Category::factory()->createOne();

        $response = $this->post($this->route, [
            'id' => $category->id,
        ])->assertOk();

        $response->assertSessionHasNoErrors();
    }

    /**
     * @dataProvider failedData
     */
    public function testFailedData($id)
    {
        $response = $this->post($this->route, [
            'id' => $id,
        ])
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
