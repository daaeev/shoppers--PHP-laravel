<?php

namespace Tests\Feature\Repositories;

use App\Models\News;
use App\Services\Interfaces\NewsRepositoryInterface;
use App\Services\Repositories\NewsRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use ViewComponents\Grids\Grid;
use ViewComponents\ViewComponents\Input\InputSource;

class NewsRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected NewsRepositoryInterface $repository;

    public function setUp(): void
    {
        parent::setUp();

        $this->repository = app(NewsRepository::class);
    }

    public function testFirstOrNullIfNotExist()
    {
        $col = $this->repository->getFirstOrNull(1);

        $this->assertNull($col);
    }

    public function testFirstOrNullIfExist()
    {
        $col_created = News::factory()->createOne();
        $col_found = $this->repository->getFirstOrNull($col_created->id);

        $this->assertNotNull($col_found);
        $this->assertInstanceOf(News::class, $col_found);
        $this->assertEquals($col_created->id, $col_found->id);
    }

    public function testGetAllUsingGrid()
    {
        $input = new InputSource([]);
        $result = $this->repository->getAllUsingGrid($input);

        $this->assertInstanceOf(Grid::class, $result);
    }
}
