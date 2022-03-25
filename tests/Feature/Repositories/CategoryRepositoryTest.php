<?php

namespace Repositories;

use App\Models\Category;
use App\Services\Interfaces\CategoryRepositoryInterface;
use App\Services\Repositories\CategoryRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected CategoryRepositoryInterface $repository;

    public function setUp(): void
    {
        parent::setUp();

        $this->repository = app(CategoryRepository::class);
    }

    public function testGetForeignColumnName()
    {
        $this->assertEquals('category_id', $this->repository->getForeignColumnName());
    }

    public function testGetAllIfNotHaveElements()
    {
        $data = $this->repository->getAll();

        $this->assertInstanceOf(Collection::class, $data);
        $this->assertEmpty($data);
    }

    public function testGetAllIfHaveElements()
    {
        Category::factory(2)->create();
        $data = $this->repository->getAll();

        $this->assertInstanceOf(Collection::class, $data);
        $this->assertNotEmpty($data);
        $this->assertCount(2, $data);
    }
}
