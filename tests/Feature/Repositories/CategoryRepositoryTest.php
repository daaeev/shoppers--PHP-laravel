<?php

namespace Repositories;

use App\Models\Category;
use App\Services\Interfaces\CategoryRepositoryInterface;
use App\Services\Repositories\CategoryRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use ViewComponents\Grids\Grid;
use ViewComponents\ViewComponents\Input\InputSource;

class CategoryRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected CategoryRepositoryInterface $repository;

    public function setUp(): void
    {
        parent::setUp();

        $this->repository = app(CategoryRepository::class);
    }

    public function testGetAllUsingGrid()
    {
        $input = new InputSource([]);
        $result = $this->repository->getAllUsingGrid($input);

        $this->assertInstanceOf(Grid::class, $result);
    }

    public function testFirstOrNullIfNotExist()
    {
        $cat = $this->repository->getFirstOrNull(1);

        $this->assertNull($cat);
    }

    public function testFirstOrNullIfExist()
    {
        $cat_created = Category::factory()->createOne();
        $cat_found = $this->repository->getFirstOrNull($cat_created->id);

        $this->assertNotNull($cat_found);
        $this->assertEquals($cat_created->id, $cat_found->id);
        $this->assertInstanceOf(Category::class, $cat_found);
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
        $this->assertCount(2, $data);
    }
}
