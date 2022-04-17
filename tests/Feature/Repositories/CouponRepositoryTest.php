<?php

namespace Tests\Feature\Repositories;

use App\Models\Coupon;
use App\Services\Interfaces\CouponsRepositoryInterface;
use App\Services\Repositories\CouponsRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use ViewComponents\Grids\Grid;
use ViewComponents\ViewComponents\Input\InputSource;

class CouponRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected CouponsRepositoryInterface $repository;

    public function setUp(): void
    {
        parent::setUp();

        $this->repository = app(CouponsRepository::class);
    }

    public function testGetAllUsingGrid()
    {
        $input = new InputSource([]);
        $result = $this->repository->getAllUsingGrid($input);

        $this->assertInstanceOf(Grid::class, $result);
    }

    public function testFirstOrNullIfNotExist()
    {
        $coupon = $this->repository->getFirstOrNull(1);

        $this->assertNull($coupon);
    }

    public function testFirstOrNullIfExist()
    {
        $coupon_created = Coupon::factory()->createOne();
        $coupon_found = $this->repository->getFirstOrNull($coupon_created->id);

        $this->assertNotNull($coupon_found);
        $this->assertEquals($coupon_created->id, $coupon_found->id);
        $this->assertInstanceOf(Coupon::class, $coupon_found);
    }

    public function testGetFirstNotActivatedIfNotExist()
    {
        $coupon = Coupon::factory()->createOne(['activated' => true]);

        $result = $this->repository->getFirstNotActivatedByTokenOrNull($coupon->token);

        $this->assertNull($result);
    }

    public function testGetFirstNotActivatedIfExist()
    {
        $coupon = Coupon::factory()->createOne();

        $result = $this->repository->getFirstNotActivatedByTokenOrNull($coupon->token);

        $this->assertNotNull($result);
        $this->assertEquals($result->id, $coupon->id);
        $this->assertInstanceOf(Coupon::class, $result);
    }
}
