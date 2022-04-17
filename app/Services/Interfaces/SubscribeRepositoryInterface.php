<?php

namespace App\Services\Interfaces;

use Illuminate\Database\Eloquent\Collection;

interface SubscribeRepositoryInterface
{
    /**
     * Метод возвращает из электронных почт, которые подписаны на рассылку
     *
     * @return Collection коллекция из моделей со свойством 'email'
     */
    public function getEmails(): Collection;
}
