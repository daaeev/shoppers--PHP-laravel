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
        $this->assertNotEmpty($data);
        $this->assertCount(2, $data);
    }
}
