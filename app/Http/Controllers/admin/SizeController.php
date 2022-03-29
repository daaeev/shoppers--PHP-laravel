<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateSize;
use App\Http\Requests\DeleteSize;
use App\Models\Size;
use App\Services\Interfaces\SizeRepositoryInterface;
use App\Services\traits\ReturnWithRedirectAndFlash;

class SizeController extends Controller
{
    use ReturnWithRedirectAndFlash;

    public function createSize(Size $model, CreateSize $validate)
    {
        $name = $validate->validated('name');

        $model->name = $name;

        if (!$model->save()) {
            return $this->withRedirectAndFlash(
                'status_failed',
                'Size save failed',
                route('admin.sizes'),
                $this->request
            );
        }

        return $this->withRedirectAndFlash(
            'status_success',
            'Size save success',
            route('admin.sizes'),
            $this->request
        );
    }

    public function deleteSize(
        SizeRepositoryInterface $sizeRepository,
        DeleteSize $validate
    )
    {
        $id = $validate->validated('id');
        $model = $sizeRepository->getFirstOrNull($id);

        if (!$model->delete()) {
            return $this->withRedirectAndFlash(
                'status_failed',
                'Size delete failed',
                route('admin.sizes'),
                $this->request
            );
        }

        return $this->withRedirectAndFlash(
            'status_success',
            'Size delete success',
            route('admin.sizes'),
            $this->request
        );
    }
}
