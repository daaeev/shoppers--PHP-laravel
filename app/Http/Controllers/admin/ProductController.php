<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateProduct;
use App\Http\Requests\DeleteProduct;
use App\Models\Product;
use App\Services\Interfaces\ImageProfilerInterface;
use App\Services\Interfaces\ProductRepositoryInterface;
use App\Services\traits\ReturnWithRedirectAndFlash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    use ReturnWithRedirectAndFlash;

    /**
     * Создание продукта
     *
     * @param ImageProfilerInterface $imgProfiller
     * @param CreateProduct $validate
     * @return mixed
     */
    public function createProduct(
        ImageProfilerInterface $imgProfiller,
        Product $model,
        CreateProduct $validate
    )
    {
        $data = $validate->validated();

        // Сохранение файлов изображений
        $main_image_name = null;
        $preview_image_name = null;

        $main_image_file = $this->request->file('main_image');
        $preview_image_file = $this->request->file('preview_image');

        if ($preview_image_file) {
            $images = $imgProfiller->saveTwoImages($main_image_file, $preview_image_file);

            if (!$images) {
                return $this->withRedirectAndFlash(
                    'status_failed',
                    'Images store failed',
                    route('admin.products.create.form'),
                    $this->request
                );
            }

            list($main_image_name, $preview_image_name) = $images;
        } else {
            $main_image_name = $imgProfiller->saveImage($main_image_file);

            if (!$main_image_name) {
                return $this->withRedirectAndFlash(
                    'status_failed',
                    'Main image store failed',
                    route('admin.products.create.form'),
                    $this->request
                );
            }
        }

        // Присвоение данных модели
        $data['main_image'] = $main_image_name;
        $data['preview_image'] = $preview_image_name;

        foreach ($data as $attr => $value) {
            $model->setAttribute($attr, $value);
        }

        // Сохранение данных модели
        if (!$model->save()) {
            if ($preview_image_name) {
                $imgProfiller->deleteImage($main_image_name);
                $imgProfiller->deleteImage($preview_image_name);
            } else {
                $imgProfiller->deleteImage($main_image_name);
            }

            return $this->withRedirectAndFlash(
                'status_failed',
                'Product save failed',
                route('admin.products.create.form'),
                $this->request
            );
        }

        return $this->withRedirectAndFlash(
            'status_success',
            'Product create success',
            route('admin.products'),
            $this->request
        );
    }

    public function deleteProduct(
        ProductRepositoryInterface $productRepository,
        ImageProfilerInterface     $imgProfiller,
        DeleteProduct              $validate
    )
    {
        $model = $productRepository->getFirstOrNull($validate->validated('id'));
        $main_image_name = $model->main_image;
        $preview_image_name = $model->preview_image;

        if (!$model->delete()) {
            return $this->withRedirectAndFlash(
                'status_failed',
                'Product delete failed',
                route('admin.products'),
                $this->request
            );
        }

        if (!$imgProfiller->deleteImage($main_image_name)) {
            return $this->withRedirectAndFlash(
                'status_warning',
                'Main image delete failed, but model is deleted',
                route('admin.products'),
                $this->request
            );
        }

        if ($preview_image_name && !$imgProfiller->deleteImage($preview_image_name)) {
            return $this->withRedirectAndFlash(
                'status_warning',
                'Preview image delete failed, but model is deleted',
                route('admin.products'),
                $this->request
            );
        }

        return $this->withRedirectAndFlash(
            'status_success',
            'Product delete success',
            route('admin.products'),
            $this->request
        );
    }
}
