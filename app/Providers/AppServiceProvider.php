<?php

namespace App\Providers;

use App\Models\Product;
use App\Services\FiltersProcessing;
use App\Services\ImageProfiler;
use App\Services\Interfaces\CategoryRepositoryInterface;
use App\Services\Interfaces\ColorRepositoryInterface;
use App\Services\Interfaces\CouponsRepositoryInterface;
use App\Services\Interfaces\FilterProcessingInterface;
use App\Services\Interfaces\ImageProfilerInterface;
use App\Services\Interfaces\ProductRepositoryInterface;
use App\Services\Interfaces\SizeRepositoryInterface;
use App\Services\Interfaces\UserRepositoryInterface;
use App\Services\Repositories\CategoryRepository;
use App\Services\Repositories\ColorRepository;
use App\Services\Repositories\CouponsRepository;
use App\Services\Repositories\ProductRepository;
use App\Services\Repositories\SizeRepository;
use App\Services\Repositories\UserRepository;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Validator;
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

        // Services
        ImageProfilerInterface::class => ImageProfiler::class,
        FilterProcessingInterface::class => FiltersProcessing::class,
    ];

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(UserRepositoryInterface $userRepository)
    {
        Paginator::defaultView('layouts.pagination');

        // РЕГИСТРАЦИЯ BLADE ДИРЕКТИВ

        Blade::if('admin', function () use ($userRepository) {
            return $userRepository->getAuthenticated()?->isAdmin();
        });

        Blade::if('notbanned', function () use ($userRepository) {
            return !($userRepository->getAuthenticated()?->isBanned());
        });
    }
}
