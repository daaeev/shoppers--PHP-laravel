<?php

namespace Tests\Feature\Requests;

use App\Http\Requests\DeleteCoupon;
use App\Models\Coupon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class DeleteCouponTest extends TestCase
{
    use RefreshDatabase;

    protected string $route = '/delete-coupon-validation-route';

    public function setUp(): void
    {
        parent::setUp();

        Route::post($this->route, function (DeleteCoupon $validate) {
            return true;
        });
    }

    public function testSuccessData()
    {
        $coupon = Coupon::factory()->createOne();

        $response = $this->post($this->route, [
            'id' => $coupon->id
        ])->assertOk();

        $response->assertSessionHasNoErrors();
    }

    /**
     * @dataProvider failedData
     */
    public function testFailedData($id)
    {
        $response = $this->post($this->route, [
            'id' => $id
        ])->assertRedirect(route('home'));

        $response->assertSessionHasErrors();
    }

    public function failedData()
    {
        return [
            [null],
            [1.2],
            [-1],
            [123]
        ];
    }
}
