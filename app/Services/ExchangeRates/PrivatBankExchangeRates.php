<?php

namespace App\Services\ExchangeRates;

use App\Services\Interfaces;
use App\Services\Interfaces\ExchangeApiDataGetInterface;
use Exception;
use function config;

class PrivatBankExchangeRates extends Interfaces\ExchangeRatesProcess
{
    /**
     * @inheritDoc
     */
    public function process(): array
    {
        $exchange = [];

        $base_cur = config('exchange.base', 'UAH');

        // Установка курса базовой валюты к базовой валюте
        $exchange[$base_cur] = 1;

        $data = json_decode($this->exchangeApi->getAPIExchangeData(), true);

        foreach ($data as $exc) {

            // Если в массиве поддерживаемых валют нет валюты, полученной из АПИ,
            // перейти к следующей валюте
            if (!in_array($exc['ccy'], config('exchange.currencies', ['UAH']))) {
                continue;
            }

            // Проверка на равность базовой валюты в курсе к базовой валюте приложения
            if ($base_cur !== $exc['base_ccy'] ) {
                throw new Exception('Base currency must be ' . $base_cur . ', but ' . $exc['base_ccy'] . ' it is in ' . $exc['ccy'] . ' exchange rate');
            }

            $exchange[$exc['ccy']] = (float) $exc['sale'];
        }

        return $exchange;
    }
}
