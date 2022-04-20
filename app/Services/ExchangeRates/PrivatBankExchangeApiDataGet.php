<?php

namespace App\Services\ExchangeRates;

use App\Services\Interfaces;

class PrivatBankExchangeApiDataGet implements Interfaces\ExchangeApiDataGetInterface
{
    /**
     * @inheritDoc
     */
    public function getAPIExchangeData(): mixed
    {
        return file_get_contents('https://api.privatbank.ua/p24api/pubinfo?json&exchange&coursid=5');
    }
}
