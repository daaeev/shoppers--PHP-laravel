<?php

namespace App\Http\Controllers;

use App\Services\Interfaces\CategoryRepositoryInterface;
use App\Services\Interfaces\ColorRepositoryInterface;
use App\Services\Interfaces\ProductRepositoryInterface;
use App\Services\Interfaces\SizeRepositoryInterface;
use App\Services\Interfaces\UserRepositoryInterface;
use Illuminate\Http\Request;
use ViewComponents\ViewComponents\Input\InputSource;

class AdminController extends Controller
{
    /**
     * @param Request $request
     */
    public function __construct(protected Request $request)
    {
    }

    /**
     * Метод отвечает за рендер страницы 'Users' админ панели
     *
     * @param UserRepositoryInterface $userRepository
     * @return mixed
     */
    public function usersList(UserRepositoryInterface $userRepository)
    {
        $input = new InputSource($this->request->query());
        $grid = $userRepository->getAllUsingGrid($input);

        return view('admin.users', compact('grid'));
    }

    /**
     * Метод отвечает за рендер страницы 'Products' админ панели
     *
     * @param ProductRepositoryInterface $productRepository
     * @return mixed
     */
    public function productsList(ProductRepositoryInterface $productRepository)
    {
        $input = new InputSource($this->request->query());
        $grid = $productRepository->getAllUsingGrid($input);

        return view('admin.products', compact('grid'));
    }

    /**
     * Метод отвечает за рендер страницы 'Categories' админ панели
     *
     * @param CategoryRepositoryInterface $categoryRepository
     * @return mixed
     */
    public function categoriesList(CategoryRepositoryInterface $categoryRepository)
    {
        $input = new InputSource($this->request->query());
        $grid = $categoryRepository->getAllUsingGrid($input);

        return view('admin.categories', compact('grid'));
    }

    /**
     * Метод отвечает за рендер страницы 'Sizes' админ панели
     *
     * @param SizeRepositoryInterface $sizeRepository
     * @return mixed
     */
    public function sizesList(SizeRepositoryInterface $sizeRepository)
    {
        $input = new InputSource($this->request->query());
        $grid = $sizeRepository->getAllUsingGrid($input);

        return view('admin.sizes', compact('grid'));
    }

    /**
     * Метод отвечает за рендер страницы 'Colors' админ панели
     *
     * @param ColorRepositoryInterface $colorRepository
     * @return mixed
     */
    public function colorsList(ColorRepositoryInterface $colorRepository)
    {
        $input = new InputSource($this->request->query());
        $grid = $colorRepository->getAllUsingGrid($input);

        return view('admin.colors', compact('grid'));
    }
}
