<?php

namespace App\Http\Controllers;

use App\Services\Interfaces\CategoryRepositoryInterface;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\View;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @param Request $request
     * @param CategoryRepositoryInterface $categoryRepository
     * @return void
     */
    public function __construct(
        protected Request $request,
        CategoryRepositoryInterface $categoryRepository
    )
    {
        $categories = $categoryRepository->getAll();
        View::share('categories', $categories);
    }
}
