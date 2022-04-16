<?php

namespace App\Http\Controllers\admin;

use App\Events\NewsCreate;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateNews;
use App\Http\Requests\SendNews;
use App\Models\News;
use App\Services\Interfaces\NewsRepositoryInterface;
use App\Services\traits\ReturnWithRedirectAndFlash;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    use ReturnWithRedirectAndFlash;

    /**
     * Создание новости
     *
     * @param News $model
     * @param CreateNews $validate
     * @return mixed
     */
    public function createNews(
        News $model,
        CreateNews $validate
    )
    {
        $model->setRawAttributes($validate->validated());

        if (!$model->save())
        {
            return $this->withRedirectAndFlash(
                'status_failed',
                'News save failed',
                route('admin.news'),
                $this->request
            );
        }

        return $this->withRedirectAndFlash(
            'status_success',
            'News save success',
            route('admin.news'),
            $this->request
        );
    }

    public function sendNews(
        NewsRepositoryInterface $newsRepository,
        SendNews $validate
    )
    {
        $model = $newsRepository->getFirstOrNull($validate->validated('id'));
        NewsCreate::dispatch($model);

        $model->setAttribute('sent', true);

        if (!$model->save()) {
            return $this->withRedirectAndFlash(
                'status_warning',
                'News sent, but edit sent status failed',
                route('admin.news'),
                $this->request
            );
        }

        return $this->withRedirectAndFlash(
            'status_success',
            'News send success',
            route('admin.news'),
            $this->request
        );
    }
}
