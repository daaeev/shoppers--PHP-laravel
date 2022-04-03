<?php

namespace Tests\Feature\Requests;

use App\Http\Requests\ajax\Cart;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    protected string $route = '/check-cart-validation-route';

    public function setUp(): void
    {
        parent::setUp();

        Route::post($this->route, function (Cart $validate) {
            return true;
        });
    }

    public function testSuccess()
    {
        $product = Product::factory()->createOne();

        $response = $this->post($this->route, [
            'product_id' => $product->id,
        ])->assertOk();

        $response->assertSessionHasNoErrors();
    }

    /**
     * @dataProvider failedData
     */
    public function testFailedData($product_id)
    {
        $this->post($this->route, [
                'product_id' => $product_id,
            ])->assertNotFound();
    }

    public function failedData()
    {
        return [
            [null],
            [1.2],
            ['string'],
            [-1],
            [123],
        ];
    }
}
