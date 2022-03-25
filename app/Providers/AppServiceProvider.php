<?php

namespace App\Providers;

use App\Services\ImageProfiler;
use App\Services\Interfaces\CategoryRepositoryInterface;
use App\Services\Interfaces\ColorRepositoryInterface;
use App\Services\Interfaces\ImageProfilerInterface;
use App\Services\Interfaces\ProductRepositoryInterface;
use App\Services\Interfaces\SizeRepositoryInterface;
use App\Services\Interfaces\UserRepositoryInterface;
use App\Services\Repositories\CategoryRepository;
use App\Services\Repositories\ColorRepository;
use App\Services\Repositories\ProductRepository;
use App\Services\Repositories\SizeRepository;
use App\Services\Repositories\UserRepository;
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

        // Services
        ImageProfilerInterface::class => ImageProfiler::class,
    ];

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(UserRepositoryInterface $userRepository)
    {
        // РЕГИСТРАЦИЯ BLADE ДИРЕКТИВ

        Blade::if('admin', function () use ($userRepository) {
            return $userRepository->getAuthenticated()?->isAdmin();
        });
    }
}
