<?php

namespace Tests\Feature\Repositories;

use App\Models\Color;
use App\Models\Product;
use App\Services\Interfaces\ProductRepositoryInterface;
use App\Services\Repositories\ColorRepository;
use App\Services\Repositories\ProductRepository;
use App\Services\Repositories\SizeRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
}
