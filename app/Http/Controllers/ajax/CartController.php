<?php

namespace App\Http\Controllers\ajax;

use App\Http\Controllers\Controller;
use App\Http\Requests\ajax\Cart;
use App\Services\Interfaces\ProductRepositoryInterface;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use function cookie;

class CartController extends Controller
{
    /**
     * Добавление продукта в корзину
     *
     * @param Cart $validate
     * @return Response
     */
    public function addToCart(Cart $validate)
    {
        // Массив идентификаторов продуктов
        $cart_array = [];

        // массив с идентификаторами продуктов или false
        $cart_cookie = unserialize($this->request->cookie('cart'));

        // Если имеется кука и она является массивом - присвоить результат $cart_array
        if (is_array($cart_cookie)) {
            $cart_array = $cart_cookie;
        }

        $product_id = $validate->validated('product_id');

        // Если в массиве товаров нет переданного идентификатора товара
        if (!array_key_exists($product_id, $cart_array)) {
            $cart_array[$product_id] = ['count' => 1];
        }

        // Создание кук
        $new_cart_cookie = cookie()->forever('cart', serialize($cart_array));
        $cart_count_cookie = cookie()->forever('cart_count', count($cart_array));

        return (new Response())->withCookie($new_cart_cookie)->withCookie($cart_count_cookie);
    }

    /**
     * Удаление продукта из корзины
     *
     * @param Cart $validate
     * @return Response
     */
    public function removeFromCart(Cart $validate)
    {
        $product_id = $validate->validated('product_id');

        // Массив товаров 'в корзине'
        $cart_array = unserialize($this->request->cookie('cart'));

        // Если кука не массив или пустой массив
        if (!is_array($cart_array) || (is_array($cart_array) && empty($cart_array))) {
            throw new HttpException(404);
        }

        // Если в массиве имеется продукт с переданным идентификатором -
        // то удалить его, создать новую куку и возвратить идентификатор удаленного продукта,
        // иначе сгенерировать исключение
        if (array_key_exists($product_id, $cart_array)) {
            unset($cart_array[$product_id]);

            $new_product_cart_cookie = cookie()->forever('cart', serialize($cart_array));
            $cart_count_cookie = cookie()->forever('cart_count', count($cart_array));

            return (new Response($product_id))->withCookie($new_product_cart_cookie)->withCookie($cart_count_cookie);
        } else {
            throw new HttpException(404);
        }
    }

    /**
     * Очищение корзины пользователя
     *
     * @return Response
     */
    public function updateCart()
    {
        return (new Response())
            ->withCookie(cookie()->forget('cart'))
            ->withCookie(cookie()->forget('cart_count'));
    }

    /**
     * Увеличение количества товара в корзине
     *
     * @param Cart $validate
     * @param ProductRepositoryInterface $productRepository
     * @return Response
     */
    public function productCountPlus(
        ProductRepositoryInterface $productRepository,
        Cart $validate
    )
    {

        $product_id = $validate->validated('product_id');
        $product = $productRepository->getFirstOrNull($product_id);

        // Массив из товаров 'в корзине'
        $cart_array = unserialize($this->request->cookie('cart'));

        // Если кука не массив или пустой массив
        if (!is_array($cart_array) || (is_array($cart_array) && empty($cart_array))) {
            throw new HttpException(404);
        }

        // Если в массиве товаров нет переданного идентификатора товара
        if (!array_key_exists($product_id, $cart_array)) {
            throw new HttpException(404);
        }

        $product_count = &$cart_array[$product_id]['count'];

        // Если количество товара в корзине больше количества товара на складе
        if ($product_count > $product->count) {
            $product_count = $product->count;
            return (new Response($product_count, 404))->withCookie(cookie()->forever('cart', serialize($cart_array)));
        }

        // Если количество товара в корзине равно количеству товара на складе
        if ($product_count == $product->count) {
            return new Response($product_count);
        }

        $product_count++;

        return (new Response($product_count))->withCookie(cookie()->forever('cart', serialize($cart_array)));
    }

    /**
     * Уменьшение количества товара в корзине
     * @param Cart $validate
     * @return Response
     */
    public function productCountMinus(Cart $validate)
    {
        $product_id = $validate->validated('product_id');

        // Массив из товаров 'в корзине'
        $cart_array = unserialize($this->request->cookie('cart'));

        // Если кука не массив или пустой массив
        if (!is_array($cart_array) || (is_array($cart_array) && empty($cart_array))) {
            throw new HttpException(404);
        }

        // Если в массиве товаров нет переданного идентификатора товара
        if (!array_key_exists($product_id, $cart_array)) {
            throw new HttpException(404);
        }

        $product_count = &$cart_array[$product_id]['count'];

        // Количество товара не может быть меньше 1
        if ($product_count == 1) {
            return (new Response(1));
        }

        $product_count--;

        return (new Response($product_count))->withCookie(cookie()->forever('cart', serialize($cart_array)));
    }
}
