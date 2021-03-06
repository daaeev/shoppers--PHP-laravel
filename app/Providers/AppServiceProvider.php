<?php

namespace App\Providers;

use App\Services\ExchangeRates\PrivatBankExchangeApiDataGet;
use App\Services\ExchangeRates\PrivatBankExchangeRates;
use App\Services\FiltersProcessing;
use App\Services\ImageProfiler;
use App\Services\Interfaces\CategoryRepositoryInterface;
use App\Services\Interfaces\ColorRepositoryInterface;
use App\Services\Interfaces\CouponsRepositoryInterface;
use App\Services\Interfaces\ExchangeApiDataGetInterface;
use App\Services\Interfaces\ExchangeRatesProcess;
use App\Services\Interfaces\ExchangeRepositoryInterface;
use App\Services\Interfaces\FilterProcessingInterface;
use App\Services\Interfaces\ImageProfilerInterface;
use App\Services\Interfaces\MessageRepositoryInterface;
use App\Services\Interfaces\NewsRepositoryInterface;
use App\Services\Interfaces\ProductRepositoryInterface;
use App\Services\Interfaces\SizeRepositoryInterface;
use App\Services\Interfaces\SubscribeRepositoryInterface;
use App\Services\Interfaces\TeammatesRepositoryInterface;
use App\Services\Interfaces\UserRepositoryInterface;
use App\Services\Repositories\CategoryRepository;
use App\Services\Repositories\ColorRepository;
use App\Services\Repositories\CouponsRepository;
use App\Services\Repositories\ExchangeRepository;
use App\Services\Repositories\MessageRepository;
use App\Services\Repositories\NewsRepository;
use App\Services\Repositories\ProductRepository;
use App\Services\Repositories\SizeRepository;
use App\Services\Repositories\SubscribeRepository;
use App\Services\Repositories\TeammatesRepository;
use App\Services\Repositories\UserRepository;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public $bindings = [

        // Repositories
        UserRepositoryInterface::class => UserRepository::class,
        ProductRepositoryInterface::class => ProductRepository::class,
        CategoryRepositoryInterface::class => CategoryRepository::class,
        SizeRepositoryInterface::class => SizeRepository::class,
        ColorRepositoryInterface::class => ColorRepository::class,
        CouponsRepositoryInterface::class => CouponsRepository::class,
        TeammatesRepositoryInterface::class => TeammatesRepository::class,
        NewsRepositoryInterface::class => NewsRepository::class,
        SubscribeRepositoryInterface::class => SubscribeRepository::class,
        MessageRepositoryInterface::class => MessageRepository::class,
        ExchangeRepositoryInterface::class => ExchangeRepository::class,

        // Services
        ImageProfilerInterface::class => ImageProfiler::class,
        FilterProcessingInterface::class => FiltersProcessing::class,
        ExchangeRatesProcess::class => PrivatBankExchangeRates::class,
        ExchangeApiDataGetInterface::class => PrivatBankExchangeApiDataGet::class,
    ];

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(UserRepositoryInterface $userRepository)
    {
        Paginator::defaultView('layouts.pagination');

        // ?????????????????????? BLADE ????????????????

        Blade::if('admin', function () use ($userRepository) {
            return $userRepository->getAuthenticated()?->isAdmin();
        });

        Blade::if('usernotbanned', function () use ($userRepository) {
            return !($userRepository->getAuthenticated()?->isBanned());
        });
    }
}
