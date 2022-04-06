<?php

namespace Tests\Feature\Requests;

use App\Http\Requests\CreateCoupon;
use App\Models\Coupon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Tests\TestCase;

class CreateCouponTest extends TestCase
{
    use RefreshDatabase;

    protected string $route = '/create-coupon-validation-route';

    public function setUp(): void
    {
        parent::setUp();

        Route::post($this->route, function (CreateCoupon $validate) {
            return true;
        });
    }

    public function testSuccessData()
    {
        $response = $this->post($this->route, [
            'percent' => 70,
            'token' => Str::random()
        ])->assertOk();

        $response->assertSessionHasNoErrors();
    }

    public function testFailedData()
    {
        $data = $this->getFailedData();

        foreach ($data as $attributes) {
            $response = $this->post($this->route, $attributes)->assertRedirect(route('home'));

            $response->assertSessionHasErrors();
        }
    }

    protected function getFailedData()
    {
        $coupon = Coupon::factory()->createOne();

        return [
            [
                'percent' => null,
                'token' => 'string'
            ],
            [
                'percent' => 1.2,
                'token' => 'string'
            ],
            [
                'percent' => 0,
                'token' => 'string'
            ],
            [
                'percent' => -1,
                'token' => 'string'
            ],
            [
                'percent' => 101,
                'token' => 'string'
            ],
            [
                'percent' => 25,
                'token' => null
            ],
            [
                'percent' => 25,
                'token' => Str::random(31)
            ],
            [
                'percent' => 25,
                'token' => $coupon->token
            ],
        ];
    }
}
