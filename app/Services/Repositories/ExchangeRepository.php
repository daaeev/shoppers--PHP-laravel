<?php

namespace App\Services\Repositories;

use App\Models\Exchange;
use ViewComponents\Eloquent\EloquentDataProvider;
use ViewComponents\Grids\Component\Column;
use ViewComponents\Grids\Grid;
use ViewComponents\ViewComponents\Customization\CssFrameworks\BootstrapStyling;
use ViewComponents\ViewComponents\Input\InputSource;

class ExchangeRepository implements \App\Services\Interfaces\ExchangeRepositoryInterface
{
    /**
     * @inheritDoc
     */
    public function getExchangeInfo(): array
    {
        $data = Exchange::firstOrFail()->attributesToArray();
        unset($data['id']);

        foreach (config('exchange.currencies', ['UAH']) as $cur) {
            if (!array_key_exists($cur, $data)) {
                throw new \Exception($cur . ' exchange rate not found');
            }
        }

        return $data;
    }

    /**
     * @inheritDoc
     */
    public function getAllUsingGrid(InputSource $input, int $pageSize = 15): Grid
    {
        $provider = new EloquentDataProvider(Exchange::query());

        $columns = [];

        foreach (config('exchange.currencies', ['UAH']) as $cur) {
            $columns[] = new Column($cur);
        }

        $grid = new Grid($provider, $columns);

        $styles = new BootstrapStyling();
        $styles->apply($grid);

        return $grid;
    }
}
