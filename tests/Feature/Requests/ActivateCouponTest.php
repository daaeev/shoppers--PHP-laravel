<?php

namespace Tests\Feature\Requests;

use App\Http\Requests\ajax\ActivateCoupon;
use App\Models\Coupon;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Tests\TestCase;

class ActivateCouponTest extends TestCase
{
    use RefreshDatabase;

    protected string $route = '/check-activate-coupon-validation-route';

    public function setUp(): void
    {
        parent::setUp();

        Route::post($this->route, function (ActivateCoupon $validate) {
            return true;
        });

        $this->user = User::factory()->createOne();
    }

    public function testSuccessData()
    {
        $coupon = Coupon::factory()->createOne();

        $response = $this->actingAs($this->user)->post($this->route, [
            'token' => $coupon->token,
        ])->assertOk();

        $response->assertSessionHasNoErrors();
    }

    public function testIfNotAuth()
    {
        $response = $this->post($this->route)->assertUnauthorized();

        $response->assertSessionHasNoErrors();
    }


    /**
     * @dataProvider failedData
     */
    public function testFailedData($token)
    {
        $this->actingAs($this->user)->post($this->route, [
            'token' => $token,
        ])->assertNotFound();
    }

    public function failedData()
    {
        return [
            [null],
            [Str::random(31)],
            ['token']
        ];
    }
}
