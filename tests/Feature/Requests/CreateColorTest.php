<?php

namespace Tests\Feature\Requests;

use App\Http\Requests\CreateColor;
use App\Models\Color;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Tests\TestCase;

class CreateColorTest extends TestCase
{
    use RefreshDatabase;

    protected string $route = '/check-create-color-validation-route';

    public function setUp(): void
    {
        parent::setUp();

        Route::post($this->route, function (CreateColor $validate) {
            return true;
        });
    }

    public function testSuccessData()
    {
        $name = Str::random();
        $hex = '#000000';

        $response = $this->post($this->route, [
            'name' => $name,
            'hex' => $hex,
        ])->assertOk();

        $response->assertSessionHasNoErrors();
    }

    public function testFailedData()
    {
        $data = $this->failedData();

        foreach ($data as list($name, $hex)) {
            $response = $this->post($this->route, [
                'name' => $name,
                'hex' => $hex,
            ])->assertRedirect(route('home'));

            $response->assertSessionHasErrors();
        }
    }

    protected function failedData()
    {
        $col = Color::factory()->createOne();

        return [
            [null, $col->hex],
            [$col->name, $col->hex],
            [Str::random(256), $col->hex],
            [$col->name, null],
            [$col->name, $col->hex],
            [$col->name, Str::random(11)],
        ];
    }
}
