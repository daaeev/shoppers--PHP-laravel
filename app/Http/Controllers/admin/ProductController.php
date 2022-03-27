<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateProduct;
use App\Http\Requests\DeleteProduct;
use App\Http\Requests\EditProduct;
use App\Models\Product;
use App\Services\Interfaces\ImageProfilerInterface;
use App\Services\Interfaces\ProductRepositoryInterface;
use App\Services\traits\ReturnWithRedirectAndFlash;
use Illuminate\Database\Eloquent\Model;

class ProductController extends Controller
{
    use ReturnWithRedirectAndFlash;

    /**
     * Создание продукта
     *
     * @param ImageProfilerInterface $imgProfiller
     * @param Product $model
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

        $main_image_file = $validate['main_image'];
        $preview_image_file = $validate['preview_image'];

        // Если переданно два изображения, использовать метод ImageProfilerInterface::saveTwoImages(),
        // иначе - ImageProfilerInterface::saveImage()
        if ($preview_image_file) {
            $images = $imgProfiller->saveTwoImages($main_image_file, $preview_image_file);

            if (!$images) {
                return $this->withRedirectAndFlash(
                    'status_failed',
                    'Images store failed',
                    route('admin.product.create.form'),
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
                    route('admin.product.create.form'),
                    $this->request
                );
            }
        }

        // Присвоение данных модели
        $data['main_image'] = $main_image_name;
        $data['preview_image'] = $preview_image_name;
        $this->setModelAttributes($model, $data);

        // Сохранение данных модели
        if (!$model->save()) {
            $delete_fails = ' ';
            if ($preview_image_name) {
                if (!$imgProfiller->deleteImage($main_image_name)) {
                    $delete_fails .= '| New main image delete failed (' . $main_image_name . ')';
                }

                if (!$imgProfiller->deleteImage($preview_image_name)) {
                    $delete_fails .= '| New preview image delete failed (' . $preview_image_name . ')';
                }
            } else {
                if (!$imgProfiller->deleteImage($main_image_name)) {
                    $delete_fails .= '| New main image delete failed (' . $main_image_name . ')';
                }
            }

            return $this->withRedirectAndFlash(
                'status_failed',
                'Product save failed' . $delete_fails,
                route('admin.product.create.form'),
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

    /**
     * Удаление продукта
     *
     * @param ProductRepositoryInterface $productRepository
     * @param ImageProfilerInterface $imgProfiller
     * @param DeleteProduct $validate
     * @return mixed
     */
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

        $delete_fails = ' ';

        if (!$imgProfiller->deleteImage($main_image_name)) {
            $delete_fails .= '| Main image delete failed (' . $main_image_name . ')';
        }

        if ($preview_image_name && !$imgProfiller->deleteImage($preview_image_name)) {
            $delete_fails .= '| Preview image delete failed (' . $preview_image_name . ')';
        }

        return $this->withRedirectAndFlash(
            empty(trim($delete_fails)) ? 'status_success' : 'status_warning',
            'Product delete success' . $delete_fails,
            route('admin.products'),
            $this->request
        );
    }

    /**
     * Редактирование продукта
     *
     * @param ProductRepositoryInterface $productRepository
     * @param ImageProfilerInterface $imgProfiler
     * @param EditProduct $validate
     * @return mixed
     */
    public function editProduct(
        ProductRepositoryInterface $productRepository,
        ImageProfilerInterface $imgProfiler,
        EditProduct $validate
    )
    {
        $model = $productRepository->getFirstOrNull($validate->validated('id'));
        $data = $validate->validated();

        $old_main_image = $model->main_image;
        $old_preview_image = $model->preview_image;

        // Создание новых изображений, если переданны, и занесение новых имен в $data
        if (isset($data['main_image'])) {
            $new_main_image = $imgProfiler->saveImage($data['main_image']);

            if (!$new_main_image) {
                return $this->withRedirectAndFlash(
                    'status_error',
                    'New main image save failed',
                    route('admin.products.edit.form', ['id' => $model->id]),
                    $this->request
                );
            }

            $data['main_image'] = $new_main_image;
        }

        if (isset($data['preview_image'])) {
            $new_preview_image = $imgProfiler->saveImage($data['preview_image']);

            // Если превью-изображение не созданно - удалить главное(если переданно)
            if (!$new_preview_image) {
                if ($data['main_image'] && !$imgProfiler->deleteImage($data['main_image'])) {
                    return $this->withRedirectAndFlash(
                        'status_error',
                        'New preview image save failed | New main image delete failed (' . $data['main_image'] . ')',
                        route('admin.products.edit.form', ['id' => $model->id]),
                        $this->request
                    );
                }

                return $this->withRedirectAndFlash(
                    'status_error',
                    'New preview image save failed',
                    route('admin.products.edit.form', ['id' => $model->id]),
                    $this->request
                );
            }

            $data['preview_image'] = $new_preview_image;
        }

        // Присвоение новый свойств модели
        $this->setModelAttributes($model, $data);

        // Если сохранение модели провалено - удалить новосозданные, если имеются
        if (!$model->save()) {
            $delete_fails = ' ';

            // Если главное изображение было заменено - удалить новосозданное
            if (isset($data['main_image']) && !$imgProfiler->deleteImage($data['main_image'])) {
                $delete_fails .= '| New main image delete failed (' . $data['main_image'] . ')';
            }

            // Если превью-изображение было заменено - удалить новосозданное
            if (isset($data['preview_image']) && !$imgProfiler->deleteImage($data['preview_image'])) {
                $delete_fails .= '| New preview image delete failed (' . $data['preview_image'] . ')';
            }

            return $this->withRedirectAndFlash(
                'status_error',
                'Product save failed' . $delete_fails,
                route('admin.products.edit.form', ['id' => $model->id]),
                $this->request
            );
        }

        // Если сохранение модели прошло удачно - удалить старые замененные изображения
        if (isset($data['main_image']) || isset($data['preview_image'])) {
            $delete_fails = ' ';

            // Если главное изображение заменено - удалить старое
            if (isset($data['main_image']) && !$imgProfiler->deleteImage($old_main_image)) {
                $delete_fails .= '| Old main image delete failed (' . $old_main_image . ')';
            }

            // Если превью-изображение заменено - удалить старое
            if (isset($data['preview_image']) && $old_preview_image && !$imgProfiler->deleteImage($old_preview_image)) {
                $delete_fails .= '| Old main image delete failed (' . $old_preview_image . ')';
            }

            if (trim($delete_fails)) {
                return $this->withRedirectAndFlash(
                    'status_warning',
                    'Product edited success' . $delete_fails,
                    route('admin.products'),
                    $this->request
                );
            }
        }

        return $this->withRedirectAndFlash(
            'status_success',
            'Product edited success',
            route('admin.products'),
            $this->request
        );
    }

    /**
     * Метод устанавливает свойства из ассоциативного массива $data
     * модели $model
     *
     * @param Model $model
     * @param array $data
     * @return void
     */
    protected function setModelAttributes(Model &$model, array $data) {
        foreach ($data as $attr => $value) {
            unset($data['id']);
            $model->setAttribute($attr, $value);
        }
    }
}
