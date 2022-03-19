<?php

namespace App\Services\traits;

/**
 * Трейт с методами для проверки прав пользователя
 */
trait HasRoles
{
    /**
     * @var int числовое значение статуса админа
     */
    public static $status_admin = 5;

    /**
     * @var int числовое значение статуса забаненого пользователя
     */
    public static $status_banned = 3;

    /**
     * @var int числовое значение статуса обычного пользователя
     */
    public static $status_user = 0;

    /**
     * Проверка на статус администратор
     *
     * @return bool результат проверки
     */
    public function isAdmin(): bool
    {
        if ($this->getStatus() == $this::$status_admin) {
            return true;
        }

        return false;
    }

    /**
     * Проверка на статус забаненного пользователя
     *
     * @return bool результат проверки
     */
    public function isBanned(): bool
    {
        if ($this->getStatus() == $this::$status_banned) {
            return true;
        }

        return false;
    }

    /**
     * Метод возвращает статус пользователя в виде числа
     *
     * @return int
     */
    protected abstract function getStatus(): int;
}
