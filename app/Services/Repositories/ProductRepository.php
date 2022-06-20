<?php

namespace App\Services\Repositories;

use App\Models\Category;
use App\Models\Color;
use App\Models\Product;
use App\Models\Size;
use App\Services\Interfaces\divided\GetAllForeignInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
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

    /**
     * @inheritDoc
     */
    public function getForeignDataForForm(GetAllForeignInterface ...$repositories): array
    {
        $array = [];

        foreach ($repositories as $repository) {
            $array[$repository->getForeignColumnName()] = $repository->getAll();
        }

        return $array;
    }

    /**
     * @inheritDoc
     */
    public function getCatalogWithPag(int $pageSize = 15): LengthAwarePaginator
    {
        return Product::where([['count', '>', 0]])->with('size')->paginate($pageSize);
    }

    /**
     * @inheritDoc
     */
    public function getCatalogWithPagAndFilters(array $filters, int $pageSize = 15): LengthAwarePaginator
    {
        $query = Product::where([['count', '>', 0]])->with('size');

        if (empty($filters)) {
            return $query->paginate($pageSize);
        }

        if (isset($filters['where'])) {
            foreach ($filters['where'] as $column => $value) {
                $query->where([[$column, '=', $value]]);
            }
        }

        if (isset($filters['like'])) {
            $query->where('name', 'like', '%' . $filters['like'] . '%');
        }

        if (isset($filters['order'])) {
            if ($filters['order']['column'] == 'name') {
                $query->orderBy($filters['order']['column'], $filters['order']['sort']);

            } else if ($filters['order']['column'] == 'price') {
                $query->join(
                    'exchange_rates', 
                    'exchange_rates.currency_code',
                    '=',
                    'products.currency'
                )->orderByRaw('products.price * exchange_rates.exchange ' . $filters['order']['sort']);
            }
        }

        return $query->paginate($pageSize);
    }

    /**
     * @inheritDoc
     */
    public function getCategoriesFilterData(): Collection
    {
        return Category::withCount(['products' => function (Builder $query) {
            $query->where([['count', '>', 0]]);
        }])->get();
    }

    /**
     * @inheritDoc
     */
    public function getColorsFilterData(): Collection
    {
        return Color::withCount(['products' => function (Builder $query) {
            $query->where([['count', '>', 0]]);
        }])->get();
    }

    /**
     * @inheritDoc
     */
    public function getSizesFilterData(): Collection
    {
        return Size::withCount(['products' => function (Builder $query) {
            $query->where([['count', '>', 0]]);
        }])->get();
    }

    /**
     * @inheritDoc
     */
    public function getFiltersData(): array
    {
        return [
            'Categories' => $this->getCategoriesFilterData(),
            'Colors' => $this->getColorsFilterData(),
            'Sizes' => $this->getSizesFilterData(),
        ];
    }

    /**
     * @inheritDoc
     */
    public function getSimilarInSizeProducts(Product $product, int $count = 6): Collection
    {
        return Product::where([
            ['count', '>', 0],
            ['id', '!=', $product->id],
            ['size_id', '=', $product->size_id],
        ])
            ->with('size')
            ->limit($count)
            ->inRandomOrder()
            ->get();
    }

    /**
     * @inheritDoc
     */
    public function getRandom(int $count = 6): Collection
    {
        return Product::with('size')
            ->limit($count)
            ->inRandomOrder()
            ->get();
    }

    /**
     * @inheritDoc
     */
    public function getProductsByIds(array $products_ids): Collection
    {
        return Product::whereIn('id', $products_ids)->with('size')->get();
    }

    /**
     * @inheritDoc
     */
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
            new Column('currency'),
            (new Column('category_id'))->setValueFormatter(function ($value) {
                return '<a href="' . route('admin.categories', ['filt_id' => $value]) . '">' . $value . '</a>';
            }),
            (new Column('size_id'))->setValueFormatter(function ($value) {
                return '<a href="' . route('admin.sizes', ['filt_id' => $value]) . '">' . $value . '</a>';
            }),
            (new Column('color_id'))->setValueFormatter(function ($value) {
                return '<a href="' . route('admin.colors', ['filt_id' => $value]) . '">' . $value . '</a>';
            }),
            new Column('count'),
            (new Column('main_image'))->setValueFormatter(function ($value) {
                return '<img src="' . asset('storage/products_images') . "/$value" . '" alt="product preview" width="150"';
            }),
            (new Column('preview_image'))->setValueFormatter(function ($value) {
                if ($value) {
                    return '<img src="' . asset('storage/products_images') . "/$value" . '" alt="product preview" width="150"';
                }

                return '';
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
