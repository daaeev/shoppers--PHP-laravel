<?php

namespace App\Services\ExchangeRates;

use App\Services\Interfaces;

class PrivatBankExchangeApiDataGet extends Interfaces\ExchangeApiDataGetInterface
{
    /**
     * @inheritDoc
     */
    public function getAPIExchangeData(): mixed
    {
        $data = $this->fileGetContentsWrapper
            ->file_get_contents('https://api.privatbank.ua/p24api/pubinfo?json&exchange&coursid=5');

        if (!$data) {
            throw new \Exception('API data get failed');
        }

        return $data;
    }
}
