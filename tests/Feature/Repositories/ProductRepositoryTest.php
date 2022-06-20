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
use ViewComponents\Grids\Grid;
use ViewComponents\ViewComponents\Input\InputSource;

class ProductRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected ProductRepositoryInterface $repository;

    public function setUp(): void
    {
        parent::setUp();

        $this->repository = app(ProductRepository::class);
        
    }

    public function testGetAllUsingGrid()
    {
        $input = new InputSource([]);
        $result = $this->repository->getAllUsingGrid($input);

        $this->assertInstanceOf(Grid::class, $result);
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
        $this->assertInstanceOf(Product::class, $product_found);
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

        array_map(function ($element) {
            $this->assertInstanceOf(Product::class, $element);
        }, $data->all());

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
        $this->assertGreaterThanOrEqual($data[1]->price, $data[0]->price);

        $filters = ['order' => ['column' => 'price', 'sort' => 'asc']];
        $data = $this->repository->getCatalogWithPagAndFilters($filters);

        $this->assertLessThanOrEqual($data[1]->price, $data[0]->price);

        $data = $this->repository->getCatalogWithPagAndFilters([]);

        $this->assertCount(2, $data->items());

        // LIKE CLAUSE

        $filters = ['like' => 'some name but not exists'];
        $data = $this->repository->getCatalogWithPagAndFilters($filters);
        $this->assertEmpty($data->items());

        $filters = ['like' => ''];
        $data = $this->repository->getCatalogWithPagAndFilters($filters);
        $this->assertCount(2, $data->items());
        $this->assertEquals($products[0]->id, ($data->items())[0]->id);
        $this->assertEquals($products[1]->id, ($data->items())[1]->id);

        $filters = ['like' => $products[0]->name];
        $data = $this->repository->getCatalogWithPagAndFilters($filters);
        $this->assertCount(1, $data->items());
        $this->assertEquals($products[0]->id, ($data->items())[0]->id);
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

    public function testGetSimilarProducts()
    {
        $product = Product::factory()->createOne();

        $data = $this->repository->getSimilarInSizeProducts($product);

        $this->assertNotNull($data);
        $this->assertInstanceOf(Collection::class, $data);
        $this->assertEmpty($data);

        Product::factory(2)->create(['size_id' => $product->size_id]);
        Product::factory()->createOne();

        $data = $this->repository->getSimilarInSizeProducts($product, 1);

        $this->assertCount(1, $data);
        $this->assertNotEquals($data[0]->id, $product->id);
        $this->assertEquals($data[0]->size_id, $product->size_id);
        $this->assertGreaterThan(0, $data[0]->count);

        $data = $this->repository->getSimilarInSizeProducts($product);

        $this->assertCount(2, $data);
        $this->assertEquals($data[0]->size_id, $product->size_id);
        $this->assertEquals($data[1]->size_id, $product->size_id);
    }

    public function testGetRandomElements()
    {
        $data = $this->repository->getRandom();

        $this->assertNotNull($data);
        $this->assertInstanceOf(Collection::class, $data);
        $this->assertEmpty($data);

        $products = Product::factory(2)->create();

        $data = $this->repository->getRandom(1);

        $this->assertCount(1, $data);
        $this->assertTrue(in_array($data[0]->id, [$products[0]->id, $products[1]->id]));

        $data = $this->repository->getRandom();

        $this->assertCount(2, $data);
    }

    public function testGetProductsById()
    {
        $ids = [];
        $data = $this->repository->getProductsByIds($ids);

        $this->assertNotNull($data);
        $this->assertInstanceOf(Collection::class, $data);
        $this->assertEmpty($data);

        $ids = [123, 221];
        $data = $this->repository->getProductsByIds($ids);

        $this->assertEmpty($data);

        $products = Product::factory(2)->create();
        $ids = [$products[0]->id, $products[1]->id];
        $data = $this->repository->getProductsByIds($ids);

        $this->assertCount(2, $data);
        $this->assertTrue(in_array($data[0]->id, $ids));
        $this->assertTrue(in_array($data[1]->id, $ids));
    }
}
