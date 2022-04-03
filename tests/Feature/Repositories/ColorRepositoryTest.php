<?php

namespace Repositories;

use App\Models\Color;
use App\Services\Interfaces\ColorRepositoryInterface;
use App\Services\Repositories\ColorRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ColorRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected ColorRepositoryInterface $repository;

    public function setUp(): void
    {
        parent::setUp();

        $this->repository = app(ColorRepository::class);
    }

    public function testFirstOrNullIfNotExist()
    {
        $col = $this->repository->getFirstOrNull(1);

        $this->assertNull($col);
    }

    public function testFirstOrNullIfExist()
    {
        $col_created = Color::factory()->createOne();
        $col_found = $this->repository->getFirstOrNull($col_created->id);

        $this->assertNotNull($col_found);
        $this->assertInstanceOf(Color::class, $col_found);
        $this->assertEquals($col_created->id, $col_found->id);
    }

    public function testGetForeignColumnName()
    {
        $this->assertEquals('color_id', $this->repository->getForeignColumnName());
    }

    public function testGetAllIfNotHaveElements()
    {
        $data = $this->repository->getAll();

        $this->assertInstanceOf(Collection::class, $data);
        $this->assertEmpty($data);
    }

    public function testGetAllIfHaveElements()
    {
        Color::factory(2)->create();
        $data = $this->repository->getAll();

        $this->assertInstanceOf(Collection::class, $data);
        $this->assertCount(2, $data);
    }
}
