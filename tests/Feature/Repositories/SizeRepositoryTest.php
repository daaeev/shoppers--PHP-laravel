<?php

namespace Repositories;

use App\Models\Size;
use App\Services\Interfaces\SizeRepositoryInterface;
use App\Services\Repositories\SizeRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SizeRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected SizeRepositoryInterface $repository;

    public function setUp(): void
    {
        parent::setUp();

        $this->repository = app(SizeRepository::class);
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
        $this->assertNotEmpty($data);
        $this->assertCount(2, $data);
    }
}
