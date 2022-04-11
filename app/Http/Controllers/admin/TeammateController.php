<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateCategory;
use App\Http\Requests\CreateTeammate;
use App\Http\Requests\DeleteCategory;
use App\Http\Requests\DeleteTeammate;
use App\Http\Requests\EditTeammate;
use App\Models\Category;
use App\Models\Product;
use App\Models\Teammate;
use App\Services\Interfaces\CategoryRepositoryInterface;
use App\Services\Interfaces\ImageProfilerInterface;
use App\Services\Interfaces\TeammatesRepositoryInterface;
use App\Services\traits\ReturnWithRedirectAndFlash;
use Illuminate\Http\Request;

class TeammateController extends Controller
{
    use ReturnWithRedirectAndFlash;

    /**
     * Создание работника
     *
     * @param Teammate $model
     * @param ImageProfilerInterface $imageProfiler
     * @param CreateTeammate $validate
     * @return mixed
     */
    public function createTeammate(
        Teammate $model,
        ImageProfilerInterface $imageProfiler,
        CreateTeammate $validate
    )
    {
        $imageProfiler->disk('public')->directory('teammates_images');

        $data = $validate->validated();
        $data['image'] = $imageProfiler->saveImage($data['image']);

        if (!$data['image']) {
            return $this->withRedirectAndFlash(
                'status_failed',
                'Image save failed',
                route('admin.team.create.form'),
                $this->request
            );
        }

        $model->setRawAttributes($data);

        if (!$model->save()) {
            return $this->withRedirectAndFlash(
                'status_failed',
                'Teammate save failed',
                route('admin.team.create.form'),
                $this->request
            );
        }

        return $this->withRedirectAndFlash(
            'status_success',
            'Teammate save success',
            route('admin.team'),
            $this->request
        );
    }

    /**
     * Удаление работника
     *
     * @param TeammatesRepositoryInterface $teammatesRepository
     * @param ImageProfilerInterface $imageProfiler
     * @param DeleteTeammate $validate
     * @return mixed
     */
    public function deleteTeammate(
        TeammatesRepositoryInterface $teammatesRepository,
        ImageProfilerInterface $imageProfiler,
        DeleteTeammate $validate
    )
    {
        $id = $validate->validated('id');
        $model = $teammatesRepository->getFirstOrNull($id);
        $image_name = $model->image;

        if (!$model->delete()) {
            return $this->withRedirectAndFlash(
                'status_failed',
                'Teammate delete failed',
                route('admin.team'),
                $this->request
            );
        }

        $imageProfiler->disk('public')->directory('teammates_images');

        if (!$imageProfiler->deleteImage($image_name)) {
            return $this->withRedirectAndFlash(
                'status_warning',
                'Image delete failed (' . $image_name . '), but model delete success',
                route('admin.team'),
                $this->request
            );
        }

        return $this->withRedirectAndFlash(
            'status_success',
            'Teammate delete success',
            route('admin.team'),
            $this->request
        );
    }

    /**
     * Редактирование данных работника
     *
     * @param TeammatesRepositoryInterface $teammatesRepository
     * @param ImageProfilerInterface $imageProfiler
     * @param EditTeammate $validate
     * @return mixed
     */
    public function editTeammate(
        TeammatesRepositoryInterface $teammatesRepository,
        ImageProfilerInterface $imageProfiler,
        EditTeammate $validate
    )
    {
        $data = $validate->validated();
        $model = $teammatesRepository->getFirstOrNull($data['id']);

        $imageProfiler->disk('public')->directory('teammates_images');

        // Если переданно новое изображение - создать новое и удалить старое.
        // При ошибке создания/удаления выполнить редирект
        if (isset($data['image'])) {
            $new_image = $imageProfiler->saveImage($data['image']);

            if (!$new_image) {
                return $this->withRedirectAndFlash(
                    'status_failed',
                    'New image save failed',
                    route('admin.team.edit.form', ['id' => $model->id]),
                    $this->request
                );
            }

            if (!$imageProfiler->deleteImage($model->image)) {
                return $this->withRedirectAndFlash(
                    'status_failed',
                    'Old image delete failed',
                    route('admin.team.edit.form', ['id' => $model->id]),
                    $this->request
                );
            }

            $data['image'] = $new_image;
        }

        $model->setRawAttributes($data);

        if (!$model->save()) {
            return $this->withRedirectAndFlash(
                'status_failed',
                'Teammate save failed',
                route('admin.team.edit.form', ['id' => $model->id]),
                $this->request
            );
        }

        return $this->withRedirectAndFlash(
            'status_success',
            'Teammate edited success',
            route('admin.team'),
            $this->request
        );
    }
}
