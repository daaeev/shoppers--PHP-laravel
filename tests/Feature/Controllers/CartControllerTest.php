<?php

namespace Tests\Feature\Controllers;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartControllerTest extends TestCase
{
    use RefreshDatabase;

    // ТЕСТЫ НЕ РАБОТАЮТ! В КОНТРОЛЛЕРЕ НЕТ ДОСТУПА К КУКАМ, ПЕРЕДАННЫХ В МЕТОД $this->withCookie()

/*    public function testAddToCart()
    {
        $product = Product::factory()->createOne();

        $response = $this->get(route(
            'ajax.cart.add',
                ['product_id' => $product->id]
            ))->assertOk();

        $response->assertCookie('cart', serialize([$product->id => ['count' => 1]]));
        $response->assertCookie('cart_count', 1, false);




        // Добавление уже имеющегося товара

        $response = $this->withCookie('cart', serialize([$product->id => ['count' => 1]]))
            ->get(route(
            'ajax.cart.add',
            ['product_id' => $product->id]
        ))->assertOk();

        $response->assertCookie('cart', serialize([$product->id => ['count' => 1]]));
        $response->assertCookie('cart_count', 1, false);

        // Добавление второго товара

        $product2 = Product::factory()->createOne();

        $response = $this->withCookie('cart', serialize([$product->id => ['count' => 1]]))
            ->get(route(
                'ajax.cart.add',
                ['product_id' => $product2->id]
            ))->assertOk();

        $response->assertCookie('cart', serialize([$product->id => ['count' => 1], $product2->id => ['count' => 1]]));
        $response->assertCookie('cart_count', 2, false);
    }

    public function testDeleteFromCart()
    {
        $product = Product::factory()->createOne();

        // Попытка удаления несуществующего товара корзины

        $response = $this->withCookie('cart', serialize([]))
            ->get(route(
                'ajax.cart.remove',
                ['product_id' => $product->id]
            ))->assertNotFound();

        $response->assertCookieMissing('cart');
        $response->assertCookieMissing('cart_count');

        // Удаление товара из корзины с одним товаром

        $response = $this->withCookie('cart', serialize([$product->id => ['count' => 1]]))
            ->get(route(
            'ajax.cart.remove',
            ['product_id' => $product->id]
        ))->assertOk();

        $response->assertCookie('cart', serialize([]));
        $response->assertCookie('cart_count', 0, false);

        // Удаление товара из корзины с несколькими товарами

        $product2 = Product::factory()->createOne();

        $response = $this->withCookie('cart', serialize([$product->id => ['count' => 1], $product2->id => ['count' => 1]]))
            ->get(route(
                'ajax.cart.remove',
                ['product_id' => $product2->id]
            ))->assertOk();

        $response->assertCookie('cart', serialize([$product->id => ['count' => 1]]));
        $response->assertCookie('cart_count', 1, false);
    }*/
}
