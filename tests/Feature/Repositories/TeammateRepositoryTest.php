<?php

namespace Tests\Feature\Repositories;

use App\Models\Teammate;
use App\Services\Interfaces\TeammatesRepositoryInterface;
use App\Services\Repositories\TeammatesRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeammateRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected TeammatesRepositoryInterface $repository;

    public function setUp(): void
    {
        parent::setUp();

        $this->repository = app(TeammatesRepository::class);
    }

    public function testFirstOrNullIfNotExist()
    {
        $cat = $this->repository->getFirstOrNull(1);

        $this->assertNull($cat);
    }

    public function testFirstOrNullIfExist()
    {
        $cat_created = Teammate::factory()->createOne();
        $cat_found = $this->repository->getFirstOrNull($cat_created->id);

        $this->assertNotNull($cat_found);
        $this->assertEquals($cat_created->id, $cat_found->id);
        $this->assertInstanceOf(Teammate::class, $cat_found);
    }

    public function testGetAllIfNotHaveElements()
    {
        $data = $this->repository->getAll();

        $this->assertInstanceOf(Collection::class, $data);
        $this->assertEmpty($data);
    }

    public function testGetAllIfHaveElements()
    {
        Teammate::factory(2)->create();
        $data = $this->repository->getAll();

        $this->assertInstanceOf(Collection::class, $data);
        $this->assertCount(2, $data);
    }
}
