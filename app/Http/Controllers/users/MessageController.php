<?php

namespace App\Http\Controllers\users;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateMessage;
use App\Models\Message;
use App\Services\Interfaces\UserRepositoryInterface;
use App\Services\traits\ReturnWithRedirectAndFlash;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    use ReturnWithRedirectAndFlash;

    /**
     * Создание сообщения
     *
     * @param Message $model
     * @param UserRepositoryInterface $userRepository
     * @param CreateMessage $validate
     * @return mixed
     */
    public function createMessage(
        Message $model,
        UserRepositoryInterface $userRepository,
        CreateMessage $validate
    )
    {
        $data = $validate->validated();
        $data['user_id'] = $userRepository->getAuthenticated()->id;

        $model->setRawAttributes($data);

        if (!$model->save()) {
            return $this->withRedirectAndFlash(
                'status_failed',
                'Oops, server error (500)',
                route('contact', $model->attributesToArray()),
                $this->request
            );
        }

        return $this->withRedirectAndFlash(
            'status_success',
            'Your message has been received, we will contact you shortly',
            route('contact'),
            $this->request
        );
    }
}
