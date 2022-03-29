<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\Interfaces\ProductRepositoryInterface;
use App\Services\Repositories\UserRepository;

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
    public function catalog(ProductRepositoryInterface $productRepository)
    {
        $catalog = $productRepository->getCatalogWithPag();

        return view('catalog', compact('catalog'));
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

    /**
     * Метод отвечает за рендер страницы профиля
     *
     * @return mixed
     */
    public function profile(UserRepository $userRepository)
    {
        $user = $userRepository->getAuthenticated();

        return view('profile', compact('user'));
    }
}
