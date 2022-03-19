<?php

namespace App\Services\traits;

use Illuminate\Http\Request;

trait ReturnWithRedirectAndFlash
{
    /**
     * Метод отвечает за установку флеш-сессии и выполнение редиректа
     *
     * @param string $flash_name имя флеш-сессии
     * @param string $flash_message значение флеш-сессии
     * @param string $redirect_route маршрут, на который произойдет редирект
     * @param Request $request
     * @return mixed
     */
    protected function withRedirectAndFlash(
        string $flash_name,
        string $flash_message,
        string $redirect_route,
        Request $request
    ) {
        $request->session()->flash($flash_name, $flash_message);

        return redirect($redirect_route);
    }
}
