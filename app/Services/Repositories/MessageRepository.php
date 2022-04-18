<?php

namespace App\Services\Repositories;

use App\Models\Message;
use ViewComponents\Eloquent\EloquentDataProvider;
use ViewComponents\Grids\Component\Column;
use ViewComponents\Grids\Grid;
use ViewComponents\ViewComponents\Component\Control\FilterControl;
use ViewComponents\ViewComponents\Component\Control\PaginationControl;
use ViewComponents\ViewComponents\Customization\CssFrameworks\BootstrapStyling;
use ViewComponents\ViewComponents\Data\Operation\FilterOperation;
use ViewComponents\ViewComponents\Input\InputSource;

class MessageRepository implements \App\Services\Interfaces\MessageRepositoryInterface
{
    /**
     * @inheritDoc
     */
    public function getFirstOrNull(int $id): Message|null
    {
        return Message::where('id', $id)->firstOr(function () {
            return null;
        });
    }

    /**
     * @inheritDoc
     */
    public function getAllUsingGrid(InputSource $input, int $pageSize = 15): Grid
    {
        $provider = new EloquentDataProvider(Message::query());

        $grid = new Grid($provider, [
            new Column('id'),
            new Column('first_name'),
            new Column('last_name'),
            new Column('title'),
            new Column('content'),
            (new Column('answered'))->setValueFormatter(function ($value) {
                if ($value) {
                    return 'Yes';
                }

                return 'No';
            }),
            (new Column('user_id'))->setValueFormatter(function ($value) {
                return '<a href="' . route('admin.users', ['filt_id' => $value]) . '">' . $value . '</a>';
            }),

            new PaginationControl($input->option('page', 1), $pageSize),
            new FilterControl('id', FilterOperation::OPERATOR_LIKE, $input->option('filt_id')),
            new FilterControl('user_id', FilterOperation::OPERATOR_LIKE, $input->option('filt_user_id')),
            new FilterControl('answered', FilterOperation::OPERATOR_LIKE, $input->option('filt_answered')),
        ]);

        $styles = new BootstrapStyling();
        $styles->apply($grid);

        return $grid;
    }
}
