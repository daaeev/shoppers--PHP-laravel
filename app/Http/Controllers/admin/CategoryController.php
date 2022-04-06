<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateCategory;
use App\Http\Requests\DeleteCategory;
use App\Models\Category;
use App\Models\Product;
use App\Services\Interfaces\CategoryRepositoryInterface;
use App\Services\traits\ReturnWithRedirectAndFlash;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    use ReturnWithRedirectAndFlash;

    /**
     * Создание категории
     *
     * @param Category $model
     * @param CreateCategory $validate
     * @return mixed
     */
    public function createCategory(Category $model, CreateCategory $validate)
    {
        $model->setRawAttributes($validate->validated());

        if (!$model->save()) {
            return $this->withRedirectAndFlash(
                'status_failed',
                'Category save failed',
                route('admin.categories'),
                $this->request
            );
        }

        return $this->withRedirectAndFlash(
            'status_success',
            'Category save success',
            route('admin.categories'),
            $this->request
        );
    }

    /**
     *
     * Удаление категории
     * @param CategoryRepositoryInterface $categoryRepository
     * @param DeleteCategory $validate
     * @return mixed
     */
    public function deleteCategory(
        CategoryRepositoryInterface $categoryRepository,
        DeleteCategory $validate
    )
    {
        $id = $validate->validated('id');
        $model = $categoryRepository->getFirstOrNull($id);

        if (!$model->delete()) {
            return $this->withRedirectAndFlash(
                'status_failed',
                'Category delete failed',
                route('admin.categories'),
                $this->request
            );
        }

        return $this->withRedirectAndFlash(
            'status_success',
            'Category delete success',
            route('admin.categories'),
            $this->request
        );
    }
}
