<?php

namespace App\Services\Repositories;

use App\Models\User;
use ViewComponents\Grids\Component\Column;
use ViewComponents\Grids\Grid;
use ViewComponents\ViewComponents\Component\Control\FilterControl;
use ViewComponents\ViewComponents\Component\Control\PaginationControl;
use ViewComponents\ViewComponents\Customization\CssFrameworks\BootstrapStyling;
use ViewComponents\ViewComponents\Data\Operation\FilterOperation;
use ViewComponents\ViewComponents\Input\InputSource;
use Illuminate\Support\Facades\Auth;
use ViewComponents\Eloquent\EloquentDataProvider;

class UserRepository implements \App\Services\Interfaces\UserRepositoryInterface
{
    /**
     * @inheritDoc
     */
    public function getAuthenticated(): mixed
    {
        return Auth::user();
    }

    /**
     * @inheritDoc
     */
    public function getFirstOrNull(int $id): User|null
    {
        return User::where('id', $id)->firstOr(function () {
            return null;
        });
    }

    public function getAllUsingGrid(InputSource $input, int $pageSize = 15): Grid
    {
        $provider = new EloquentDataProvider(User::query());

        $grid = new Grid($provider, [
            new Column('id'),
            new Column('name'),
            new Column('email'),
            (new Column('email_verified_at', 'Email verify'))->setValueFormatter(function ($value) {
                return $value ? 'Verified' : 'Not verified';
            }),
            (new Column('status'))->setValueFormatter(function ($value) {
                switch ($value) {
                    case User::$status_user:
                        return 'User';
                    case User::$status_admin:
                        return 'Admin';
                    case User::$status_banned:
                        return 'Banned';
                }
            }),
            new PaginationControl($input->option('page', 1), $pageSize),
            new FilterControl('id', FilterOperation::OPERATOR_LIKE, $input->option('filt_id')),
            new FilterControl('status', FilterOperation::OPERATOR_LIKE, $input->option('filt_status')),

        ]);

        $styles = new BootstrapStyling();
        $styles->apply($grid);

        return $grid;
    }
}
