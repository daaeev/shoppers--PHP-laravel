<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\Interfaces\ExchangeRepositoryInterface;
use App\Services\Interfaces\FilterProcessingInterface;
use App\Services\Interfaces\ProductRepositoryInterface;
use App\Services\Interfaces\TeammatesRepositoryInterface;
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
     * @param ExchangeRepositoryInterface $exchangeRepository
     * @return mixed
     */
    public function cart(
        ProductRepositoryInterface $productRepository,
        UserRepositoryInterface $userRepository,
        ExchangeRepositoryInterface $exchangeRepository
    )
    {
        $exchange = $exchangeRepository->getExchangeInfo();
        $user = $userRepository->getAuthenticated();
        $products = new Collection();
        $cart_array = unserialize($this->request->cookie('cart'));

        if (is_array($cart_array)) {
            $products = $productRepository->getProductsByIds(array_keys($cart_array));
        }

        return view('cart', compact('products', 'cart_array', 'user', 'exchange'));
    }

    /**
     * Рендер страницы с формой для оформления покупки
     *
     * @return mixed
     */
    public function buy(
        UserRepositoryInterface $userRepository,
        ProductRepositoryInterface $productRepository,
        ExchangeRepositoryInterface $exchangeRepository
    )
    {
        $user = $userRepository->getAuthenticated();

        $cart_array = unserialize($this->request->cookie('cart'));
        $products = $productRepository->getProductsByIds(array_keys($cart_array));
        $exchange = $exchangeRepository->getExchangeInfo();

        return view('buy', compact('user', 'products', 'cart_array', 'exchange'));
    }

    /**
     * Рендер страницы связи
     *
     * @return mixed
     */
    public function contact()
    {
        $request = $this->request;
        return view('contact', compact('request'));
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
        // GET-параметры запроса
        $get_params = $this->request->query();

        // Массив коллекций из моделей, от которых зависит товар
        $filters_data = $productRepository->getFiltersData();

        $pageSize = 15;
        $catalog = new LengthAwarePaginator([], 0, $pageSize);

        // Если в запросе имеются GET-параметры с 'зарезервированными' именами
        if ($filterProcessing->arrayHasFilters($this->request->query())) {

            // Получить массив GET-параметров, которые относяться к фильтрам
            $filters = $filterProcessing->getFiltersFromArray($this->request->query());

            // Парс массива фонфигураций
            $filters = $filterProcessing->processFiltersArray($filters);

            // Поулчение товаров, используя фильтры
            $catalog = $productRepository->getCatalogWithPagAndFilters($filters, $pageSize);
        } else {
            $catalog = $productRepository->getCatalogWithPag($pageSize);
        }

        return view('catalog', compact('catalog', 'get_params', 'filters_data'));
    }

    /**
     * Рендер страницы 'О нас'
     *
     * @param TeammatesRepositoryInterface $teammatesRepository
     * @return mixed
     */
    public function about(TeammatesRepositoryInterface $teammatesRepository)
    {
        $teammates = $teammatesRepository->getAll();

        return view('about', compact('teammates'));
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
