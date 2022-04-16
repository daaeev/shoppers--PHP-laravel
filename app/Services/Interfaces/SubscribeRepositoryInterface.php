<?php

namespace App\Services\Interfaces;

interface SubscribeRepositoryInterface
{
    /**
     * Метод возвращает из электронных почт, которые подписаны на рассылку
     *
     * @return array нумеративный массив электронных почт
     */
    public function getEmails(): array;
}
