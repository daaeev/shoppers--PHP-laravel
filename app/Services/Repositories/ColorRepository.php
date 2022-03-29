<?php

namespace App\Services\Repositories;

use App\Models\Color;
use Illuminate\Database\Eloquent\Collection;
use ViewComponents\Eloquent\EloquentDataProvider;
use ViewComponents\Grids\Component\Column;
use ViewComponents\Grids\Grid;
use ViewComponents\ViewComponents\Component\Control\FilterControl;
use ViewComponents\ViewComponents\Component\Control\PaginationControl;
use ViewComponents\ViewComponents\Customization\CssFrameworks\BootstrapStyling;
use ViewComponents\ViewComponents\Data\Operation\FilterOperation;
use ViewComponents\ViewComponents\Input\InputSource;

class ColorRepository implements \App\Services\Interfaces\ColorRepositoryInterface
{
    /**
     * @inheritDoc
     */
    public function getAll(): Collection
    {
        return Color::all();
    }

    /**
     * @inheritDoc
     */
    public function getForeignColumnName(): string
    {
        return 'color_id';
    }

    /**
     * @inheritDoc
     */
    public function getAllUsingGrid(InputSource $input, int $pageSize = 15): Grid
    {
        $provider = new EloquentDataProvider(Color::query());

        $grid = new Grid($provider, [
            new Column('id'),
            new Column('name'),
            new PaginationControl($input->option('page', 1), $pageSize),
            new FilterControl('id', FilterOperation::OPERATOR_LIKE, $input->option('filt_id')),
            new FilterControl('name', FilterOperation::OPERATOR_LIKE, $input->option('filt_name')),
        ]);

        $styles = new BootstrapStyling();
        $styles->apply($grid);

        return $grid;
    }

    /**
     * @inheritDoc
     */
    public function getFirstOrNull(int $id): Color|null
    {
        return Color::where('id', $id)->firstOr(function () {
            return null;
        });
    }
}
