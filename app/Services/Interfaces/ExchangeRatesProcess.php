<?php

namespace App\Services\Interfaces;

abstract class ExchangeRatesProcess
{
    /**
     * @param ExchangeApiDataGetInterface $exchangeApi
     */
    public function __construct(protected ExchangeApiDataGetInterface $exchangeApi)
    {
    }

    /**
     * Метод обрабатывает данные курса валют, полученные из АПИ (защищенное свойство $exchangeApi),
     * и возвращает массив, ключи которого - коды валюты (UAH, USD),
     * а значения - курс валюты к базовой валюте.
     *
     * @return array массив с данными курса валют
     * @throws \Exception если базовая валюта курса не равна базовой валюте сайта
     */
    abstract public function process(): array;
}
