<?php

namespace Tests\Feature\Repositories;

use App\Models\Product;
use App\Services\Interfaces\ProductRepositoryInterface;
use App\Services\Repositories\ProductRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected ProductRepositoryInterface $repository;

    public function setUp(): void
    {
        $this->repository = new ProductRepository();
        parent::setUp();
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
}
