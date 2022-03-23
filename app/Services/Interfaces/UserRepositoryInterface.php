<?php

namespace App\Services\Interfaces;

interface UserRepositoryInterface extends GridInterface, GetterInterface
{
    /**
     * Метод возвращает экземпляр аутентифицированного пользователя
     * @return mixed
     */
    public function getAuthenticated(): mixed;
}
