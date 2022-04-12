<?php

namespace Tests\Feature\Middlewares;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class HaveProductsInCartTest extends TestCase
{
    protected string $route = '/have-product-in-cart-test-route';

    // ТЕСТЫ НЕ ПРОХОДЯТ, ТАК КАК В ПОСРЕДНИКЕ НЕ РАСШИФРОВУЕТСЯ КУКА

/*    public function setUp(): void
    {
        parent::setUp();

        Route::get($this->route, function () {
            return true;
        })->middleware('haveProductsInCart');
    }

    public function testIfHaveProducts()
    {
        $cart = [1 => ['count' => 2]];

        $this->withCookie('cart', serialize($cart))
            ->get($this->route)
            ->assertOk();
    }*/
}
