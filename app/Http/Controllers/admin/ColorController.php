<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateColor;
use App\Http\Requests\DeleteColor;
use App\Models\Color;
use App\Services\Interfaces\ColorRepositoryInterface;
use App\Services\traits\ReturnWithRedirectAndFlash;

class ColorController extends Controller
{
    use ReturnWithRedirectAndFlash;

    /**
     * Создание цвета
     *
     * @param Color $model
     * @param CreateColor $validate
     * @return mixed
     */
    public function createColor(Color $model, CreateColor $validate)
    {
        $name = $validate->validated('name');
        $hex = $validate->validated('hex');

        $model->name = $name;
        $model->hex = $hex;

        if (!$model->save()) {
            return $this->withRedirectAndFlash(
                'status_failed',
                'Category save failed',
                route('admin.colors'),
                $this->request
            );
        }

        return $this->withRedirectAndFlash(
            'status_success',
            'Category save success',
            route('admin.colors'),
            $this->request
        );
    }

    /**
     * Удаление цвета
     *
     * @param ColorRepositoryInterface $colorRepository
     * @param DeleteColor $validate
     * @return mixed
     */
    public function deleteColor(
        ColorRepositoryInterface $colorRepository,
        DeleteColor $validate
    )
    {
        $id = $validate->validated('id');
        $model = $colorRepository->getFirstOrNull($id);

        if (!$model->delete()) {
            return $this->withRedirectAndFlash(
                'status_failed',
                'Color delete failed',
                route('admin.colors'),
                $this->request
            );
        }

        return $this->withRedirectAndFlash(
            'status_success',
            'Color delete success',
            route('admin.colors'),
            $this->request
        );
    }
}
