<?php

namespace App\Services\Repositories;

use App\Models\Coupon;
use ViewComponents\Eloquent\EloquentDataProvider;
use ViewComponents\Grids\Component\Column;
use ViewComponents\Grids\Grid;
use ViewComponents\ViewComponents\Component\Control\FilterControl;
use ViewComponents\ViewComponents\Component\Control\PaginationControl;
use ViewComponents\ViewComponents\Customization\CssFrameworks\BootstrapStyling;
use ViewComponents\ViewComponents\Data\Operation\FilterOperation;
use ViewComponents\ViewComponents\Input\InputSource;

class CouponsRepository implements \App\Services\Interfaces\CouponsRepositoryInterface
{
    /**
     * @inheritDoc
     */
    public function getFirstOrNull(int $id): Coupon|null
    {
        return Coupon::where('id', $id)->firstOr(function () {
            return null;
        });
    }

    /**
     * @inheritDoc
     */
    public function getAllUsingGrid(InputSource $input, int $pageSize = 15): Grid
    {
        $provider = new EloquentDataProvider(Coupon::query());

        $grid = new Grid($provider, [
            new Column('id'),
            new Column('percent'),
            new Column('token'),
            (new Column('activated'))->setValueFormatter(function ($value) {
                return ($value ? 'true' : 'false');
            }),
            (new Column('used'))->setValueFormatter(function ($value) {
                return ($value ? 'true' : 'false');
            }),
            new PaginationControl($input->option('page', 1), $pageSize),
            new FilterControl('id', FilterOperation::OPERATOR_LIKE, $input->option('filt_id')),
            new FilterControl('token', FilterOperation::OPERATOR_LIKE, $input->option('filt_token')),
            new FilterControl('activated', FilterOperation::OPERATOR_LIKE, $input->option('filt_activated')),
            new FilterControl('used', FilterOperation::OPERATOR_LIKE, $input->option('filt_used')),

        ]);

        $styles = new BootstrapStyling();
        $styles->apply($grid);

        return $grid;
    }
}
