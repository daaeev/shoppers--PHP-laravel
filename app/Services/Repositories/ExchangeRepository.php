<?php

namespace App\Services\Repositories;

use App\Models\Exchange;
use Exception;
use ViewComponents\Eloquent\EloquentDataProvider;
use ViewComponents\Grids\Component\Column;
use ViewComponents\Grids\Grid;
use ViewComponents\ViewComponents\Component\Control\PaginationControl;
use ViewComponents\ViewComponents\Customization\CssFrameworks\BootstrapStyling;
use ViewComponents\ViewComponents\Input\InputSource;

class ExchangeRepository implements \App\Services\Interfaces\ExchangeRepositoryInterface
{
    /**
     * @inheritDoc
     */
    public function getExchangeInfo(): array
    {
        $exchange = array_column(
            Exchange::get()->toArray(),
            'exchange',
            'currency_code'
        );

        foreach (config('exchange.currencies', [config('exchange.base')]) as $curr_code) {
            if (!array_key_exists($curr_code, $exchange)) {
                throw new Exception('Supported currency is not in the exchange rate', 404);
            }
        }

        return $exchange;
    }

    /**
     * @inheritDoc
     */
    public function getAllUsingGrid(InputSource $input, int $pageSize = 15): Grid
    {
        $provider = new EloquentDataProvider(Exchange::query());

        $grid = new Grid($provider, [
            new Column('currency_code'),
            new Column('exchange'),
            new PaginationControl($input->option('page', 1), $pageSize),
        ]);

        $styles = new BootstrapStyling();
        $styles->apply($grid);

        return $grid;
    }
}
