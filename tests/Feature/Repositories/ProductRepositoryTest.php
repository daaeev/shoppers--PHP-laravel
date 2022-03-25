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
        $user = $this->repository->getFirstOrNull(1);

        $this->assertNull($user);
    }

    public function testFirstOrNullIfExist()
    {
        $user_created = Product::factory()->createOne();
        $user_found = $this->repository->getFirstOrNull($user_created->id);

        $this->assertNotNull($user_found);
        $this->assertEquals($user_created->id, $user_found->id);
    }

    public function testGetForeignDataIfNoRepositoriesGive()
    {
        $data = $this->repository->getForeignDataForForm();

        $this->assertEmpty($data);
    }

    public function testGetForeignDataIfRepositoriesGive()
    {
        $color = app(ColorRepository::class);
        $size = app(SizeRepository::class);
        $data = $this->repository->getForeignDataForForm($color, $size);

        $this->assertNotEmpty($data);

        $this->assertCount(2, $data);

        $this->assertArrayHasKey($color->getForeignColumnName(), $data);
        $this->assertArrayHasKey($size->getForeignColumnName(), $data);

        $this->assertInstanceOf(Collection::class, $data[$color->getForeignColumnName()]);
        $this->assertInstanceOf(Collection::class, $data[$size->getForeignColumnName()]);
    }

    public function testGetForeignDataIfRepositoriesGiveWithElements()
    {
        $color = app(ColorRepository::class);
        Color::factory(2)->create();

        $data = $this->repository->getForeignDataForForm($color);

        $this->assertNotEmpty($data);
        $this->assertArrayHasKey($color->getForeignColumnName(), $data);
        $this->assertCount(2, $data[$color->getForeignColumnName()]);
    }
}
