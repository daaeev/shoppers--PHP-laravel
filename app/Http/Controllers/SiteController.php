<?php

namespace App\Http\Controllers;

class SiteController extends Controller
{
    /**
     * Рендер главное страницы
     *
     * @return mixed
     */
    public function index()
    {
        return view('index');
    }

    /**
     * Рендер страницы просмотра корзины
     *
     * @return mixed
     */
    public function cart()
    {
        return view('cart');
    }

    /**
     * Рендер страницы с формой для оформления покупки
     *
     * @return mixed
     */
    public function buy()
    {
        return view('buy');
    }

    /**
     * Рендер страницы связи
     *
     * @return mixed
     */
    public function contact()
    {
        return view('contact');
    }

    /**
     * Рендер страницы каталога товаров
     *
     * @return mixed
     */
    public function catalog()
    {
        return view('catalog');
    }

    /**
     * Рендер страницы о нас
     *
     * @return mixed
     */
    public function about()
    {
        return view('about');
    }

    /**
     * Рендер страницы просмотра товара
     *
     * @return mixed
     */
    public function single()
    {
        return view('single');
    }

    /**
     * Рендер страницы благодарности за покупку товара
     *
     * @return mixed
     */
    public function thanks()
    {
        return view('thanks');
    }
}
