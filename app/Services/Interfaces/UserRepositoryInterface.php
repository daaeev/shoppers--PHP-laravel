<?php

namespace App\Services\Interfaces;

use App\Services\Interfaces\divided\GetterInterface;
use App\Services\Interfaces\divided\GridInterface;

interface UserRepositoryInterface extends GridInterface, GetterInterface
{
    /**
     * Метод возвращает экземпляр аутентифицированного пользователя
     * @return mixed
     */
    public function getAuthenticated(): mixed;
}
