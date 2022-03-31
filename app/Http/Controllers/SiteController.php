<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\Interfaces\FilterProcessingInterface;
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
     * @param ProductRepositoryInterface $productRepository
     * @param FilterProcessingInterface $filterProcessing
     * @return mixed
     */
    public function catalog(
        ProductRepositoryInterface $productRepository,
        FilterProcessingInterface $filterProcessing
    )
    {
        $get_params = $this->request->query();
        $filters_data = $productRepository->getFiltersData();
        $pageSize = 15;
        $catalog = [];

        if ($filterProcessing->arrayHasFilters($this->request->query())) {
            $filters = $filterProcessing->getFiltersFromArray($this->request->query());
            $filters = $filterProcessing->processFiltersArray($filters);
            $catalog = $productRepository->getCatalogWithPagAndFilters($filters, $pageSize);
        } else {
            $catalog = $productRepository->getCatalogWithPag($pageSize);
        }

        return view('catalog', compact('catalog', 'get_params', 'filters_data'));
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
     * @param Product $product
     * @param ProductRepositoryInterface $productRepository
     * @return mixed
     */
    public function single(
        Product $product,
        ProductRepositoryInterface $productRepository
    )
    {
        $similar = $productRepository->getSimilarInSizeProducts($product);

        return view('single', compact('product', 'similar'));
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
