<?php

namespace App\Http\Controllers\ajax;

use App\Http\Controllers\Controller;
use App\Http\Requests\ajax\CreateSub;
use App\Models\Subscribe;
use App\Services\Interfaces\SubscribeRepositoryInterface;
use App\Services\Interfaces\UserRepositoryInterface;
use App\Services\traits\ReturnWithRedirectAndFlash;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class SubscribeController extends Controller
{
    use ReturnWithRedirectAndFlash;

    /**
     * Создание подписки на рассылку
     *
     * @param Subscribe $model
     * @param UserRepositoryInterface $userRepository
     * @param CreateSub $validate
     * @return Response|void
     */
    public function createSub(
        Subscribe $model,
        UserRepositoryInterface $userRepository,
        CreateSub $validate
    )
    {
        $data = $validate->validated();

        $user = $userRepository->getAuthenticated();
        $data['user_id'] = $user->id;

        // Если пользователь уже подписан на новости
        if ($user->news_subscribe) {
            return new Response('You are already subscribed to our news');
        }

        $model->setRawAttributes($data);

        if (!$model->save()) {
            throw new HttpException(500, 'An error occurred on the server');
        }

        return new Response('You have subscribed to our news');
    }

    /**
     * Отписка пользователя от рассылки
     *
     * @param UserRepositoryInterface $userRepository
     * @param Subscribe $model
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function unsubUser(
        UserRepositoryInterface $userRepository,
        Subscribe $model
    )
    {
        $user = $userRepository->getAuthenticated();
        $sub = $model->where('user_id', $user->id)->first();

        if (!$sub) {
            return $this->withRedirectAndFlash(
                'status_warning',
                'You are not subscribed to the newsletter',
                route('profile'),
                $this->request
            );
        }

        if (!$sub->delete()) {
            return $this->withRedirectAndFlash(
                'status_failed',
                'An error occurred on the server',
                route('profile'),
                $this->request
            );
        }

        return $this->withRedirectAndFlash(
            'status_success',
            'You have unsubscribed from the newsletter',
            route('profile'),
            $this->request
        );
    }
}
