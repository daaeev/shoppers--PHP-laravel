<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SetMessageStatus;
use App\Models\Message;
use App\Services\Interfaces\MessageRepositoryInterface;
use App\Services\traits\ReturnWithRedirectAndFlash;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    use ReturnWithRedirectAndFlash;

    /**
     * Установить сообщению статус 'Отвечено'
     *
     * @param MessageRepositoryInterface $messageRepository
     * @param SetMessageStatus $validate
     * @return mixed
     */
    public function setAnsweredStatus(
        MessageRepositoryInterface $messageRepository,
        SetMessageStatus $validate
    )
    {
        $model = $messageRepository->getFirstOrNull($validate->validated('id'));

        $model->setAttribute('answered', true);

        if (!$model->save()) {
            return $this->withRedirectAndFlash(
                'status_failed',
                'Message set status failed',
                route('admin.messages'),
                $this->request
            );
        }

        return $this->withRedirectAndFlash(
            'status_success',
            'Message set status success',
            route('admin.messages'),
            $this->request
        );
    }

    /**
     * Удаление всех сообщений со статусом 'Отмечено'
     *
     * @param Message $model
     * @return void
     */
    public function deleteAnswered(Message $model)
    {
        if (!$model->where('answered', true)->delete()) {
            return $this->withRedirectAndFlash(
                'status_failed',
                'Messages delete failed',
                route('admin.messages'),
                $this->request
            );
        }

        return $this->withRedirectAndFlash(
            'status_success',
            'Messages delete success',
            route('admin.messages'),
            $this->request
        );
    }
}
