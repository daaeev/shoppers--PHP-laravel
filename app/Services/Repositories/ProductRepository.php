<?php

namespace App\Services\Repositories;

use App\Models\Product;
use ViewComponents\Grids\Component\Column;
use ViewComponents\Grids\Grid;
use ViewComponents\ViewComponents\Component\Control\FilterControl;
use ViewComponents\ViewComponents\Component\Control\PaginationControl;
use ViewComponents\ViewComponents\Customization\CssFrameworks\BootstrapStyling;
use ViewComponents\ViewComponents\Data\Operation\FilterOperation;
use ViewComponents\ViewComponents\Input\InputSource;
use ViewComponents\Eloquent\EloquentDataProvider;

class ProductRepository implements \App\Services\Interfaces\ProductRepositoryInterface
{
    /**
     * @inheritDoc
     */
    public function getFirstOrNull(int $id): Product|null
    {
        return Product::where('id', $id)->firstOr(function () {
            return null;
        });
    }

    public function getAllUsingGrid(InputSource $input, int $pageSize = 15): Grid
    {
        $provider = new EloquentDataProvider(Product::query());

        $grid = new Grid($provider, [
            new Column('id'),
            new Column('name'),
            new Column('subname'),
            new Column('description'),
            new Column('price'),
            new Column('discount_price'),
            new Column('category_id'),
            new Column('size_id'),
            new Column('color_id'),
            new Column('count'),
            (new Column('main_image'))->setValueFormatter(function ($value) {
                return '<img src="' . asset('storage/products_images') . "/$value" . '" alt="product preview" width="150"';
            }),
            (new Column('preview_image'))->setValueFormatter(function ($value) {
                return '<img src="' . asset('storage/products_images') . "/$value" . '" alt="product preview" width="150"';
            }),
            new PaginationControl($input->option('page', 1), $pageSize),
            new FilterControl('id', FilterOperation::OPERATOR_LIKE, $input->option('filt_id')),
            new FilterControl('name', FilterOperation::OPERATOR_LIKE, $input->option('filt_name')),
            new FilterControl('count', FilterOperation::OPERATOR_LIKE, $input->option('filt_count')),
            new FilterControl('category', FilterOperation::OPERATOR_LIKE, $input->option('filt_category')),
            new FilterControl('color', FilterOperation::OPERATOR_LIKE, $input->option('filt_color')),
            new FilterControl('size', FilterOperation::OPERATOR_LIKE, $input->option('filt_size')),

        ]);

        $styles = new BootstrapStyling();
        $styles->apply($grid);

        return $grid;
    }
}
