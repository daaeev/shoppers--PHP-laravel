<?php

namespace App\Services\Interfaces;

interface ExchangeApiDataGetInterface
{
    /**
     * Метод получает и возвращает данные курса валют,
     * полученные из АПИ
     *
     * @return mixed данные курса валют
     */
    public function getAPIExchangeData(): mixed;
}
