<?php

namespace App\Services\Repositories;

use App\Models\News;
use ViewComponents\Eloquent\EloquentDataProvider;
use ViewComponents\Grids\Component\Column;
use ViewComponents\Grids\Grid;
use ViewComponents\ViewComponents\Component\Control\FilterControl;
use ViewComponents\ViewComponents\Component\Control\PaginationControl;
use ViewComponents\ViewComponents\Customization\CssFrameworks\BootstrapStyling;
use ViewComponents\ViewComponents\Data\Operation\FilterOperation;
use ViewComponents\ViewComponents\Input\InputSource;

class NewsRepository implements \App\Services\Interfaces\NewsRepositoryInterface
{
    /**
     * @inheritDoc
     */
    public function getFirstOrNull(int $id): News|null
    {
        return News::where('id', $id)->firstOr(function () {
            return null;
        });
    }

    /**
     * @inheritDoc
     */
    public function getAllUsingGrid(InputSource $input, int $pageSize = 15): Grid
    {
        $provider = new EloquentDataProvider(News::query());

        $grid = new Grid($provider, [
            new Column('id'),
            new Column('title'),
            new Column('content'),
            (new Column('sent'))->setValueFormatter(function ($value) {
                if ($value) {
                    return 'True';
                }

                return 'False';
            }),

            new PaginationControl($input->option('page', 1), $pageSize),
            new FilterControl('id', FilterOperation::OPERATOR_LIKE, $input->option('filt_id')),
            new FilterControl('sent', FilterOperation::OPERATOR_LIKE, $input->option('filt_sent')),
        ]);

        $styles = new BootstrapStyling();
        $styles->apply($grid);

        return $grid;
    }
}
