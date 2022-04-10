<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\Interfaces\FilterProcessingInterface;
use App\Services\Interfaces\ProductRepositoryInterface;
use App\Services\Interfaces\UserRepositoryInterface;
use App\Services\Repositories\UserRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class SiteController extends Controller
{
    /**
     * Рендер главное страницы
     *
     * @param ProductRepositoryInterface $productRepository
     * @return mixed
     */
    public function index(ProductRepositoryInterface $productRepository)
    {
        $recommendations = $productRepository->getRandom();

        return view('index', compact('recommendations'));
    }

    /**
     * Рендер страницы просмотра корзины
     *
     * @param ProductRepositoryInterface $productRepository
     * @param UserRepositoryInterface $userRepository
     * @return mixed
     */
    public function cart(
        ProductRepositoryInterface $productRepository,
        UserRepositoryInterface $userRepository
    )
    {
        $user = $userRepository->getAuthenticated();
        $products = new Collection();
        $cart_array = unserialize($this->request->cookie('cart'));

        if (is_array($cart_array)) {
            $products = $productRepository->getProductsByIds(array_keys($cart_array));
        }

        return view('cart', compact('products', 'cart_array', 'user'));
    }

    /**
     * Рендер страницы с формой для оформления покупки
     *
     * @return mixed
     */
    public function buy(
        UserRepositoryInterface $userRepository,
        ProductRepositoryInterface $productRepository
    )
    {
        $user = $userRepository->getAuthenticated();
        $cart_array = unserialize($this->request->cookie('cart'));
        $products = $productRepository->getProductsByIds(array_keys($cart_array));

        return view('buy', compact('user', 'products', 'cart_array'));
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
        $catalog = new LengthAwarePaginator([], 0, $pageSize);

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
    public function payThanks()
    {
        return view('paythanks');
    }

    /**
     * Рендер страницы ошибки оплаты товара
     *
     * @return mixed
     */
    public function payError()
    {
        return view('payerror');
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
