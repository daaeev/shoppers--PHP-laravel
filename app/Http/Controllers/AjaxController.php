<?php

namespace App\Http\Controllers;

use App\Http\Requests\ajax\Cart;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AjaxController extends Controller
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

        // Если имеется кука - присвоить результат $cart_array
        if (is_array($cart_cookie)) {
            $cart_array = $cart_cookie;
        }

        // Занесение переданного идентификатора товара в массив
        $product_id = $validate->validated('product_id');

        // Если в массиве товаров нет переданного идентификатора товара
        if (!in_array($product_id, $cart_array)) {
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

        // Массив товаров в корзине
        $cart_array = unserialize($this->request->cookie('cart'));

        // Если кука не массив или пустой массив
        if (!is_array($cart_array) || (is_array($cart_array) && empty($cart_array))) {
            throw new HttpException(404);
        }

        // Если в массиве имеется продукт с переданным идентификатором -
        // то удалить его, создать новую куку и возвратить идентификатор удаленного продукта,
        // иначе сгенерировать исключение
        if (isset($cart_array[$product_id])) {
            unset($cart_array[$product_id]);

            $new_product_cart_cookie = cookie()->forever('cart', serialize($cart_array));
            $cart_count_cookie = cookie()->forever('cart_count', count($cart_array));

            return (new Response($product_id))->withCookie($new_product_cart_cookie)->withCookie($cart_count_cookie);
        } else {
            throw new HttpException(404);
        }
    }
}
