<?php

namespace Tests\Feature\Controllers;

use App\Models\Coupon;
use App\Models\User;
use App\Services\Interfaces\CouponsRepositoryInterface;
use App\Services\Interfaces\UserRepositoryInterface;
use App\Services\Repositories\CouponsRepository;
use App\Services\Repositories\UserRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AjaxCouponControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testSuccessActivate()
    {
        $user = User::factory()->createOne();
        $coupon = Coupon::factory()->createOne();

        $rep_user_mock = $this->getMockBuilder(UserRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getAuthenticated'])
            ->getMock();

        $rep_user_mock->expects($this->any())
            ->method('getAuthenticated')
            ->willReturn($user);

        $this->instance(
            UserRepositoryInterface::class,
            $rep_user_mock
        );

        $rep_coup_mock = $this->getMockBuilder(CouponsRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getFirstNotActivatedByTokenOrNull'])
            ->getMock();

        $rep_coup_mock->expects($this->once())
            ->method('getFirstNotActivatedByTokenOrNull')
            ->with($coupon->token)
            ->willReturn($coupon);

        $this->instance(
            CouponsRepositoryInterface::class,
            $rep_coup_mock
        );

        $data = ['token' => $coupon->token];

        $response = $this->actingAs($user)
            ->post(route('ajax.coupon.activate'), $data)
            ->assertOk();

        $this->assertEquals($coupon->percent, $response->content());

        $this->assertDatabaseHas(User::class, ['id' => $user->id, 'coupon_id' => $coupon->id]);
        $this->assertDatabaseHas(Coupon::class, ['id' => $coupon->id, 'activated' => true]);
    }

    public function testActivateIfCouponNotExistOrAlredyActivated()
    {
        $user = User::factory()->createOne();
        $coupon = Coupon::factory()->createOne(['activated' => true]);

        $rep_user_mock = $this->getMockBuilder(UserRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getAuthenticated'])
            ->getMock();

        $rep_user_mock->expects($this->any())
            ->method('getAuthenticated')
            ->willReturn($user);

        $this->instance(
            UserRepositoryInterface::class,
            $rep_user_mock
        );

        $rep_coup_mock = $this->getMockBuilder(CouponsRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getFirstNotActivatedByTokenOrNull'])
            ->getMock();

        $rep_coup_mock->expects($this->once())
            ->method('getFirstNotActivatedByTokenOrNull')
            ->with($coupon->token)
            ->willReturn(null);

        $this->instance(
            CouponsRepositoryInterface::class,
            $rep_coup_mock
        );

        $data = ['token' => $coupon->token];

        $response = $this->actingAs($user)
            ->post(route('ajax.coupon.activate'), $data)
            ->assertNotFound();
    }

    public function testActivateUserModelSaveFailed()
    {
        $user_mock = $this->getMockBuilder(User::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['save'])
            ->getMock();

        $user_mock->expects($this->once())
            ->method('save')
            ->willReturn(false);

        $coupon = Coupon::factory()->createOne();

        $rep_user_mock = $this->getMockBuilder(UserRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getAuthenticated'])
            ->getMock();

        $rep_user_mock->expects($this->any())
            ->method('getAuthenticated')
            ->willReturn($user_mock);

        $this->instance(
            UserRepositoryInterface::class,
            $rep_user_mock
        );

        $rep_coup_mock = $this->getMockBuilder(CouponsRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getFirstNotActivatedByTokenOrNull'])
            ->getMock();

        $rep_coup_mock->expects($this->once())
            ->method('getFirstNotActivatedByTokenOrNull')
            ->with($coupon->token)
            ->willReturn($coupon);

        $this->instance(
            CouponsRepositoryInterface::class,
            $rep_coup_mock
        );

        $data = ['token' => $coupon->token];

        $response = $this->actingAs($user_mock)
            ->post(route('ajax.coupon.activate'), $data)
            ->assertStatus(500);

        $this->assertDatabaseMissing(Coupon::class, ['id' => $coupon->id, 'activated' => true]);
    }

    public function testActivateCouponModelSaveFailed()
    {
        $coup_mock = $this->getMockBuilder(Coupon::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['save'])
            ->getMock();

        $coup_mock->expects($this->once())
            ->method('save')
            ->willReturn(false);

        $user = User::factory()->createOne();
        $coupon = Coupon::factory()->createOne();

        $rep_user_mock = $this->getMockBuilder(UserRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getAuthenticated'])
            ->getMock();

        $rep_user_mock->expects($this->any())
            ->method('getAuthenticated')
            ->willReturn($user);

        $this->instance(
            UserRepositoryInterface::class,
            $rep_user_mock
        );

        $rep_coup_mock = $this->getMockBuilder(CouponsRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getFirstNotActivatedByTokenOrNull'])
            ->getMock();

        $rep_coup_mock->expects($this->once())
            ->method('getFirstNotActivatedByTokenOrNull')
            ->with($coupon->token)
            ->willReturn($coup_mock);

        $this->instance(
            CouponsRepositoryInterface::class,
            $rep_coup_mock
        );

        $data = ['token' => $coupon->token];

        $response = $this->actingAs($user)
            ->post(route('ajax.coupon.activate'), $data)
            ->assertStatus(500);

        $this->assertDatabaseMissing(User::class, ['id' => $user->id, 'coupon_id' => $coupon->id]);
    }

    public function testActivateIfUserAlreadyHaveCoupon()
    {
        $user = User::factory()->createOne();
        $coupon = Coupon::factory()->createOne(['activated' => true]);

        $user->coupon_id = $coupon->id;

        $rep_user_mock = $this->getMockBuilder(UserRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getAuthenticated'])
            ->getMock();

        $rep_user_mock->expects($this->any())
            ->method('getAuthenticated')
            ->willReturn($user);

        $this->instance(
            UserRepositoryInterface::class,
            $rep_user_mock
        );

        $data = ['token' => $coupon->token];

        $response = $this->actingAs($user)
            ->post(route('ajax.coupon.activate'), $data)
            ->assertNotFound();
    }
}
