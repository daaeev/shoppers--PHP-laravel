<?php

namespace App\Services\Interfaces;

use App\Services\Wrappers\FileGetContentsWrapper;

abstract class ExchangeApiDataGetInterface
{
    public function __construct(protected FileGetContentsWrapper $fileGetContentsWrapper)
    {
    }

    /**
     * Метод получает и возвращает данные курса валют,
     * полученные из АПИ
     *
     * @return mixed данные курса валют
     * @throws \Exception если данные из API не получены
     */
    abstract public function getAPIExchangeData(): mixed;
}
