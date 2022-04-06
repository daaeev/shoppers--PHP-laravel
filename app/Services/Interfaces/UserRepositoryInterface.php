<?php

namespace App\Services\Interfaces;

use App\Services\Interfaces\divided\GetFirstInterface;
use App\Services\Interfaces\divided\GridInterface;

interface UserRepositoryInterface extends
    GridInterface,
    GetFirstInterface
{
    /**
     * Метод возвращает экземпляр аутентифицированного пользователя
     * @return mixed
     */
    public function getAuthenticated(): mixed;
}
