<?php

namespace Tests\Feature\Repositories;

use App\Models\Category;
use App\Models\Color;
use App\Models\Product;
use App\Models\Size;
use App\Services\Interfaces\ProductRepositoryInterface;
use App\Services\Repositories\ColorRepository;
use App\Services\Repositories\ProductRepository;
use App\Services\Repositories\SizeRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Tests\TestCase;

class ProductRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected ProductRepositoryInterface $repository;

    public function setUp(): void
    {
        parent::setUp();

        $this->repository = app(ProductRepository::class);
    }

    public function testFirstOrNullIfNotExist()
    {
        $product = $this->repository->getFirstOrNull(1);

        $this->assertNull($product);
    }

    public function testFirstOrNullIfExist()
    {
        $product_created = Product::factory()->createOne();
        $product_found = $this->repository->getFirstOrNull($product_created->id);

        $this->assertNotNull($product_found);
        $this->assertEquals($product_created->id, $product_found->id);
    }

    public function testGetForeignDataIfNoRepositoriesGive()
    {
        $data = $this->repository->getForeignDataForForm();

        $this->assertEmpty($data);
    }

    public function testGetForeignDataIfRepositoriesGive()
    {
        $color_rep = app(ColorRepository::class);
        $size_rep = app(SizeRepository::class);
        $data = $this->repository->getForeignDataForForm($color_rep, $size_rep);

        $this->assertNotEmpty($data);

        $this->assertCount(2, $data);

        $this->assertArrayHasKey($color_rep->getForeignColumnName(), $data);
        $this->assertArrayHasKey($size_rep->getForeignColumnName(), $data);

        $this->assertInstanceOf(Collection::class, $data[$color_rep->getForeignColumnName()]);
        $this->assertInstanceOf(Collection::class, $data[$size_rep->getForeignColumnName()]);
    }

    public function testGetForeignDataIfRepositoriesGiveWithElements()
    {
        $color_rep = app(ColorRepository::class);
        Color::factory(2)->create();

        $data = $this->repository->getForeignDataForForm($color_rep);

        $this->assertNotEmpty($data);
        $this->assertArrayHasKey($color_rep->getForeignColumnName(), $data);
        $this->assertCount(2, $data[$color_rep->getForeignColumnName()]);
    }

    public function testGetCatalogWithPag()
    {
        $data = $this->repository->getCatalogWithPag();

        $this->assertNotNull($data);
        $this->assertInstanceOf(LengthAwarePaginator::class, $data);
        $this->assertEmpty($data);

        Product::factory(2)->create();
        Product::factory()->createOne(['count' => 0]);
        $data = $this->repository->getCatalogWithPag();

        $this->assertNotNull($data);
        $this->assertInstanceOf(LengthAwarePaginator::class, $data);
        $this->assertCount(2, $data->items());
        $this->assertEquals(15, $data->perPage());

        $data = $this->repository->getCatalogWithPag(1);

        $this->assertEquals(1, $data->perPage());
    }

    public function testGetCatalogWithPagAndFilters()
    {
        $data = $this->repository->getCatalogWithPagAndFilters([]);

        $this->assertNotNull($data);
        $this->assertInstanceOf(LengthAwarePaginator::class, $data);
        $this->assertEmpty($data);
        $this->assertEquals(15, $data->perPage());

        $data = $this->repository->getCatalogWithPagAndFilters([], 2);

        $this->assertEquals(2, $data->perPage());

        $products = Product::factory(2)->create();
        Product::factory()->createOne(['count' => 0]);

        // WHERE CLAUSE

        $filters = ['where' => ['category_id' => $products[0]->category_id]];
        $data = $this->repository->getCatalogWithPagAndFilters($filters);

        $this->assertCount(1, $data->items());
        $this->assertEquals($products[0]->id, ($data->items())[0]->id);

        $filters = ['where' => ['size_id' => $products[0]->size_id]];
        $data = $this->repository->getCatalogWithPagAndFilters($filters);

        $this->assertCount(1, $data->items());
        $this->assertEquals($products[0]->id, ($data->items())[0]->id);

        $filters = ['where' => ['color_id' => $products[0]->color_id]];
        $data = $this->repository->getCatalogWithPagAndFilters($filters);

        $this->assertCount(1, $data->items());
        $this->assertEquals($products[0]->id, ($data->items())[0]->id);

        // ORDER CLAUSE

        $filters = ['order' => ['column' => 'price', 'sort' => 'desc']];
        $data = $this->repository->getCatalogWithPagAndFilters($filters);

        $this->assertCount(2, $data->items());
        $this->assertGreaterThan($data[1]->price, $data[0]->price);

        $filters = ['order' => ['column' => 'price', 'sort' => 'asc']];
        $data = $this->repository->getCatalogWithPagAndFilters($filters);

        $this->assertLessThan($data[1]->price, $data[0]->price);

        $data = $this->repository->getCatalogWithPagAndFilters([]);

        $this->assertCount(2, $data->items());
    }

    public function testGetCategoriesFiltersData()
    {
        $data = $this->repository->getCategoriesFilterData();

        $this->assertNotNull($data);
        $this->assertInstanceOf(Collection::class, $data);
        $this->assertEmpty($data);

        $category = Category::factory()->createOne();
        Product::factory()->createOne(['category_id' => $category->id]);
        Product::factory()->createOne(['count' => 0, 'category_id' => $category->id]);

        $data = $this->repository->getCategoriesFilterData();

        $this->assertInstanceOf(Collection::class, $data);
        $this->assertCount(1, $data);
        $this->assertEquals(1, $data[0]->products_count);

        Product::factory()->createOne(['category_id' => $category->id]);

        $data = $this->repository->getCategoriesFilterData();

        $this->assertEquals(2, $data[0]->products_count);

        Category::factory()->createOne();

        $data = $this->repository->getCategoriesFilterData();

        $this->assertCount(2, $data);
    }

    public function testGetColorsFiltersData()
    {
        $data = $this->repository->getColorsFilterData();

        $this->assertNotNull($data);
        $this->assertInstanceOf(Collection::class, $data);
        $this->assertEmpty($data);

        $color = Color::factory()->createOne();
        Product::factory()->createOne(['color_id' => $color->id]);
        Product::factory()->createOne(['count' => 0, 'color_id' => $color->id]);

        $data = $this->repository->getColorsFilterData();

        $this->assertInstanceOf(Collection::class, $data);
        $this->assertCount(1, $data);
        $this->assertEquals(1, $data[0]->products_count);

        Product::factory()->createOne(['color_id' => $color->id]);

        $data = $this->repository->getColorsFilterData();

        $this->assertEquals(2, $data[0]->products_count);

        Color::factory()->createOne();

        $data = $this->repository->getColorsFilterData();

        $this->assertCount(2, $data);
    }

    public function testGetSizesFiltersData()
    {
        $data = $this->repository->getSizesFilterData();

        $this->assertNotNull($data);
        $this->assertInstanceOf(Collection::class, $data);
        $this->assertEmpty($data);

        $size = Size::factory()->createOne();
        Product::factory()->createOne(['size_id' => $size->id]);
        Product::factory()->createOne(['count' => 0, 'size_id' => $size->id]);

        $data = $this->repository->getSizesFilterData();

        $this->assertCount(1, $data);
        $this->assertEquals(1, $data[0]->products_count);

        Product::factory()->createOne(['size_id' => $size->id]);

        $data = $this->repository->getSizesFilterData();

        $this->assertEquals(2, $data[0]->products_count);

        Size::factory()->createOne();

        $data = $this->repository->getSizesFilterData();

        $this->assertCount(2, $data);
    }

    public function testGetFiltersData()
    {
        $data = $this->repository->getFiltersData();

        $this->assertIsArray($data);
        $this->assertArrayHasKey('Categories', $data);
        $this->assertArrayHasKey('Colors', $data);
        $this->assertArrayHasKey('Sizes', $data);

        $this->assertInstanceOf(Collection::class, $data['Categories']);
        $this->assertInstanceOf(Collection::class, $data['Colors']);
        $this->assertInstanceOf(Collection::class, $data['Sizes']);
    }
}
