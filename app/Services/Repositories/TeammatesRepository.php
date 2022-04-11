<?php

namespace App\Services\Repositories;

use App\Models\Teammate;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use ViewComponents\Eloquent\EloquentDataProvider;
use ViewComponents\Grids\Component\Column;
use ViewComponents\Grids\Grid;
use ViewComponents\ViewComponents\Component\Control\FilterControl;
use ViewComponents\ViewComponents\Component\Control\PaginationControl;
use ViewComponents\ViewComponents\Customization\CssFrameworks\BootstrapStyling;
use ViewComponents\ViewComponents\Data\Operation\FilterOperation;
use ViewComponents\ViewComponents\Input\InputSource;

class TeammatesRepository implements \App\Services\Interfaces\TeammatesRepositoryInterface
{
    /**
     * @inheritDoc
     */
    public function getAll(): Collection
    {
        return Teammate::all();
    }

    public function getFirstOrNull(int $id): Teammate|null
    {
        return Teammate::where('id', $id)->firstOr(function () {
            return null;
        });
    }

    /**
     * @inheritDoc
     */
    public function getAllUsingGrid(InputSource $input, int $pageSize = 15): Grid
    {
        $provider = new EloquentDataProvider(Teammate::query());

        $grid = new Grid($provider, [
            new Column('id'),
            new Column('full_name'),
            new Column('position'),
            new Column('description'),
            (new Column('image'))->setValueFormatter(function ($value) {
                return '<img src="' . asset('storage/teammates_images/' . $value) . '" alt="preview" width="150"';
            }),

            new PaginationControl($input->option('page', 1), $pageSize),
            new FilterControl('id', FilterOperation::OPERATOR_LIKE, $input->option('filt_id')),
            new FilterControl('name', FilterOperation::OPERATOR_LIKE, $input->option('filt_name')),
        ]);

        $styles = new BootstrapStyling();
        $styles->apply($grid);

        return $grid;
    }
}
