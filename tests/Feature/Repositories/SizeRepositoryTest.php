<?php

namespace Repositories;

use App\Models\Size;
use App\Services\Interfaces\SizeRepositoryInterface;
use App\Services\Repositories\SizeRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use ViewComponents\Grids\Grid;
use ViewComponents\ViewComponents\Input\InputSource;

class SizeRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected SizeRepositoryInterface $repository;

    public function setUp(): void
    {
        parent::setUp();

        $this->repository = app(SizeRepository::class);
    }

    public function testGetAllUsingGrid()
    {
        $input = new InputSource([]);
        $result = $this->repository->getAllUsingGrid($input);

        $this->assertInstanceOf(Grid::class, $result);
    }

    public function testFirstOrNullIfNotExist()
    {
        $col = $this->repository->getFirstOrNull(1);

        $this->assertNull($col);
    }

    public function testFirstOrNullIfExist()
    {
        $size_created = Size::factory()->createOne();
        $size_found = $this->repository->getFirstOrNull($size_created->id);

        $this->assertNotNull($size_found);
        $this->assertInstanceOf(Size::class, $size_found);
        $this->assertEquals($size_created->id, $size_found->id);
    }

    public function testGetForeignColumnName()
    {
        $this->assertEquals('size_id', $this->repository->getForeignColumnName());
    }

    public function testGetAllIfNotHaveElements()
    {
        $data = $this->repository->getAll();

        $this->assertInstanceOf(Collection::class, $data);
        $this->assertEmpty($data);
    }

    public function testGetAllIfHaveElements()
    {
        Size::factory(2)->create();
        $data = $this->repository->getAll();

        $this->assertInstanceOf(Collection::class, $data);
        $this->assertCount(2, $data);
    }
}
