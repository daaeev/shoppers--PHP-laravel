<?php

namespace Tests\Feature\Requests;

use App\Http\Requests\DeleteProduct;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class DeleteProductTest extends TestCase
{
    use RefreshDatabase;

    protected string $route = '/check-delete-product-validation-route';

    public function setUp(): void
    {
        parent::setUp();

        Route::post($this->route, function (DeleteProduct $validation) {
            return true;
        });
    }

    public function testSuccessData()
    {
        $id = Product::factory()->createOne()->id;

        $response = $this->post($this->route, [
            'id' => $id,
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
