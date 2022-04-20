<?php

namespace App\Services\Interfaces;

use App\Services\Interfaces\divided\GridInterface;

interface ExchangeRepositoryInterface extends GridInterface
{
    /**
     * Метод возвращает информацию о курсе валют из БД.
     * Ключами результирующего массива должны быть
     * названия столбцов (коды вылюты), а значения -
     * значение столбца (курс к базовой валюте)
     *
     * @return array ассоциативный массив с курсом валют
     * @throws \Exception если курс поддерживаемой валюты не найден
     */
    public function getExchangeInfo(): array;
}
