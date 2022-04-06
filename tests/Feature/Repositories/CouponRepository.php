<?php

namespace Tests\Feature\Repositories;

use App\Models\Coupon;
use App\Services\Interfaces\CouponsRepositoryInterface;
use App\Services\Repositories\CouponsRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CouponRepository extends TestCase
{
    use RefreshDatabase;

    protected CouponsRepositoryInterface $repository;

    public function setUp(): void
    {
        parent::setUp();

        $this->repository = app(CouponsRepository::class);
    }

    public function testFirstOrNullIfNotExist()
    {
        $cat = $this->repository->getFirstOrNull(1);

        $this->assertNull($cat);
    }

    public function testFirstOrNullIfExist()
    {
        $cat_created = Coupon::factory()->createOne();
        $cat_found = $this->repository->getFirstOrNull($cat_created->id);

        $this->assertNotNull($cat_found);
        $this->assertEquals($cat_created->id, $cat_found->id);
        $this->assertInstanceOf(Coupon::class, $cat_found);
    }
}
