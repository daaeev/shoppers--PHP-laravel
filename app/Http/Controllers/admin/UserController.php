<?php

namespace App\Http\Controllers\admin;

use \App\Http\Controllers\Controller;
use App\Http\Requests\UserSetRole;
use App\Services\Interfaces\UserRepositoryInterface;
use App\Services\traits\ReturnWithRedirectAndFlash;
use Illuminate\Http\Request;

class UserController extends Controller
{
    use ReturnWithRedirectAndFlash;

    /**
     * @param Request $request
     */
    public function __construct(protected Request $request)
    {
    }

    /**
     * Метод устанавливет статус определенному пользователю
     *
     * @param UserRepositoryInterface $userRepository
     * @param UserSetRole $validation
     * @return \Illuminate\Http\RedirectResponse
     */
    public function setRole(
        UserRepositoryInterface $userRepository,
        UserSetRole $validation
    )
    {
        $user_id = $this->request->input('id');
        $role = $this->request->input('role');

        // Сохранение данных пользователя в БД
        $user = $userRepository->getFistOrNull($user_id);
        $user->status = $role;
        if (!$user->save()) {
            return $this->withRedirectAndFlash(
                'status_failed',
                'Save failed',
                route('admin.users'),
                $this->request
            );
        }

        return $this->withRedirectAndFlash(
            'status_success',
            'Role is set',
            route('admin.users'),
            $this->request
        );
    }
}
