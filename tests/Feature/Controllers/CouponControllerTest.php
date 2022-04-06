<?php

namespace Tests\Feature\Controllers;

use App\Models\Category;
use App\Models\Coupon;
use App\Models\User;
use App\Services\Interfaces\CategoryRepositoryInterface;
use App\Services\Interfaces\CouponsRepositoryInterface;
use App\Services\Repositories\CategoryRepository;
use App\Services\Repositories\CouponsRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class CouponControllerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->user_admin = User::factory()->createOne(['status' => User::$status_admin]);
    }

    public function testSuccessCreate()
    {
        $data = ['percent' => 25, 'token' => Str::random()];

        $this->assertDatabaseMissing(Coupon::class, $data);

        $response = $this->actingAs($this->user_admin)
            ->post(route('admin.coupon.create'), $data)
            ->assertRedirect(route('admin.coupons'));

        $this->assertDatabaseHas(Coupon::class, $data);

        $response->assertSessionDoesntHaveErrors();
        $response->assertSessionHas('status_success');
    }

    public function testCreateFailedModelSave()
    {
        $model_mock = $this->getMockBuilder(Coupon::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['save'])
            ->getMock();

        $model_mock->expects($this->once())
            ->method('save')
            ->willReturn(false);

        $this->instance(
            Coupon::class,
            $model_mock
        );

        $data = ['percent' => 25, 'token' => Str::random()];

        $response = $this->actingAs($this->user_admin)
            ->post(route('admin.coupon.create'), $data)
            ->assertRedirect(route('admin.coupons'));

        $response->assertSessionDoesntHaveErrors();
        $response->assertSessionHas('status_failed');
    }

    public function testDeleteSuccess()
    {
        $coupon = Coupon::factory()->createOne();

        $rep_mock = $this->getMockBuilder(CouponsRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getFirstOrNull'])
            ->getMock();

        $rep_mock->expects($this->once())
            ->method('getFirstOrNull')
            ->with($coupon->id)
            ->willReturn($coupon);

        $this->instance(
            CouponsRepositoryInterface::class,
            $rep_mock
        );

        $data = ['id' => $coupon->id];

        $response = $this->actingAs($this->user_admin)
            ->post(route('admin.coupon.delete'), $data)
            ->assertRedirect(route('admin.coupons'));

        $this->assertDatabaseMissing(Coupon::class, $coupon->attributesToArray());

        $response->assertSessionDoesntHaveErrors();
        $response->assertSessionHas('status_success');
    }

    public function testDeleteFailedModelDelete()
    {
        $coupon = Coupon::factory()->createOne();

        $model_mock = $this->getMockBuilder(Coupon::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['delete'])
            ->getMock();

        $model_mock->expects($this->once())
            ->method('delete')
            ->willReturn(false);

        $rep_mock = $this->getMockBuilder(CouponsRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getFirstOrNull'])
            ->getMock();

        $rep_mock->expects($this->once())
            ->method('getFirstOrNull')
            ->with($coupon->id)
            ->willReturn($model_mock);

        $this->instance(
            CouponsRepositoryInterface::class,
            $rep_mock
        );

        $data = ['id' => $coupon->id];

        $response = $this->actingAs($this->user_admin)
            ->post(route('admin.coupon.delete'), $data)
            ->assertRedirect(route('admin.coupons'));

        $response->assertSessionDoesntHaveErrors();
        $response->assertSessionHas('status_failed');
    }
}
